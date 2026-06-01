<?php

include("db_class/VisitaDomiciliar.php");
include_once("db_class/EsusHistoricoItens.php");

class ThriftVisitaDomiciliar {
	
	function __construct(){
		$this->getGUID = getGUID();
	}
	
	public function executeMain($eeh_codigo){
		$bdVisitaDomiciliar = new VisitaDomiciliar();
                $bdEsusHistoricoItens = new EsusHistoricoItens();
		$dadosVisitaDomiciliar = $bdVisitaDomiciliar->getDadosVisitaDomiciliar();
		$numAtend = $bdVisitaDomiciliar->numDadosVisitaDomiciliar();
		if ($numAtend > 0) {
			//Efetuando leitura de dados
			foreach($dadosVisitaDomiciliar as $row) {
				//GERA��O DO ARQUIVO
				$ThriftVisitaDomiciliar = new ThriftVisitaDomiciliar();
				$uuid = $ThriftVisitaDomiciliar->getGUID;
				// Passo 1: criar e preencher o thrift do atendimento;
				$dadosVisitaDomiciliar = $this->visitaDomiciliarMasterThrift($uuid,$row);
				// Passo 2: serializar o thrift do atendimento;
				$dadosVisitaDomiciliarSerializado = $this->serialize_thrift($dadosVisitaDomiciliar);
				// Passo 3: coletar as informa��es do envio e das instala��es, preencher o thrift de transporte com as informa��es coletadas;
				$array_envio = $this->montaArrayEnvioDto($dadosVisitaDomiciliarSerializado,$uuid,$row);
				// Serializa todas informa��es de envio mais os bytes serializos...
				$array_envio_serializado = $this->serialize_thrift($array_envio); 
				// Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
				$this->zipWriter($array_envio_serializado,$uuid,$eeh_codigo);
                                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,5);
				// Passo 5: atualizar os dados
				$bdVisitaDomiciliar->atualizaStatus($uuid,$row["ate_codigo"]);
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
	
	public function visitaDomiciliarMasterThrift($uuid=FALSE,$row=FALSE){
		$dadosHeaderTransport = $this->headerTransport($row);
		$dadosFichaVisitaDomiciliar = $this->fichaVisitaDomiciliarChildThrift($row);
		$array = array(
			"uuidFicha" => $uuid,
			"tpCdsOrigem" => 3,
			"headerTransport" => $this->getUnicaLotacaoHeaderThrift($dadosHeaderTransport),
			"visitasDomiciliares" => array($this->getFichaVisitaDomiciliarChildThrift($dadosFichaVisitaDomiciliar))
		);
		$dadosVD = $this->getFichaVisitaDomiciliarMasterThrift($array);
		return $dadosVD;	
	}
	
	public function headerTransport($row=FALSE){
		$array = array(
			"profissionalCNS"=> ($this->validaCnsBanco(trim($row['esv_profissional_cns']))==true ? trim($row['esv_profissional_cns']) : NULL),
			"cboCodigo_2002"=> $row['esv_cbo_codigo_2002'],
			"cnes"=> $row['esv_cnes'],
			"ine"=> $row['esv_ine'],
			"dataAtendimento"=> ((strtotime($row['esv_dtatendimento'])) + 86400) * 1000 ,
			"codigoIbgeMunicipio"=> $row['esv_codigo_ibge_mun']
		);
        return $array;
	}
	
	public function fichaVisitaDomiciliarChildThrift($row){
		$array = array(
			"numProntuario" => $row['esv_num_prontuario'],
			"numCartaoSus" => ($this->validaCnsBanco(trim($row['esv_usu_cns']))==true ? trim($row['esv_usu_cns']) : NULL),
			"dtNascimento" => ((strtotime($row['esv_usu_datanasc'])) + 86400) * 1000 ,
			"sexo" => $row['esv_usu_sexo'],
			"statusVisitaCompartilhadaOutroProfissional" => "",
			"motivosVisita" => $this->motivoVisita($row['co_cds_visita_domiciliar']),
			"desfecho" => $row['esv_desfecho']
		);
		return $array;
	}
	
	public function  motivoVisita($codVisita){
		$bdVisitaDomiciliar = new VisitaDomiciliar();
		$dadosVisitaDomiciliar = $bdVisitaDomiciliar->getCodigosVisita($codVisita); 
		$codsVis = array();
		foreach($dadosVisitaDomiciliar as $rowSql) {
			array_push($codsVis, $rowSql["co_cds_visita_dom_motivo"]." L");
		}	
		return $codsVis;
	}
	
	private function montaArrayEnvioDto($dadosVisitaDomiciliarSerializado,$uuid,$row){
		
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
			"tipoDadoSerializado" => "8 L",
			"cnesDadoSerializado" =>  $row['esv_cnes'],
			"codIbge" => $row['esv_codigo_ibge_mun'],
			"ineDadoSerializado" => $row['esv_ine'],
			"dadoSerializado" => $dadosVisitaDomiciliarSerializado,
			"remetente" => $this->getInfoInstal($remetente),
			"originadora"  => $this->getInfoInstal($originadora),
			"versao" => $this->getVersao($array_versao)
		);
								 
        return $this->getTransporteThrift($array_envio_dto);
        
    }
	
	public function getDadosOriginadoraRemetente(){
		$bdVisitaDomiciliar = new VisitaDomiciliar();
		$dadosOriginadoraRemetente = $bdVisitaDomiciliar->getDadosOriginadoraRemetente(); 
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
    public function serialize_thrift($dadosVisitaDomiciliar = FALSE) {
       //echo "<pre>".print_r($ficha_master,1); die();
	   $tStream = new Thrift\Serializer\TBinarySerializer();
       return $tStream->serialize($dadosVisitaDomiciliar);
    }
	
	// Transforma o array em um objeto do E-SUS
    public function getUnicaLotacaoHeaderThrift($vals){
        $unica = new br\gov\saude\esus\cds\transport\generated\thrift\common\UnicaLotacaoHeaderThrift($vals);
        return $unica;
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
	
	// Transforma o array em um objeto do E-SUS
    public function getFichaVisitaDomiciliarMasterThrift($vals){
        $dadosVisitaDomiciliar = new \br\gov\saude\esus\cds\transport\generated\thrift\visitadomiciliar\FichaVisitaDomiciliarMasterThrift($vals);
        return $dadosVisitaDomiciliar;
    }
	
	public function getFichaVisitaDomiciliarChildThrift($vals){
        $dadosVisitaDomiciliar = new \br\gov\saude\esus\cds\transport\generated\thrift\visitadomiciliar\FichaVisitaDomiciliarChildThrift($vals);
        return $dadosVisitaDomiciliar;
    }

}