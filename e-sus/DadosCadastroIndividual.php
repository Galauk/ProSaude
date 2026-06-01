<?php

include "db_class/CadastroIndividual.php";
include_once("db_class/EsusHistoricoItens.php");

class ThriftCadastroIndividual {

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

    public function executeMain($eeh_codigo=FALSE) {
        $bdCadastroIndividual = new BancoCadastroIndividual();
        $bdEsusHistoricoItens = new EsusHistoricoItens();

		$dadosCadastroIndividual = $bdCadastroIndividual->getDados();
       	
       	// die("TESTE");

		$numAtend = $bdCadastroIndividual->getQtdRegistros();
		if ($numAtend > 0) {
                   // echo "<pre>".print_r($dadosCadastroIndividual,1); die();
			foreach($dadosCadastroIndividual as $row){
				$thriftCadastroIndividual = new ThriftCadastroIndividual();
				$uuid = $thriftCadastroIndividual->getGUID();
        if(empty($uuid)){
          file_put_contents('../uuid_'.date("Y-m-d").'.log', print_r($row, true), FILE_APPEND);
        }
				// Passo 1: criar e preencher o thrift do atendimento;
				$ficha_master = $this->cadastroIndividualThrift($uuid,$row);
				// Passo 2: serializar o thrift do atendimento;
				$ficha_master_serializado = $this->serialize_thrift($ficha_master);
				// Passo 3: coletar as informações do envio e das instalações, preencher o thrift de transporte com as informações coletadas;
				//echo "<pre>".print_r($ficha_master_serializado,1);die();
                                $array_envio = $this->montaArrayEnvioDto($ficha_master_serializado,$uuid,$row);
				// serializa todas informaões de envio mais os bytes serializos...
                                //echo "<pre>".print_r($array_envio,1);die();
				$array_envio_serializado = $this->serialize_thrift($array_envio);
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$eeh_codigo);
                                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,1);
				// Passo 5: atualiza status da ficha
				$bdCadastroIndividual->atualizaStatus($uuid,$row["usu_codigo"]);
				$i++;
			}
		}
	}

	public function cadastroIndividualThrift($uuid,$row){
    	$condicoesSaude = $this->condicoesDeSaudeThrift($row);
		$dadosGerais = $this->headerCdsCadastroThrift($row);
                $arraySituacaoDeRua = array("statusSituacaoRua"=>($row["eci_usu_sit_rua"] == "f" ? "0" : "1"));

		$emSituacaoDeRua = $this->emSituacaoDeRuaThrift($arraySituacaoDeRua);
                $arrayIdentificacao = array("statusFrequentaEscola" => ($row["eci_usu_escola"]  == "f" ? "0" : "1"),
                                            "statusTemAlgumaDeficiencia" => ($row["eci_usu_deficiencia"] == "f" ? "0" : "1"));
		$dadosCidadao = $this->identificacaoUsuarioCidadaoThrift($row);

		$infoDemo = $this->InformacoesSocioDemograficasThrift($arrayIdentificacao);
		$array_cad = array(
			"condicoesDeSaude"=>$condicoesSaude,
			"dadosGerais" => $dadosGerais,
			"emSituacaoDeRua" => $emSituacaoDeRua ,
			"fichaAtualizada" => "false",
			"identificacaoUsuarioCidadao" => $dadosCidadao,
			"informacoesSocioDemograficas" => $infoDemo,
			"statusTermoRecusaCadastroIndividualAtencaoBasica" => "0",
			"tpCdsOrigem" => "3",
			"uuid" => $uuid,
		);
		// Transforma o array em um objeto do E-SUS e retorna
                $ficha_master = $this->getCadastroIndividualThrift($array_cad);
                //echo "<pre>".print_r($ficha_master,1);die();
		return $ficha_master;
    }

	public function headerCdsCadastroThrift($row){
		$headerCadDomiciliar = array(
			"cnsProfissional"=> ($this->validaCnsBanco($row["eci_usr_profissional_cns"])==true ? $row["eci_usr_profissional_cns"] : NULL),
			"cnesUnidadeSaude" => $row["eci_usr_cnes"],
			"codigoIbgeMunicipio"=> $row["eci_usr_codigo_ibge"],
			"dataAtendimento" => (strtotime($row["eci_usr_dtatendimento"])) * 1000 ,
		);
                //echo"<pre>".print_r($headerCadDomiciliar,1);die();
		$header = $this->getHeader($headerCadDomiciliar);
		return $header;
	}

	public function condicoesDeSaudeThrift($row){
		$condicoesSaude = array();
		$condicao = $this->getCondicoesDeSaudeThrift($condicoesSaude);
        return $condicao;
	}

	public function emSituacaoDeRuaThrift($situacaoRua) {


		$situacaoRuaConv = $this->getEmSituacaoDeRuaThrift($situacaoRua);
		return $situacaoRuaConv;
	}

	public function InformacoesSocioDemograficasThrift($infoDemo) {
		$infoDemoConv = $this->getInformacoesSocioDemograficasThrift($infoDemo);
		return $infoDemoConv;
	}


	public function identificacaoUsuarioCidadaoThrift($row){
                //die(strtotime("2015-12-15") . "------" . strtotime("15/12/2015") . "---------" . strtotime("15-12-2015"));
		$dadosCidadao = array(
			"codigoIbgeMunicipioNascimento" => $row["eci_usu_codigo_ibge"],
			"dataNascimentoCidadao" => (strtotime($row["eci_usu_dtnascimento"])) * 1000 ,
                        //"dataNascimentoCidadao" => "1416009600000",
			"nacionalidadeCidadao" => $row["eci_usu_nacionalidade"],
			"nomeCidadao" => trim($row["eci_usu_nome"]),
			"nomeMaeCidadao" => trim($row["eci_usu_mae"]),
			"numeroCartaoSus" => ($this->validaCnsBanco($row["eci_usu_cns"])==true ? $row["eci_usu_cns"] : NULL),
			"racaCorCidadao" => $row["eci_usu_raca"],
			"sexoCidadao" => $row["eci_usu_sexo"]
		);
                //echo"<pre>".print_r($dadosCidadao,1);die();
		$dadosCidadaoConv = $this->getIdentificacaoUsuarioCidadaoThrift($dadosCidadao);
		return $dadosCidadaoConv;
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
		// Adiciona um arquivo à pasta
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
			"major"    => "1",
                        "minor"    => "3",
                        "revision" => "0"
		);

		$array_envio_dto = array(
                    "uuidDadoSerializado" => $uuid,
                    "tipoDadoSerializado" => $row['eci_tipo_dado_serializado'],
                    "cnesDadoSerializado" =>  $row['eci_usr_cnes'] ? $row['eci_usr_cnes'] : '',
                    "codIbge" => $row['eci_usr_codigo_ibge'],
                    "dadoSerializado" => $ficha_master_serializado,
                    "remetente" => $this->getInfoInstal($remetente),
                    "originadora"  => $this->getInfoInstal($originadora),
                    "versao" => $this->getVersao($array_versao)
		);
		//echo"<pre>".print_r($dadosCidadao,1);die();
		return $this->getTransporteThrift($array_envio_dto);
	}

	public function getDadosOriginadoraRemetente(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql);
		$row = pg_fetch_array($query);
		return $row;
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

	public function getCondicoesDeSaudeThrift($vals){
        $condicao = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastroindividual\CondicoesDeSaudeThrift($vals);
        return $condicao;
    }

    public function getHeader($vals){
    $header = new \br\gov\saude\esus\cds\transport\generated\thrift\common\HeaderCdsCadastroThrift($vals);
    return $header;
    }

	public function getEmSituacaoDeRuaThrift($vals){
        $endereco = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastroindividual\EmSituacaoDeRuaThrift($vals);
        return $endereco;
    }

	public function getInformacoesSocioDemograficasThrift($vals){
        $infoDemo = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastroindividual\InformacoesSocioDemograficasThrift($vals);
        return $infoDemo;
    }

	public function getIdentificacaoUsuarioCidadaoThrift($vals){
        $dadosCidadao = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastroindividual\IdentificacaoUsuarioCidadaoThrift($vals);
        return $dadosCidadao;
    }

	public function getCadastroIndividualThrift($vals){
        $cadastroIndividualThrift = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastroindividual\CadastroIndividualThrift($vals);
        return $cadastroIndividualThrift;
    }

	// Transforma o array em um objeto do E-SUS
    public function getInfoInstal($vals){
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoInstalacaoThrift($vals);
    }

	// Transforma o array em um objeto do E-SUS
    public function getVersao($vals){
        return new \br\gov\saude\esus\transport\common\api\configuracaodestino\VersaoThrift($vals);
    }

	// Serializa objeto do Thrift
    public function serialize_thrift($ficha_master = FALSE) {
       //echo "<pre>".print_r($ficha_master,1); die();
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($ficha_master);
    }

	public function getTransporteThrift($vals){
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoTransporteThrift($vals);
    }

}