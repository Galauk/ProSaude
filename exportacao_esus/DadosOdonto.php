<?php

include("db_class/Odontologico.php");
include_once("db_class/EsusHistoricoItens.php");

class ThriftOdonto {

    private function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
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

    public function executeMain($eeh_codigo) {
		$bdOdonto = new BancoOdonto();
                $bdEsusHistoricoItens = new EsusHistoricoItens();
		$dadosOdonto = $bdOdonto->getDados();
		$numAtend = $bdOdonto->getQtdRegistros();
		$cab = "Total Atendimento Odontologico: ".$numAtend."\n";
		log_exportacao($cab);
		$controle=0;		
		if ($numAtend > 0) {
			foreach($dadosOdonto as $row) {
                $controle++;
				$dadosThrift = new ThriftOdonto();
				$uuid = $dadosThrift->getGUID();
                echo '+';
                $info = "Atendimento Odontologico: ".$controle." Serial:" .$uuid."\n";
                log_exportacao($info);
                //GERAÇÃO DO ARQUIVO
                echo $info;
                if(empty($uuid)){
                    file_put_contents('../uuid_'.date("Y-m-d").'.log', print_r($row, true), FILE_APPEND);
                }
				// Passo 1: criar e preencher o thrift do atendimento;
				$ficha_master = $this->fichaAtendimentoOdontologicoMasterThrift($uuid,$row);
				// Passo 2: serializar o thrift do atendimento;
				$ficha_master_serializado = $this->serialize_thrift($ficha_master);
				// Passo 3: coletar as informa��es do envio e das instala��es, preencher o thrift de transporte com as informa��es coletadas;
				$array_envio = $this->montaArrayEnvioDto($ficha_master_serializado,$uuid,$row);
				// serializa todas informa��es de envio mais os bytes serializos...
				$array_envio_serializado = $this->serialize_thrift($array_envio);
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$eeh_codigo);
                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,7);
				// Passo 5: atualiza status da ficha
				$bdOdonto->atualizaStatus($uuid,$row["odo_pcon_codigo"]);
			}
		}
	}

	public function zipWriter($array_envio_serializado,$uuid,$eeh_codigo){
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
		$zip->open('./arqs/RAS_FileZip_'.$eeh_codigo.'.zip', ZipArchive::CREATE);
		// Adiciona um arquivo � pasta
		$zip->addFile("./arqs/".$arquivo,$arquivo);
		// Fecha a pasta e salva o arquivo
		$zip->close();
		// Deleta arquivo adiciona a pasta
		unlink("./arqs/".$arquivo);
	}

	public function fichaAtendimentoOdontologicoMasterThrift($uuid,$row){
    	// Pega os dados do profissional e do atendimento e coloca em array, para ser transformado em um objeto do E-SUS
    	$variasLotacoes = $this->variasLotacoes($row);
    	// Pega os dados do paciente, atendimento e procedimentos realizados e coloca em array
        $atendimentosOdontologicos = array($this->fichaAtendimentoOdontologicoChildThrift($row));
        // Pega mais alguns dados do atendimento, os array especificados acima e coloca tudo dentro de um �nico array
        $array_dto = array(
			"uuidFicha"=>$uuid,
			"headerTransport" => $this->getVariasLotacao($variasLotacoes),
			"atendimentosOdontologicos" => $atendimentosOdontologicos,
			"tpCdsOrigem" => "3"
		);
        
        // Transforma o array em um objeto do E-SUS e retorna
        $ficha_master = $this->getFichaAtendimentoOdontologicoMasterThrift($array_dto);
        return $ficha_master;
    }

	public function variasLotacoes($row){
		$headerTransport = array(
			"profissionalCNS"=> ($this->validaCnsBanco(trim($row['eo_profissional_cns']))==true ? trim($row['eo_profissional_cns']) : NULL),
			"cboCodigo_2002"=> $row['eo_cbo_codigo_2002'],
			"cnes"=> $row['eo_cnes'],
			"ine"=> $row['eo_ine'],
			"dataAtendimento"=> ((strtotime($row['eo_dtatendimento'])) + 86400) * 1000 ,
			"codigoIbgeMunicipio"=> $row['eo_codigo_ibge_mun']
        );
        
		$variasLotacoes = array(
			"lotacaoForm" => $this->getUnicaLotacao($headerTransport)
		);
        
        return $variasLotacoes;
    }

	public function fichaAtendimentoOdontologicoChildThrift($row){
        // echo "<pre>"; print_r($row); die();
		$array_atend_odonto = array(
			"dtNascimento" => ((strtotime($row['eo_dtnascimento'])) + 86400) * 1000 ,
			"cnsCidadao" => ($this->validaCnsBanco(trim($row['eo_num_cartao_sus']))==true ? trim($row['eo_num_cartao_sus']) : NULL),
			"numProntuario" => $row['eo_numprontuario'],
			"localAtendimento" => $row['co_local_atend'],
			"tipoAtendimento" => $row['eo_tipo_atendimento'] == 99 ? array(2) : $row['eo_tipo_atendimento'],
			"tiposEncamOdonto" => $this->getCondutaEncaminhamento($row['odo_pcon_codigo']),
			"tiposVigilanciaSaudeBucal" => $this->getVigilanciaSaudeBucal($row['odo_pcon_codigo']),
			"tiposConsultaOdonto" => ($row['eo_tipo_consulta'] != "" ? array($row['eo_tipo_consulta']) : array(4)),
			"procedimentosRealizados" => array(),
			"coMsProcedimento" => $this->listaProcedimentosSigtap($row['odo_pcon_codigo']),
            "sexo" => $row["eo_sexo"],
            "gestante" => ($row['usu_esta_gestante'] != "" ? $row['usu_esta_gestante'] : 'false'),
            "turno" => $row['eo_turno'] != "" ? $row['eo_turno'] : 1,
            "quantidade" => $row['eo_proc_quantidade'] != NULL ? $row['eo_proc_quantidade'] : 0
		);
        
        // print_r($array_atend_odonto);
        //die(strtotime("2015-12-15") . "------" . strtotime("15/12/2015") . "---------" . strtotime("15-12-2015"));
		return $this->getFichaAtendimentoOdontologicoChildThrift($array_atend_odonto);
	}

	public function listaProcedimentosSigtap($odoPconCodigo){
		$bdOdonto = new BancoOdonto();
		$dadosProcedimentosSigtap = $bdOdonto->getProcedimentos($odoPconCodigo);
		$arrayGeral = array();
		foreach($dadosProcedimentosSigtap as $val) {
			$array = array(
				"coMsProcedimento" => $val["proc_codigo_sus"] != NULL ? $val["proc_codigo_sus"] : "ABPO015",
				"quantidade" => "1"
			);
			$arrayConv = $this->getProcedimentoQuantidadeThrift($array);
			array_push($arrayGeral,$arrayConv);
		}
		return $arrayGeral;
	}

	public function getCondutaEncaminhamento($odoPconCodigo){
		$bdOdonto = new BancoOdonto();
		$dadosEncaminhamento = $bdOdonto->getCondutaEncaminhamento($odoPconCodigo);
		$procsRea = array();
		foreach($dadosEncaminhamento as $rowSql) {
			array_push($procsRea, $rowSql["tp_cds_encam_odonto"] == 99 ? 11 : $rowSql["tp_cds_encam_odonto"]);
		}
		return $procsRea;
	}

	public function getVigilanciaSaudeBucal($odoPconCodigo){
		$bdOdonto = new BancoOdonto();
		$dadosVigilancia = $bdOdonto->getVigilanciaSaudeBucal($odoPconCodigo);
		$procsRea = array();
		foreach($dadosVigilancia as $rowSql) {
			array_push($procsRea, $rowSql["tp_cds_vig_saude_bucal"] == 99 ? array(11) : $rowSql["tp_cds_vig_saude_bucal"]);
		}
		return $procsRea;
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

	public function getFichaAtendimentoOdontologicoChildThrift($vals){
        $atend_proc = new \br\gov\saude\esus\cds\transport\generated\thrift\atendimentoodontologico\fichaAtendimentoOdontologicoChildThrift($vals);
        return $atend_proc;
    }

	// Transforma o array em um objeto do E-SUS
    public function getFichaAtendimentoOdontologicoMasterThrift($vals){
        $ficha_master = new \br\gov\saude\esus\cds\transport\generated\thrift\atendimentoodontologico\FichaAtendimentoOdontologicoMasterThrift($vals);
        return $ficha_master;
    }

	// Transforma o array em um objeto do E-SUS
    public function getProcedimentoQuantidadeThrift($vals){
        $ficha_master = new \br\gov\saude\esus\cds\transport\generated\thrift\atendimentoodontologico\ProcedimentoQuantidadeThrift($vals);
        return $ficha_master;
    }

	// Serializa objeto do Thrift
    public function serialize_thrift($ficha_master = FALSE) {
       //echo "<pre>".print_r($ficha_master,1); die();
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($ficha_master);
    }

	private function montaArrayEnvioDto($ficha_master_serializado,$uuid,$row){

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
			"tipoDadoSerializado" => $row['eo_tipo_dado_serializado'],
			"cnesDadoSerializado" => $row['eo_cnes'],
			"codIbge" => $row['eo_codigo_ibge_mun'],
			"dadoSerializado" => $ficha_master_serializado,
			"remetente" => $this->getInfoInstal($remetente),
			"originadora"  => $this->getInfoInstal($originadora),
			"versao" => $this->getVersao($array_versao)
		);

        return $this->getTransporteThrift($array_envio_dto);

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