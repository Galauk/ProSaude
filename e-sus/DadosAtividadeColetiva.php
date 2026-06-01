<?php

// Conex�o banco de dados
include("db_class/AtividadeColetiva.php");
include_once("db_class/EsusHistoricoItens.php");

class ThriftAtividadeColetiva {
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

	public function executeMain($eeh_codigo=FALSE){
		$bdAtividadeColetiva = new AtividadeColetiva();
        $bdEsusHistoricoItens = new EsusHistoricoItens();
		$dadosAtividadeColetiva = $bdAtividadeColetiva->getDadosAtividadeColetiva();
		// echo "<pre>";print_r($dadosAtividadeColetiva);die();
		$numAtend = $bdAtividadeColetiva->numDadosAtividadeColetiva();
		if ($numAtend > 0) {
			$i=0;
			foreach($dadosAtividadeColetiva as $row) {
				$ThriftAtividadeColetiva = new ThriftAtividadeColetiva();
				$uuid = $ThriftAtividadeColetiva->getGUID();
				if(empty($uuid)){
					file_put_contents('../uuid_'.date("Y-m-d").'.log', print_r($row, true), FILE_APPEND);
				}
				// Passo 1: criar e preencher o thrift do atendimento;
				$dadosAtividadeColetiva = $this->atividadeColetivaMasterThrift($uuid,$row);
				//echo "<pre>".print_r($dadosAtividadeColetiva,1); die();
				// Passo 2: serializar o thrift do atendimento;
				$dadosAtividadeColetivaSerializado = $this->serialize_thrift($dadosAtividadeColetiva);
				// Passo 3: coletar as informa��es do envio e das instala��es, preencher o thrift de transporte com as informa��es coletadas;
				$array_envio = $this->montaArrayEnvioDto($dadosAtividadeColetivaSerializado,$uuid,$row);
				// Serializa todas informa��es de envio mais os bytes serializos...
				$array_envio_serializado = $this->serialize_thrift($array_envio);
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$eeh_codigo);
                                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,6);
				// Passo 5: Atualiza status da ficha
				$bdAtividadeColetiva->atualizaStatus($uuid,$row["co_cds_ficha_ativ_col"]);
				$i++;
			}
		}
	}

	private function zipWriter($array_envio_serializado,$uuid,$eeh_codigo){
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
		// Adiciona um arquivo � pasta
		$zip->addFile("./arqs/".$arquivo,$arquivo);
		// Fecha a pasta e salva o arquivo
		$zip->close();
		// Deleta arquivo adiciona a pasta
		unlink("./arqs/".$arquivo);
    }

	public function atividadeColetivaMasterThrift($uuid=FALSE,$row=FALSE){
		$codAtiv = $row["co_cds_ficha_ativ_col"];
		// echo "<pre>";print_r($row);die();
		// die($uuid);
		$array = array(
			"uuidFicha" => $uuid,
			"dtAtividadeColetiva" => (strtotime(substr($row["eav_dt_atividade"],0,10))) * 1000 ,
			"numParticipantesProgramados" => $row["eav_num_participantes_prog"],
			"localAtividade" => strip_tags(trim($row["eav_local_atividade"])),
			"horaInicio" => ((strtotime($row["eav_hora_inicio"])) ) * 1000 ,
                        "horaFim" => ((strtotime($row["eav_hora_fim"])) ) * 1000 ,	
			"inep" => $row["eav_inep"],
			"responsavelCns" => ($this->validaCnsBanco(trim($row['eav_responsavel_cns']))==true ? trim($row['eav_responsavel_cns']) : NULL),
			"responsavelCnesUnidade" => $row["eav_responsavel_cnes"],
			//"responsavelNumIne" => $row["eav_responsavel_ine"],
			"numParticipantes" => $row["eav_num_participantes"],
			"numAvaliacoesAlteradas" => $row["eav_num_aval_alteradas"],
			"profissionais" => $this->profissionalCboRowItemThrift($codAtiv),
			"atividadeTipo" => $row["eav_atividade_tipo"]." L",
			"temasParaReuniao" => $this->temasParaReuniao($codAtiv),
			"publicoAlvo" => $this->publicoAlvo($codAtiv),
			"praticasTemasParaSaude" => $this->praticasTemasParaSaude($codAtiv),
			"participantes" => $this->participantes($codAtiv),
			"tbCdsOrigem" => "3",
			"codigoIbgeMunicipio" => $row["eav_codigo_ibge"],
			// ESUS 3.1
			"turno" => $row["eav_turno"]
		);
		// echo "<pre>";print_r($array[1]);die();
		$dadosAct = $this->getFichaAtividadeColetivaThrift($array);
		return $dadosAct;	
	}


	public function profissionalCboRowItemThrift($codAtiv){
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosProfissional = $bdAtividadeColetiva->getDadosProfissional($codAtiv);
		// echo "<pre>";print_r($dadosProfissional);die();
		$arrayGeral = array();
		foreach($dadosProfissional as $val) {
			$array = array(
				"cns" => ($this->validaCnsBanco($val["cnes_cod_cns"])==true ? $val["cnes_cod_cns"] : NULL),
				"codigoCbo2002" => $val["cbo"]
			);
			$arrayConv = $this->getProfissionalCboRowItemThrift($array);
			array_push($arrayGeral,$arrayConv);
		}
		return $arrayGeral;
	}

	public function temasParaReuniao($codAtiv){
		// die("ola tema");
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosTemas = $bdAtividadeColetiva->getCodigosTemas($codAtiv);
		$codsTema = array();
		foreach($dadosTemas as $val) {
			array_push($codsTema, $val["co_cds_ativ_col_tema"]." L");
		}
		return $codsTema;
	}

	public function  publicoAlvo($codAtiv){
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosPublicoAlvo = $bdAtividadeColetiva->getCodigosPublicoAlvo($codAtiv);
		$codsPublicoAlvo = array();
		foreach($dadosPublicoAlvo as $val) {
			array_push($codsPublicoAlvo, $val["co_cds_ativ_col_publico_alvo"]." L");
		}
		return $codsPublicoAlvo;
	}

	public function praticasTemasParaSaude($codAtiv) {
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosPratica = $bdAtividadeColetiva->getCodigosPratica($codAtiv);
		$codsPratica = array();
		foreach($dadosPratica as $val) {
			array_push($codsPratica, $val["co_cds_ativ_col_pratica"]." L");
		}
		return $codsPratica;
	}

	public function participantes($codAtiv){
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosParticipantes = $bdAtividadeColetiva->getDadosParticipantes($codAtiv);
		$arrayGeral = array();
		foreach($dadosParticipantes as $val) {
			if ($this->validaCnsBanco(trim($val["usu_cartao_sus"]))==true){
				$array = array(
					"cns" => ($this->validaCnsBanco(trim($val["usu_cartao_sus"]))==true ? trim($val["usu_cartao_sus"]) : NULL),
					"dataNascimento" => ((strtotime(substr($val["dt_nascimento"],0,10))) + 86400) * 1000,
					"avaliacaoAlterada" => $val["st_avaliacao_alterada"],
					"peso" => ($val["nu_peso"] < 1 ? "1" : $val["nu_peso"]),
					"altura" => ($val["nu_altura"] < 20 ? "20" : $val["nu_altura"]),
					//"cessouHabitoFumar" => $val["st_cessou_habito_fumar"],
					//"abandonouGrupo" => $val["st_abandonou_grupo"]
				);
				$arrayConv = $this->getParticipanteRowItemThrift($array);
				array_push($arrayGeral,$arrayConv);
			}
		}
		return $arrayGeral;
	}

	private function montaArrayEnvioDto($dadosAtividadeColetivaSerializado,$uuid,$row){

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
			"tipoDadoSerializado" => "6 L",
			"cnesDadoSerializado" =>  $row['eav_uni_cnes'] ? $row['eav_uni_cnes'] : '',
			"codIbge" => $row['eav_codigo_ibge'],
			"ineDadoSerializado" => $row['eav_responsavel_ine'],
			"dadoSerializado" => $dadosAtividadeColetivaSerializado,
			"remetente" => $this->getInfoInstal($remetente),
			"originadora"  => $this->getInfoInstal($originadora),
			"versao" => $this->getVersao($array_versao)
		);

		return $this->getTransporteThrift($array_envio_dto);
    }

	public function getDadosOriginadoraRemetente(){
		$bdAtividadeColetiva = new AtividadeColetiva();
		$dadosOriginadoraRemetente = $bdAtividadeColetiva->getDadosOriginadoraRemetente();
		return $dadosOriginadoraRemetente;
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
    public function serialize_thrift($dadosAtividadeColetiva = FALSE) {
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($dadosAtividadeColetiva);
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

	public function getFichaAtividadeColetivaThrift($vals){
		$dadosAtividadeColetivaThrift = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadecoletiva\FichaAtividadeColetivaThrift($vals);
		// echo "<pre>";print_r($dadosAtividadeColetivaThrift);die();
        return $dadosAtividadeColetivaThrift;
    }

	public function getProfissionalCboRowItemThrift($vals){
		// echo "<pre>";print_r($vals);die();
        $dadosProfissional = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadecoletiva\ProfissionalCboRowItemThrift($vals);
        return $dadosProfissional;
    }

	public function getParticipanteRowItemThrift($vals){
        $dadosParticipantes = new \br\gov\saude\esus\cds\transport\generated\thrift\atividadecoletiva\ParticipanteRowItemThrift($vals);
        return $dadosParticipantes;
    }

}