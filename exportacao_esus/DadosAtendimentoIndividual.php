<?php

include("db_class/AtendimentoIndividual.php");
include("db_class/EsusHistoricoItens.php");

class ThriftAteIndividual {

    private function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = md5(uniqid(rand(), true));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }

    public function executeMain($log) {
		$bdAteIndividual = new BancoAtendimentoIndividual();
        $bdEsusHistoricoItens = new EsusHistoricoItens();
		$dadosAteIndividual = $bdAteIndividual->getDados();
		$numAtend = $bdAteIndividual->getQtdRegistros();
		$cab = "Total Atendimento Indivual: ".$numAtend."\n";
		log_exportacao($cab);
		$controle=0;
		if ($numAtend > 0) {
			foreach($dadosAteIndividual as $row) {
				$controle++;
				$info = "Atendimento Individual: ".$controle." Serial:" .$uuid."\n";
				log_exportacao($info);
				$dadosThrift = new ThriftAteIndividual();
				$uuid = $dadosThrift->getGUID();
				// Passo 1: criar e preencher o thrift do atendimento;
				$ficha_master = $this->fichaAtendimentoIndividualMasterThrift($uuid,$row);
				//echo "<pre>".print_r($ficha_master,1); die();
				// Passo 2: serializar o thrift do atendimento;
				$ficha_master_serializado = $this->serialize_thrift($ficha_master);
				// Passo 3: coletar as informa��es do envio e das instala��es, preencher o thrift de transporte com as informa��es coletadas;
				$array_envio = $this->montaArrayEnvioDto($ficha_master_serializado,$uuid,$row);
				$array_envio_serializado = $this->serialize_thrift($array_envio); // serializa todas informaões de envio mais os bytes serializos...
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$log);
                                $bdEsusHistoricoItens->registratHistoricoItens($log,$uuid,4);
				// Passo 5: Atualiza status da ficha
				$bdAteIndividual->atualizaStatus($uuid,$row["ate_codigo"]);
				echo "+";
			}
		}
        pg_query("COMMIT;");
	}

	public function zipWriter($array_envio_serializado,$uuid,$eeh){
		// Define nome do arquivo
		$arquivo = $uuid.".esus";
		// Abre arquivo
		$fp = fopen("./arqs/".$arquivo, 'w');
		// Escreve no arquivo
		fwrite($fp,$array_envio_serializado);
		// Fecha o arquivo
		fclose($fp);
		// Cria ou abre uma pasta compactada
		$zip = new ZipArchive;
		$zip->open('./arqs/RAS_FileZip_'.$eeh.'.zip', ZipArchive::CREATE);
		// Adiciona um arquivo � pasta
		$zip->addFile("./arqs/".$arquivo,$arquivo);
		// Fecha a pasta e salva o arquivo
		$zip->close();
		// Deleta arquivo adiciona a pasta
		unlink("./arqs/".$arquivo);
	}

	public function fichaAtendimentoIndividualMasterThrift($uuid,$row){
    	// Pega os dados do profissional e do atendimento e coloca em array, para ser transformado em um objeto do E-SUS
    	$variasLotacoes = $this->variasLotacoes($row);
    	// Pega os dados do paciente, atendimento e procedimentos realizados e coloca em array
        $atendimentosIndividuais = array($this->fichaAtendimentoIndividualChildThrift($row));
        // Pega mais alguns dados do atendimento, os array especificados acima e coloca tudo dentro de um �nico array
        $array_dto = array(
			"uuidFicha"=>$uuid,
			"headerTransport" => $this->getVariasLotacao($variasLotacoes),
			"atendimentosIndividuais" => $atendimentosIndividuais,
			"tpCdsOrigem" => "3"
		);
        // Transforma o array em um objeto do E-SUS e retorna
        $ficha_master = $this->getFichaAtendimentoIndividualMasterThrift($array_dto);
        return $ficha_master;
    }

	public function variasLotacoes($row){
		$headerTransport = array(
			"profissionalCNS"=> ($this->validaCnsBanco(trim($row['eai_profissional_cns']))==true ? trim($row['eai_profissional_cns']) : NULL),
			"cboCodigo_2002"=> $row['eai_cbo_codigo_2002'],
			"cnes"=> $row['eai_cnes'],
			"ine" => $row['nu_ine'],
			"dataAtendimento"=> ((strtotime($row['eai_dtatendimento'])) + 86400) * 1000 ,
			"codigoIbgeMunicipio"=> $row['eai_codigo_ibge_mun']
		);
		$variasLotacoes = array(
			"lotacaoForm" => $this->getUnicaLotacao($headerTransport)
		);
        return $variasLotacoes;
    }

	public function fichaAtendimentoIndividualChildThrift($row){
		$nasf = array($row['ate_nasf_aval'],$row['ate_nasf_proc'],$row['ate_nasf_presc']);
		$array_atend_individual = array(
			"dataNascimento" => ((strtotime($row['eai_dtnascimento'])) + 86400) * 1000 ,
			"localDeAtendimento" => $row['co_local_atend']." L",
			"tipoAtendimento" => $row['eai_tipo_atendimento']." L",
			"problemaCondicaoAvaliada" => $this->problemaCondicaoAvaliacaoAIThrift($row["ate_codigo"]),
			"condutas" => $this->getCondutas($row["ate_codigo"]),
			"outrosSia" => $this->outrosSiaThrift($row["ate_codigo"]),
			"cns" => ($this->validaCnsBanco(trim($row['eai_num_cartao_sus']))==true ? trim($row['eai_num_cartao_sus']) : NULL),
			"numeroProntuario" => $row['eai_numprontuario'],
			"sexo" => $row['eai_sexo'],
			"turno" => $row['turno'],
			"pesoAcompanhamentoNutricional" => $row['vd_peso'],
			"alturaAcompanhamentoNutricional" => $row['vd_altura'],
			"aleitamentoMaterno" => $row['aleitamento_materno'],
			"dumDaGestante" => $row['dum'],
			"idadeGestacional" => $row['idade_gestacional'],
			"atencaoDomiciliarModalidade" => $row['usu_ate_dom_mod'],
			"vacinaEmDia" => $row['vacinacao_em_dia'],
			"ficouEmObservacao" => false,
			"nasfs" => $nasf,
			"stGravidezPlanejada" => $row['gravidez_planejada'],
			"nuGestasPrevias" => $row['gestas_previas'],			
			"nuPartos" => $row['partos'],
			"racionalidadeSaude" => $row['ate_rac_saude'],
			"perimetroCefalico" => $row['ate_perimetro_cefalico']

			/*"cid10" => $row[''],
			"cid10_2" => $row[''],
			"examesSolicitados" => $row[''],
			"examesAvaliados" => $row[''],*/
		);
		return $this->getFichaAtendimentoIndividualChildThrift($array_atend_individual);
	}

	public function problemaCondicaoAvaliacaoAIThrift($ateCodigo){
		$dadosCiaps = $this->getCiaps($ateCodigo);
		$dadosOutroCiaps = $this->getCiapsOutros($ateCodigo);
		$dadosCondicao = array(
			"ciaps" => $dadosCiaps,
			"outroCiap1" => (count($dadosOutroCiaps) > 0 ? trim($dadosOutroCiaps[0]) : null),
			"outroCiap2" => (count($dadosOutroCiaps) > 0 ? trim($dadosOutroCiaps[1]) : null)
		);
		return $this->getProblemaCondicaoAvaliacaoAIThrift($dadosCondicao);
	}

	public function getCiaps($ateCodigo){
		// Array Padr�o Ciaps
		$arrayCiaps = array("ABP009","ABP019","ABP008","ABP006","ABP010","ABP020","ABP018","ABP005","ABP007","ABP001","ABP004","ABP002","ABP023","ABP022","ABP024","ABP015","ABP014","ABP003","ABP011","ABP017","ABP012","ABP013");
		// Buscando ciaps informados
		$bdAteIndividual = new BancoAtendimentoIndividual();
		$dadosCiaps = $bdAteIndividual->getCiaps($ateCodigo);
		$ciaps = array();
		foreach($dadosCiaps as $row) {
			// Verificando se cont�m valor dentro do array de Ciaps Padr�o, se conter inclu�
			if (in_array($row["co_ciap"], $arrayCiaps)) {
				array_push($ciaps, $row["co_ciap"]);
			}
		}
		return $ciaps;
	}

	public function getCiapsOutros($ateCodigo){
		// Array Padr�o Ciaps
		$arrayCiaps = array("ABP009","ABP019","ABP008","ABP006","ABP010","ABP020","ABP018","ABP005","ABP007","ABP001","ABP004","ABP002","ABP023","ABP022","ABP024","ABP015","ABP014","ABP003","ABP011","ABP017","ABP012","ABP013");
		// Buscando ciaps informados
		$bdAteIndividual = new BancoAtendimentoIndividual();
		$dadosCiaps = $bdAteIndividual->getCiaps($ateCodigo);
		$ciaps = array();
		foreach($dadosCiaps as $row) {
			// Verificando se cont�m valor dentro do array de Ciaps Padr�o, se conter inclu�
			if (!in_array($row["co_ciap"], $arrayCiaps)) {
				array_push($ciaps, $row["co_ciap"]);
			}
		}
		return $ciaps;
	}

	public function getCondutas($ateCodigo){
		$bdAteIndividual = new BancoAtendimentoIndividual();
		$dadosConduta = $bdAteIndividual->getCondutas($ateCodigo);
		$conduta = array();
		foreach($dadosConduta as $row) {
			array_push($conduta, $row["co_cds_tipo_conduta"]." L");
		}
		return $conduta;
	}

	public function outrosSiaThrift($ateCodigo){
		$bdAteIndividual = new BancoAtendimentoIndividual();
		$dadosProcedimentosSigtap = $bdAteIndividual->getExames($ateCodigo);
		$arrayGeral = array();
		foreach($dadosProcedimentosSigtap as $val) {
			$array = array(
				"codigoExame" => $val["proc_codigo_sus"],
				"solicitadoAvaliado" => array("S")
			);
			$arrayConv = $this->getOutrosSiaThrift($array);
			array_push($arrayGeral,$arrayConv);
		}
		return (count($arrayGeral) > 0 ? $arrayGeral : array());
	}

	public function montaArrayEnvioDto($ficha_master_serializado,$uuid,$row){

		$dadosRO = $this->getDadosOriginadoraRemetente();

		$remetente = array(
			"contraChave" => ($dadosRO["ero_contra_chave"] != "" ? $dadosRO["ero_contra_chave"] : ""),
			"uuidInstalacao" => $uuid,
			"cpfOuCnpj" => $dadosRO["ero_cpf_cnpj"],
			"nomeOuRazaoSocial" => $dadosRO["ero_nome_razao"],
			"fone" => $dadosRO["ero_fone"],
			"email" => $dadosRO["ero_email"]
		);

		$originadora = array(
			"contraChave" => ($dadosRO["ero_contra_chave"] != "" ? $dadosRO["ero_contra_chave"] : ""),
			"uuidInstalacao" => $uuid,
			"cpfOuCnpj" => $dadosRO["ero_cpf_cnpj"],
			"nomeOuRazaoSocial" => $dadosRO["ero_nome_razao"],
			"fone" => $dadosRO["ero_fone"],
			"email" => $dadosRO["ero_email"]
		);

		$array_versao = array(
			"major" => "1",
            "minor" => "3",
            "revision" =>"0"
		);

		$array_envio_dto = array(
			"uuidDadoSerializado" => $uuid,
			"tipoDadoSerializado" => $row['eai_tipo_dado_serializado'],
			"cnesDadoSerializado" => $row['eai_cnes'] ? $row['eai_cnes'] : '',
			"codIbge" => $row['eai_codigo_ibge_mun'],
			"dadoSerializado" => $ficha_master_serializado,
			"remetente" => $this->getInfoInstal($remetente),
			"originadora"  => $this->getInfoInstal($originadora),
			"versao" => $this->getVersao($array_versao)
		);

        return $this->getTransporteThrift($array_envio_dto);
    }

	public function validaCnsBanco($cns){
		if ((substr($cns,0,1)!="7") && (substr($cns,0,1)!="8") && (substr($cns,0,1)!="9")){
			return $this->validaCNS($cns);
		}else{
			return $this->validaCNS_PROVISORIO($cns);
		}
	}

	public function validaCNS($cns) {
		if ((strlen(trim($cns))) != 15) {
			return false;
		}
		$pis = substr($cns,0,11);
		$soma = (((substr($pis, 0,1)) * 15) +
		         ((substr($pis, 1,1)) * 14) +
			     ((substr($pis, 2,1)) * 13) +
			     ((substr($pis, 3,1)) * 12) +
			     ((substr($pis, 4,1)) * 11) +
			     ((substr($pis, 5,1)) * 10) +
			     ((substr($pis, 6,1)) * 9) +
			     ((substr($pis, 7,1)) * 8) +
			     ((substr($pis, 8,1)) * 7) +
			     ((substr($pis, 9,1)) * 6) +
			     ((substr($pis, 10,1)) * 5));
		$resto = fmod($soma, 11);
		$dv = 11  - $resto;
		if ($dv == 11) {
			$dv = 0;
		}
		if ($dv == 10) {
			$soma = ((((substr($pis, 0,1)) * 15) +
		              ((substr($pis, 1,1)) * 14) +
			          ((substr($pis, 2,1)) * 13) +
			          ((substr($pis, 3,1)) * 12) +
			          ((substr($pis, 4,1)) * 11) +
			          ((substr($pis, 5,1)) * 10) +
			          ((substr($pis, 6,1)) * 9) +
			          ((substr($pis, 7,1)) * 8) +
			          ((substr($pis, 8,1)) * 7) +
			          ((substr($pis, 9,1)) * 6) +
			          ((substr($pis, 10,1)) * 5)) + 2);
			$resto = fmod($soma, 11);
			$dv = 11  - $resto;
			$resultado = $pis."001".$dv;
		} else {
			$resultado = $pis."000".$dv;
		}
		if ($cns != $resultado){
            return false;
        } else {
        	return true;
		}
	}

	public function validaCNS_PROVISORIO($cns) {
		if ((strlen(trim($cns))) != 15) {
			return false;
		}
		$soma = (((substr($cns,0,1)) * 15) +
		((substr($cns,1,1)) * 14) +
		((substr($cns,2,1)) * 13) +
		((substr($cns,3,1)) * 12) +
		((substr($cns,4,1)) * 11) +
		((substr($cns,5,1)) * 10) +
		((substr($cns,6,1)) * 9) +
		((substr($cns,7,1)) * 8) +
		((substr($cns,8,1)) * 7) +
		((substr($cns,9,1)) * 6) +
		((substr($cns,10,1)) * 5) +
		((substr($cns,11,1)) * 4) +
		((substr($cns,12,1)) * 3) +
		((substr($cns,13,1)) * 2) +
		((substr($cns,14,1)) * 1));
		$resto = fmod($soma,11);
		if ($resto != 0) {
			return false;
		} else {
			return true;
		}
	}

	// Transforma o array em um objeto do E-SUS
    public function getUnicaLotacao($vals){
        $unica = new br\gov\saude\esus\cds\transport\generated\thrift\common\UnicaLotacaoHeaderThrift($vals);
        return $unica;
    }

	// Transforma o array em um objeto do E-SUS
    public function getVariasLotacao($vals){
        $unica = new br\gov\saude\esus\cds\transport\generated\thrift\common\VariasLotacoesHeaderThrift($vals);
        return $unica;
    }

	public function getProblemaCondicaoAvaliacaoAIThrift($vals){
        $atend_proc = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadeindividual\ProblemaCondicaoAvaliacaoAIThrift($vals);
        return $atend_proc;
    }

	public function getFichaAtendimentoIndividualChildThrift($vals){
        $atend_proc = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadeindividual\FichaAtendimentoIndividualChildThrift($vals);
        return $atend_proc;
    }

	// Transforma o array em um objeto do E-SUS
    public function getFichaAtendimentoIndividualMasterThrift($vals){
        $ficha_master = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadeindividual\FichaAtendimentoIndividualMasterThrift($vals);
        return $ficha_master;
    }

	// Transforma o array em um objeto do E-SUS
    public function getOutrosSiaThrift($vals){
        $ficha_master = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadeindividual\OutrosSiaThrift($vals);
        return $ficha_master;
    }

	// Serializa objeto do Thrift
    public function serialize_thrift($ficha_master = FALSE) {
       //echo "<pre>".print_r($ficha_master,1); die();
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($ficha_master);
    }

	public function getDadosOriginadoraRemetente(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql);
		$row = pg_fetch_array($query);
		return $row;
	}

	// Transforma o array em um objeto do E-SUS
    public function getInfoInstal($vals){
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoInstalacaoThrift($vals);
    }

	// Transforma o array em um objeto do E-SUS
    public function getVersao($vals){
        return new \br\gov\saude\esus\transport\common\api\configuracaodestino\VersaoThrift($vals);
    }

	// Transforma o array em um objeto do E-SUS
    public function getTransporteThrift($vals){
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoTransporteThrift($vals);
    }

}