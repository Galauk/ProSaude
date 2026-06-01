<?php

include("db_class/Procedimentos.php");
include_once("db_class/EsusHistoricoItens.php");
class ThriftProcedimento {

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
		$bdProcedimentos = new BancoProcedimentos();
                $bdEsusHistoricoItens = new EsusHistoricoItens();
		$dadosProcedimentos = $bdProcedimentos->getDadosProcedimentos();

		$numAtend = $bdProcedimentos->getCountDadosProcedimentos();
		if ($numAtend > 0) {
			//Efetuando leitura de dados
			foreach($dadosProcedimentos as $row) {
				//GERAÇÃO DO ARQUIVO
				$dadosThrift = new ThriftProcedimento();
				$uuid = $dadosThrift->getGUID();
        if(empty($uuid)){
          file_put_contents('../uuid_'.date("Y-m-d").'.log', print_r($row, true), FILE_APPEND);
        }
				// Passo 1: criar e preencher o thrift do atendimento;
				$ficha_master = $this->montaArrayFichaProcedimentoMaster($uuid,$row);
				// Passo 2: serializar o thrift do atendimento;
				$ficha_master_serializado = $this->serialize_thrift($ficha_master);
				// Passo 3: coletar as informações do envio e das instalações, preencher o thrift de transporte com as informações coletadas;
				$array_envio = $this->montaArrayEnvioDto($ficha_master_serializado,$uuid,$row);
				// serializa todas informações de envio mais os bytes serializos...
				$array_envio_serializado = $this->serialize_thrift($array_envio);
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$eeh_codigo);
                                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,3);
				// Passo 5: atualiza status da ficha
				$bdProcedimentos->atualizaStatus($uuid,$row["age_codigo"]);
			}
		}
	}

	public function getQtd(){
		$bdProcedimentos = new BancoProcedimentos();
		$numAtend = $bdProcedimentos->getCountDadosProcedimentos();
		return $numAtend;
	}

	public function zipWriter($array_envio_serializado,$uuid,$eeh_codigo){
		// Define nome do arquivo
		$arquivo = $uuid.".esus13";
		// Abre arquivo
		$fp = fopen("./arqs/".$arquivo, 'w');
		// Escreve no arquivo
		fwrite($fp,$array_envio_serializado);
		// Fecha o arquivo
		fclose($fp);
        // Cria ou abre uma pasta compactada
		$zip = new ZipArchive;
		$zip->open('./arqs/RAS_FileZip_'.$eeh_codigo.'.zip', ZipArchive::CREATE);
		// Adiciona um arquivo ࡰasta
		$zip->addFile("./arqs/".$arquivo,$arquivo);
		// Fecha a pasta e salva o arquivo
		$zip->close();
		// Deleta arquivo adiciona a pasta
		unlink("./arqs/".$arquivo);
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
			"tipoDadoSerializado" => $row['efp_tipo_dado_serializado'],
			"cnesDadoSerializado" =>  $row['efp_cnes'] ? $row['efp_cnes'] : '',
			"codIbge" => $row['efp_codigo_ibge_mun'],
			"ineDadoSerializado" => $row['efp_ine'],
			"dadoSerializado" => $ficha_master_serializado,
			"remetente" => $this->getInfoInstal($remetente),
			"originadora"  => $this->getInfoInstal($originadora),
			"versao" => $this->getVersao($array_versao)
		);

        return $this->getTransporteThrift($array_envio_dto);

    }

    public function montaArrayFichaProcedimentoMaster($uuid,$row){
    	// Pega os dados do profissional e do atendimento e coloca em array, para ser transformado em um objeto do E-SUS
    	// echo "<pre>";print_r($row);die();
    	$array_unica = $this->montaArrayUnica($row);
    	// Pega os dados do paciente, atendimento e procedimentos realizados e coloca em array
        $array_atend_proc = array($this->montaArrayAtendProcedimentos($row));
        // Pega mais alguns dados do atendimento, os array especificados acima e coloca tudo dentro de um �nico array
        $array_dto = array(
			"uuidFicha"=>$uuid,
			"tpCdsOrigem" => "3",
			"headerTransport" => $this->getUnicaLotacao($array_unica),
			"atendProcedimentos" => $array_atend_proc,
			"numTotalAfericaoPa" => $row["numtotalafericaopa"],
			"numTotalGlicemiaCapilar" => $row["numtotalglicemiacapilar"],
			"numTotalAfericaoTemperatura" => $row["numtotalafericaotemperatura"],
			"numTotalMedicaoAltura" => $row["numtotalmedicaoalturapeso"],
			"numTotalCurativoSimples" => $row["numtotalcurativo"],
			"numTotalMedicaoPeso" => $row["numtotalmedicaoalturapeso"],
			"numTotalColetaMaterialParaExameLaboratorial" => $row["numtotalexame"],
			"turno" => $row["turno"]
		); //23505
        // echo "<pre>";print_r($array_dto);die();
        // Transforma o array em um objeto do E-SUS e retorna
        $ficha_master = $this->getFichaProcedimentoMasterThrift($array_dto);
        return $ficha_master;
    }

	public function getDadosOriginadoraRemetente(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql);
		$row = pg_fetch_array($query);
		return $row;
	}

    public function montaArrayUnica($row){
        $array_unica = array(
			"profissionalCNS"=> ($this->validaCnsBanco(trim($row['efp_profissional_cns']))==true ? trim($row['efp_profissional_cns']) : NULL),
			"cboCodigo_2002"=> $row['efp_cbo_codigo_2002'],
			"cnes"=> $row['efp_cnes'],
			"ine"=> $row['efp_ine'],
			"dataAtendimento"=> ((strtotime($row['efp_dtatendimento'])) + 86400) * 1000 ,
			"codigoIbgeMunicipio"=> $row['efp_codigo_ibge_mun']
		);
		return $array_unica;
    }

	public function montaArrayAtendProcedimentos($row){
		$array_atend_proced = array(
			"numProntuario" => $row['efp_numprontuario'],
			"numCartaoSus" => ($this->validaCnsBanco(trim($row['efp_num_cartao_sus']))==true ? trim($row['efp_num_cartao_sus']) : NULL),
			"dtNascimento" => ((strtotime($row['efp_dtnascimento'])) + 86400) * 1000 ,
			"sexo" => $row['efp_sexo'],
			"localAtendimento" => $row['co_local_atend']." L",
			//"turno" => "",
			"procedimentos" => array(),
			"outrosSiaProcedimentos" => $this->listaProcedimentosSigtap($row['age_codigo'])
		);
		return $this->getAtendProc($array_atend_proced);
	}

	public function listaProcedimentosSigtap($ageCodigo){
		$bdProcedimentos = new BancoProcedimentos();
		$dadosProcedimentosSigtap = $bdProcedimentos->getProcedimentosSigtap($ageCodigo);
		$procsRea = array();
		foreach($dadosProcedimentosSigtap as $rowSql) {
			array_push($procsRea, $rowSql["proc_codigo_sus"]);
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

    // Serializa objeto do Thrift
    public function serialize_thrift($ficha_master = FALSE) {
       //echo "<pre>".print_r($ficha_master,1); die();
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($ficha_master);
    }

    // Transforma o array em um objeto do E-SUS
    public function getUnicaLotacao($vals){
        $unica = new br\gov\saude\esus\cds\transport\generated\thrift\common\UnicaLotacaoHeaderThrift($vals);
        return $unica;
    }

    public function getAtendProc($vals){
        $atend_proc = new \br\gov\saude\esus\cds\transport\generated\thrift\procedimento\FichaProcedimentoChildThrift($vals);
        return $atend_proc;
    }

    // Transforma o array em um objeto do E-SUS
    public function getFichaProcedimentoMasterThrift($vals){
        $ficha_master = new \br\gov\saude\esus\cds\transport\generated\thrift\procedimento\FichaProcedimentoMasterThrift($vals);
        return $ficha_master;
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