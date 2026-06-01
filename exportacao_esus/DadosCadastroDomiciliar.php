<?php
include "db_class/CadastroDomiciliar.php";
include_once("db_class/EsusHistoricoItens.php");

class ThriftCadastroDomiciliar {

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
        $bdCadastroDomiciliar = new BancoCadastroDomiciliar();
        $bdEsusHistoricoItens = new EsusHistoricoItens();
        $dadosCadastroDomiciliar = $bdCadastroDomiciliar->getDadosCadastroDomiciliar();
        $numAtend = $bdCadastroDomiciliar->getNumDadosCadastroDomiciliar();
        $cab = "Total Cadastro Domiciliar: ".$numAtend."\n";
        log_exportacao($cab);
        $controle=0;
        if ($numAtend > 0) {
            foreach ($dadosCadastroDomiciliar as $row) {
                $controle++;
                $info = "Cadastro Domiciliar: ".$controle." Serial:" .$uuid."\n";
                log_exportacao($info);
                $thriftCadastroDomiciliar = new ThriftCadastroDomiciliar();
                $uuid = $thriftCadastroDomiciliar->getGUID();
                if(empty($uuid)){
                    file_put_contents('../uuid_'.date("Y-m-d").'.log', print_r($row, true), FILE_APPEND);
                }
                // Passo 1: criar e preencher o thrift do atendimento;
                $ficha_master = $this->cadastroDomiciliarThrift($uuid, $row);
                //echo "<pre>".print_r($ficha_master,1);
                // Passo 2: serializar o thrift do atendimento;
                $ficha_master_serializado = $this->serialize_thrift($ficha_master);
                // Passo 3: coletar as informações do envio e das instalações, preencher o thrift de transporte com as informações coletadas;
                $array_envio = $this->montaArrayEnvioDto($ficha_master_serializado, $uuid, $row);
                // serializa todas informaões de envio mais os bytes serializos...
                $array_envio_serializado = $this->serialize_thrift($array_envio);
                // Passo 4: serializar o thrift de transporte e gerar o arquivo zip;
                $this->zipWriter($array_envio_serializado, $uuid,$eeh_codigo);
                $bdEsusHistoricoItens->registratHistoricoItens($eeh_codigo,$uuid,2);
                // Passo 5: Atualiza status da ficha
                $bdCadastroDomiciliar->atualizaStatus($uuid, $row["co_cds_cad_domiciliar"]);
                echo "+";
            }
        }
    }

    private function zipWriter($array_envio_serializado, $uuid,$eeh_codigo) {
        // Define nome do arquivo
        $arquivo = $uuid . ".esus";
        // Abre arquivo
        $fp = fopen("./arqs/" . $arquivo, 'w');
        // Escreve no arquivo
        fwrite($fp, $array_envio_serializado);
        // Fecha o arquivo
        fclose($fp);
        // Cria ou abre uma pasta compactada
        $zip = new ZipArchive;
        $zip->open('./arqs/RAS_FileZip_'.$eeh_codigo.'.zip', ZipArchive::CREATE);
        // Adiciona um arquivo à pasta
        $zip->addFile("./arqs/" . $arquivo, $arquivo);
        // Fecha a pasta e salva o arquivo
        $zip->close();
        // Deleta arquivo adiciona a pasta
        unlink("./arqs/" . $arquivo);
    }

    private function cadastroDomiciliarThrift($uuid, $row) {
        $condicaoMoradia = $this->condicaoMoradiaThrift($row);
        $headerCadDomiciliar = $this->headerCdsCadastroThrift($row);
        $enderecoLocalPermanencia = $this->enderecoLocalPermanenciaThrift($row);
        $familias = $this->familiaRowThrift($row);
        $array_cad = array(
            "uuid" => $uuid,
            "condicaoMoradia" => $condicaoMoradia,
            "dadosGerais" => $headerCadDomiciliar,
            "enderecoLocalPermanencia" => $enderecoLocalPermanencia,
            "familias" => $familias,
            "fichaAtualizada" => 't',
            "tpCdsOrigem" => "3",
        );
        // Transforma o array em um objeto do E-SUS e retorna
        $ficha_master = $this->getCadastroDomiciliarThrift($array_cad);
        //echo"<pre>".print_r($ficha_master,1);die();
        return $ficha_master;
    }

    public function familiaRowThrift($row) {
        $bdCadastroDomiciliar = new BancoCadastroDomiciliar();
        $array = $bdCadastroDomiciliar->getDadosFamilia($row["co_cds_cad_domiciliar"]);
        foreach ($array as $dados) {
            $dadosFamilia = array(
                "dataNascimentoResponsavel" => (strtotime($dados["usu_datanasc"])+ 86400) * 1000,
                "numeroCnsResponsavel" => ($this->validaCnsBanco($dados["usu_cartao_sus"]) == true ? $dados["usu_cartao_sus"] : NULL),
                "numeroProntuario" => $dados["usu_prontuario"]
            );
        }
        if (count($dadosFamilia) > 0) {
            return array($this->getFamilia($dadosFamilia));
        } else {
            return array();
        }
    }

    public function condicaoMoradiaThrift($item) {
        $condicaoMoradia = array(
            "situacaoMoradiaPosseTerra" => $item["situacao_moradia"],
            "localizacao" => $item["localizacao"]
        );
        $condicao = $this->getCondicoes($condicaoMoradia);
        return $condicao;
    }

    public function headerCdsCadastroThrift($item) {
        $headerCadDomiciliar = array(
            "cnsProfissional" => ($this->validaCnsBanco(trim($item["cns_profissional"])) == true ? trim($item["cns_profissional"]) : NULL),
            "cnesUnidadeSaude" => $item["cnes_unidade"],
            "codigoIbgeMunicipio" => $item["codigo_ibge_municipio"],
            "dataAtendimento" => ((strtotime(str_replace("-", "/", $item["data_atendimento"])))) * 1000,
            "ineEquipe" => $item["nu_ine"]
        );
        $header = $this->getHeader($headerCadDomiciliar);
        //echo"<pre>".print_r($headerCadDomiciliar,1);die();
        return $header;
    }

    public function enderecoLocalPermanenciaThrift($item) {
        //echo"<pre>".print_r($item,1);die();
        $enderecoLocalPermanencia = array(
            "bairro" => $item["bairro"],
            "cep" => $item["cep"],
            "codigoIbgeMunicipio" => $item["cid_codigo_ibge"],
            "nomeLogradouro" => $item["nome_logradouro"],
            "numero" => $item["numero"],
            "numeroDneUf" => $item["estado"],
            "tipoLogradouroNumeroDne" => $item["tipo_logradouro_numero_dne"]
        );
        $endereco = $this->getEnderecoLocal($enderecoLocalPermanencia);
        return $endereco;
    }

    private function montaArrayEnvioDto($ficha_master_serializado, $uuid, $row) {

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
            "revision" => "0"
        );

        $array_envio_dto = array(
            "uuidDadoSerializado" => $uuid,
            "tipoDadoSerializado" => "3 L",
            "cnesDadoSerializado" => $row['cnes_unidade'] ? $row['cnes_unidade'] : '',
            "codIbge" => $row['codigo_ibge_municipio'],
            "ineDadoSerializado" => $row['nu_ine'],
            "dadoSerializado" => $ficha_master_serializado,
            "remetente" => $this->getInfoInstal($remetente),
            "originadora" => $this->getInfoInstal($originadora),
            "versao" => $this->getVersao($array_versao)
        );
        //echo"<pre>".print_r($array_envio_dto,1);die();
        return $this->getTransporteThrift($array_envio_dto);
    }

    public function getDadosOriginadoraRemetente() {
        $sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
        $query = pg_query($sql);
        $row = pg_fetch_array($query);
        return $row;
    }

    public function validaCnsBanco($cns) {
        if ((substr($cns, 0, 1) != "7") && (substr($cns, 0, 1) != "8") && (substr($cns, 0, 1) != "9")) {
            return $this->validaCNS($cns);
        } else {
            return $this->validaCNS_PROVISORIO($cns);
        }
    }

    public function validaCNS($cns) {
        if ((strlen(trim($cns))) != 15) {
            return false;
        }
        $pis = substr($cns, 0, 11);
        $soma = (((substr($pis, 0, 1)) * 15) +
                ((substr($pis, 1, 1)) * 14) +
                ((substr($pis, 2, 1)) * 13) +
                ((substr($pis, 3, 1)) * 12) +
                ((substr($pis, 4, 1)) * 11) +
                ((substr($pis, 5, 1)) * 10) +
                ((substr($pis, 6, 1)) * 9) +
                ((substr($pis, 7, 1)) * 8) +
                ((substr($pis, 8, 1)) * 7) +
                ((substr($pis, 9, 1)) * 6) +
                ((substr($pis, 10, 1)) * 5));
        $resto = fmod($soma, 11);
        $dv = 11 - $resto;
        if ($dv == 11) {
            $dv = 0;
        }
        if ($dv == 10) {
            $soma = ((((substr($pis, 0, 1)) * 15) +
                    ((substr($pis, 1, 1)) * 14) +
                    ((substr($pis, 2, 1)) * 13) +
                    ((substr($pis, 3, 1)) * 12) +
                    ((substr($pis, 4, 1)) * 11) +
                    ((substr($pis, 5, 1)) * 10) +
                    ((substr($pis, 6, 1)) * 9) +
                    ((substr($pis, 7, 1)) * 8) +
                    ((substr($pis, 8, 1)) * 7) +
                    ((substr($pis, 9, 1)) * 6) +
                    ((substr($pis, 10, 1)) * 5)) + 2);
            $resto = fmod($soma, 11);
            $dv = 11 - $resto;
            $resultado = $pis . "001" . $dv;
        } else {
            $resultado = $pis . "000" . $dv;
        }
        if ($cns != $resultado) {
            return false;
        } else {
            return true;
        }
    }

    public function validaCNS_PROVISORIO($cns) {
        if ((strlen(trim($cns))) != 15) {
            return false;
        }
        $soma = (((substr($cns, 0, 1)) * 15) +
                ((substr($cns, 1, 1)) * 14) +
                ((substr($cns, 2, 1)) * 13) +
                ((substr($cns, 3, 1)) * 12) +
                ((substr($cns, 4, 1)) * 11) +
                ((substr($cns, 5, 1)) * 10) +
                ((substr($cns, 6, 1)) * 9) +
                ((substr($cns, 7, 1)) * 8) +
                ((substr($cns, 8, 1)) * 7) +
                ((substr($cns, 9, 1)) * 6) +
                ((substr($cns, 10, 1)) * 5) +
                ((substr($cns, 11, 1)) * 4) +
                ((substr($cns, 12, 1)) * 3) +
                ((substr($cns, 13, 1)) * 2) +
                ((substr($cns, 14, 1)) * 1));
        $resto = fmod($soma, 11);
        if ($resto != 0) {
            return false;
        } else {
            return true;
        }
    }

    // Transforma o array em um objeto do E-SUS
    public function getInfoInstal($vals) {
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoInstalacaoThrift($vals);
    }

    // Transforma o array em um objeto do E-SUS
    public function getVersao($vals) {
        return new \br\gov\saude\esus\transport\common\api\configuracaodestino\VersaoThrift($vals);
    }

    // Serializa objeto do Thrift
    public function serialize_thrift($ficha_master = FALSE) {
        //echo "<pre>".print_r($ficha_master,1); die();
        $tStream = new Thrift\Serializer\TBinarySerializer();
        return $tStream->serialize($ficha_master);
    }

    public function getCadastroDomiciliarThrift($vals) {
        $cadastroDomiciliarThrift = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastrodomiciliar\CadastroDomiciliarThrift($vals);
        return $cadastroDomiciliarThrift;
    }

    public function getCondicoes($vals) {
        $condicao = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastrodomiciliar\CondicaoMoradiaThrift($vals);
        return $condicao;
    }

    public function getHeader($vals) {
        $header = new \br\gov\saude\esus\cds\transport\generated\thrift\common\HeaderCdsCadastroThrift($vals);
        return $header;
    }

    public function getEnderecoLocal($vals) {
        $endereco = new \br\gov\saude\esus\cds\transport\generated\thrift\common\EnderecoLocalPermanenciaThrift($vals);
        return $endereco;
    }

    public function getFamilia($vals) {
        $familia = new \br\gov\saude\esus\cds\transport\generated\thrift\cadastrodomiciliar\FamiliaRowThrift($vals);
        return $familia;
    }

    public function getTransporteThrift($vals) {
        return new \br\gov\saude\esus\transport\common\generated\thrift\DadoTransporteThrift($vals);
    }

}
