<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ExportacaoRaas extends Elotech_Db_Table_Abstract {
    

    public function montaCabecalho($cabeca){
        $cabecalhoPronto = $this->validaCabecalho($cabeca);
        return $cabecalhoPronto;

    }

    public function montaTextos($ficha,$acoes){

       //for($j=0 ; $j<count($cabeca); j++){
        //echo "<pre>";print_r($ficha);die();

        $fichaPronta = $this->validaFicha($ficha,$acoes);
        //echo "<pre>";print_r($fichaPronta);die();
        $msg = $fichaPronta;

        for ($i=0; $i < count($acoes) ; $i++) { 
             $acaoPronta = $this->validaAcoes($acoes[$i]);
             $msg .=$acaoPronta;
        }

            //echo "<pre>";print_r($msg);die();
        return $msg;
    }

    public function validaCabecalho($cabeca){
        $quebra = chr(13) . chr(10);

        //echo "<pre>";print_r($cabeca);die();

        $cbclin = $cabeca[cbclin];
        if(strlen($cbclin) < 6){
            while(strlen($cbclin) < 6){
                $cbclin = '0' . $cbclin ;
            }
        }

        //30 caracteres nome estabelecimento
        $cbcrsp = substr($cabeca[cbcrsp],0,30);
        if(strlen($cbcrsp) < 30){
            while(strlen($cbcrsp) < 30){
                $cbcrsp = $cbcrsp . ' ';
            }
        }

        $cbcsgl = $cabeca[cbcsgl];
        if(strlen($cbcsgl) < 6){
            while(strlen($cbcsgl) < 6){
                $cbcsgl = '0' . $cbcsgl;
            }
        }

        $cbccgccpf = $cabeca[cbccgccpf];
        if(strlen($cbccgccpf) < 14){
            while(strlen($cbccgccpf) < 14){
                $cbccgccpf = '0' . $cbccgccpf;
            }
        }

        $cbcdst = $cabeca[cbcdst];
        if(strlen($cbcdst) < 40){
            while(strlen($cbcdst) < 40){
                $cbcdst = $cbcdst . ' ';
            }
        }

        $cbcversao = $cabeca[cbcversao];
        if(strlen($cbcversao) < 15){
            while(strlen($cbcversao) < 15){
                $cbcversao = $cbcversao . ' ';
            }
        }
        $cbcbdversao = $cabeca[cbcbdversao];
        if(strlen($cbcbdversao) < 7){
            while(strlen($cbcbdversao) < 7){
                $cbcbdversao = $cbcbdversao . ' ';
            }
        }


        return $c = $cabeca[cbccodlinha].$cabeca[cbchdr].$cabeca[cbcmvm] . $cbclin . $cabeca[cbcsmtvrf]. $cbcrsp . $cbcsgl . $cbccgccpf . $cbcdst . $cabeca[cbcdstin] . $cabeca[cbcdtger] . $cbcversao .$cbcbdversao . '               '.$quebra;
    }
    public function validaFicha($ficha,$acoes){
        $quebra = chr(13) . chr(10);
        //retira separador cep e datas
        //datas iniciais e finais da validade do atendimento
        //ras_dataexe
        $rasdatax = $acoes[0][ras_dataexe];
        if($rasdatax =="") $rasdatax = "        ";
        else{
            $explode1 = explode("-", $rasdatax);
            $tbProntAno1 = $explode1[0];
            $tbProntMes1 = $explode1[1];
            $tbProntDia1 = $explode1[2];
            $rasdatax = $tbProntAno1 . $tbProntMes1 . $tbProntDia1;
        }
        //echo"<pre>";print_r($rasdatax);die();

        $pegaanomes = substr($rasdatax,0,6); 
        //valida se vazio preencher com 8 brancos
        if($ficha[0][ras_val_fin] =="") $ras_val_fin = "        ";
        else{
            $explode2 = explode("-", $ficha[0][ras_val_fin]);
            $tbProntAno2 = $explode2[0];
            $tbProntMes2 = $explode2[1];
            $tbProntDia2 = $explode2[2];
            $ras_val_fin = $tbProntAno2 . $tbProntMes2 . $tbProntDia2;
        }
        //data de nascimento
        if($ficha[0][ras_datanasc] =="") $ras_datanasc = "        ";
        else{
            $explode3 = explode("-", $ficha[0][ras_datanasc]);
            $tbProntAno3 = $explode3[0];
            $tbProntMes3 = $explode3[1];
            $tbProntDia3 = $explode3[2];
            $ras_datanasc = $tbProntAno3 . $tbProntMes3 . $tbProntDia3;
        }
        //cep
        
        if($ficha[0][ras_cep] =="") $ras_cep = "85950000";
        else{
            $explode4 = explode("-", $ficha[0][ras_cep]);
            $cep1= $explode4[0];
            $cep2 = $explode4[1];
            $ras_cep = $cep1 . $cep2;
        }

        //limita 30 caracteres se menor, preencher o restante com brancos
        $nomepaciente = substr($ficha[0][ras_paciente],0, 30);

        if(mb_strlen($nomepaciente,'utf8') < 30){
            while(mb_strlen($nomepaciente,'utf8') < 30){
                $nomepaciente = $nomepaciente . ' ';
            }
        }
        $nomemae = substr($ficha[0][ras_nomemae],0,30);
        if(mb_strlen($nomemae,'utf8') < 30){
            while(mb_strlen($nomemae,'utf8') < 30){
                $nomemae = $nomemae . ' ';
            }
        }



        $responsavel = substr($ficha[0][ras_responsavel],0,30);
        if(mb_strlen($responsavel,'utf8') < 30){
            while(mb_strlen($responsavel,'utf8') < 30){
                $responsavel = $responsavel . ' ';
            }
        }

        //limita 10 caracteres se menor preencher o restante com brancos
        $complemento = substr($ficha[0][ras_complemento],0,10);
        if($complemento = "") $complemento = '          ';
        else if(mb_strlen($complemento,'utf8') < 10){
            while(mb_strlen($complemento,'utf8') < 10){
                $complemento = $complemento . ' ';
            }
        }



        //valida raca caracteres //se diferente de 99 preencher um 0 na frente
        $raca = $ficha[0][ras_raca]; 
        if($raca!=99) $raca ='0'.$raca;

        //valida prontuario caracteres se menos que 10 preencher com brancos
        $ras_prontuario = trim($ficha[0][ras_prontuario]);
        if(strlen($ras_prontuario) < 10){
            while(strlen($ras_prontuario) < 10){
                $ras_prontuario = '0'.$ras_prontuario ;
            }
        }
        //valida nacionalidade
        $nacionalidade = '010';
        if(strlen($nacionalidade) < 3){
            while(strlen($nacionalidade) < 3){
                $nacionalidade = '0' . $nacionalidade ;
            }
        }



        //validar caracteres se vazio preencher com brancos
        $ras_motivosaida = $ficha[0][ras_motivosaida];
        if($ras_motivosaida=="") $ras_motivosaida = '  ';

        //valida data obito alta permanencia
        $ras_data_obito_alta = $ficha[0][ras_data_obito_alta];
        if($ras_data_obito_alta =="") $ras_data_obito_alta ='        ';
        else {
            $exp = explode("-", $ras_data_obito_alta);
            $dtfin1 = $exp[0];
            $dtfin2 = $exp[1];
            $dtfin3 = $exp[2];
            $ras_data_obito_alta = $dtfin1 . $dtfin2 . $dtfin3;
        }

        //validar sexo se for 1 ou 0
        $ras_sexo =  $ficha[0][ras_sexo];

        //validar se vazio colocar caracteres em branco 4 em cada
        $ras_cidp = $ficha[0][ras_cidp]; //obrigatorio
        if($ras_cidp =="") $ras_cidp ='    ';
        else if(strlen($ras_cidp)<4) $ras_cidp.' ';
        $ras_cids1 = $ficha[0][ras_cids1];
        if($ras_cids1 =="") $ras_cids1 ='    ';
        else if(strlen($ras_cids1)<4) $ras_cids1.' ';
        $ras_cids2 = $ficha[0][ras_cids2];
        if($ras_cids2 =="") $ras_cids2 ='    ';
        else if(strlen($ras_cids2)<4) $ras_cids2.' ';
        $ras_cids3 = $ficha[0][ras_cids3];
        if($ras_cids3 =="") $ras_cids3 ='    ';
        else if(strlen($ras_cids3)<4) $ras_cids3.' ';
        $ras_cidca = $ficha[0][ras_cidca];
        if($ras_cidca =="") $ras_cidca ='    ';
        else if(strlen($ras_cidca)<4) $ras_cidca.' ';
        //validar se vazio preencher com brancos
        $ras_cnes_esf = $ficha[0][ras_cnes_esf];
        if ($ras_cnes_esf =="" && $ficha[0][ras_cobertura_esf] =="S" ) $ras_cnes_esf = '6411029';
        else if($ras_cnes_esf =="") $ras_cnes_esf = '0000000';

        //validar 5 caracteres zeros a esquerda
        $ras_total_acoes = count($acoes);
        if($ras_total_acoes == "") $ras_total_acoes = '00000';
        else if(strlen($ras_total_acoes) < 5 ){
            while(strlen($ras_total_acoes) < 5){
                $ras_total_acoes = '0' . $ras_total_acoes ;
            }
        }
        //echo"<pre>";print_r($ras_total_acoes); die();

        //validar destino e origem adicionar 0 a esquerda
        $ras_destino = '0'.$ficha[0][ras_destino];
        $ras_origem ='0'.$ficha[0][ras_origem];
        //sit rua se vazio preencher N
        $ras_situacao_rua = $ficha[0][ras_situacao_rua];
        if($ras_situacao_rua=="") $ras_situacao_rua = 'N';

        //tipos de drogas preencher 3 brancos se vazio, 2 brancos se apenas um tipo, 1 branco se dois tipos
        $ras_usu_tipo_droga = $ficha[0][ras_usu_tipo_droga];
        if($ras_usu_tipo_droga==""){
            while(strlen($ras_usu_tipo_droga) < 3){
                $ras_usu_tipo_droga = ' ' . $ras_usu_tipo_droga ;
            }
        }
        else if(strlen($ras_usu_tipo_droga) < 3 ){
            while(strlen($ras_usu_tipo_droga) < 3){
                $ras_usu_tipo_droga = ' ' . $ras_usu_tipo_droga ;
            }
        }
        //autorizacao 13 caracteres se vazio preencher com brancos se não, brancos nos caracteres restantes
        $ras_carater = $ficha[0][ras_carater];
        if($ras_carater == 0) $ras_carater = '00';
        else $ras_carater = '0' . $ras_carater;

        $ras_autorizacao = $ficha[0][ras_autorizacao];
        if(strlen($ras_autorizacao) < 13 ){
            while(strlen($ras_autorizacao) < 13){
                $ras_autorizacao = $ras_autorizacao . ' ' ;
            }
        }

        $ras_ibge_mun = $ficha[0][ras_ibge_mun];
        if($ras_ibge_mun == "") $ras_ibge_mun = '       ';
        else if(strlen($ras_ibge_mun) < 7 ){
            while(strlen($ras_ibge_mun) < 7){
                $ras_ibge_mun = $ras_ibge_mun . ' ' ;
            }
        }
        $ras_cns_paciente = $ficha[0][ras_cns_paciente];
        
        $getbai = $this->getDefaultAdapter()->query(
            "SELECT usuario.dom_codigo , usuario.usu_end_bairro from usuario
            WHERE usuario.usu_cartao_sus = '$ras_cns_paciente'
            "
        )->fetchAll();

        $xc = intval($getbai[0][dom_codigo]);
        $getdom = $this->getDefaultAdapter()->query(
            "SELECT * from domicilio
            WHERE domicilio.dom_codigo = $xc
            "
        )->fetchAll();

        $xb = intval($getdom[0][bai_codigo]);
        $getbaidom = $this->getDefaultAdapter()->query(
            "SELECT * from bairro
            where bairro.bai_codigo = $xb
            "
        )->fetchAll();

        $xrua = intval($getdom[0][rua_codigo]);
        $getrua = $this->getDefaultAdapter()->query(
            "SELECT * from rua
            WHERE rua.rua_codigo = $xrua
            "
        )->fetchAll();
        
        $logradouro = substr($ficha[0][ras_logradouro],0,30);
        if($logradouro == ""){
        	$logradouro = $getrua[0][rua_nome];
        }

        if(mb_strlen($logradouro,'utf8') < 30){
            while(mb_strlen($logradouro,'utf8') < 30){
                $logradouro = $logradouro . ' ';
            }
        }

        //limita 5 caracteres se menor preencher o restante com brancos
        $numeroresidencia = substr($ficha[0][ras_numero],0,5);
        if($numeroresidencia == "") $numeroresidencia = $getdom[0][dom_numero];
        else if(strlen($numeroresidencia) < 5){
            while(strlen($numeroresidencia) < 5){
                $numeroresidencia = '0'.$numeroresidencia;
            }
        }


        //$bairro = $xb[usu_end_bairro];
        if($getbai[0][usu_end_bairro] != "" && $getbai[0][usu_end_bairro] !="0" ) $bairro = $getbai[0][usu_end_bairro];
        else if ($getbaidom[0][bai_nome] != "") $bairro = $getbaidom[0][bai_nome];
        else $bairro = 'CENTRO                        ';

        if(mb_strlen($bairro) < 30){
            while(mb_strlen($bairro) < 30){
                $bairro = $bairro . ' ';
            }
        }else if(mb_strlen($bairro)>30){
            $bairro = substr($bairro,0,30);
        }

        //echo "<pre>";print_r($getrua);die();
        $tipologradouro = trim($getrua[0][co_tipo_logradouro]);
        if(strlen($tipologradouro) < 3){
            while(strlen($tipologradouro) < 3){
                $tipologradouro = '0'.$tipologradouro;
            }
        }
        if($tipologradouro == "") $tipologradouro = '006                                      ';
        if(strlen($tipologradouro) < 41){
            while(strlen($tipologradouro) < 41){
                $tipologradouro = $tipologradouro . ' ';
            }
        }else if(strlen($tipologradouro)>41){
            $tipologradouro = substr($tipologradouro,0,41);
        }
        //validar caracteres se vazio 11 caracteres telefone colocar um 0 a esquerda
        //retirar caracteres especiais dos telefones

        //pega telefone
        $gettelefones = $this->getDefaultAdapter()->query(
            "SELECT usuario.usu_celular , usuario.usu_fone from usuario
            WHERE usuario.usu_cartao_sus = '$ras_cns_paciente'
            "
        )->fetchAll();

        $ras_telefone = $ficha[0][ras_telefone];
        if($ras_telefone==""){
            $ras_telefone = $gettelefones[0][usu_fone];
        }
        $ras_telefone = preg_replace("/[^0-9]/", "", $ras_telefone);
        if(strlen($ras_telefone) > 11) $ras_telefone = substr($ras_telefone,2,12);
        if(strlen($ras_telefone) < 11){
        	while(strlen($ras_telefone)<11){
        		$ras_telefone = '0'.$ras_telefone;
        	}
        }
        if($ras_telefone=="00000000000") $ras_telefone = "44999999999";


        $ras_celular = $ficha[0][ras_celular];
        if($ras_celular==""){
            $ras_celular = $gettelefones[0][usu_celular];
        }
        $ras_celular = preg_replace("/[^0-9]/", "", $ras_celular);
        if(strlen($ras_celular) > 11) $ras_celular = substr($ras_celular,2,12);
        if(strlen($ras_celular) < 11){
        	while(strlen($ras_celular)<11){
        		$ras_celular = '0'.$ras_celular;
        	}
        }
        if($ras_celular=="00000000000") $ras_celular = "44999999999";


        return $f = $ficha[0][ras_codlinha_ad] . $ficha[0][ras_uf] . $pegaanomes . $ficha[0][ras_cnes] . $ras_cns_paciente . $rasdatax . $ras_val_fin . $nomepaciente . $ras_prontuario . $nomemae . $logradouro . $numeroresidencia . $complemento . $ras_cep . $ras_ibge_mun. $ras_datanasc . $ras_sexo . $raca . $responsavel . $nacionalidade . '    ' . $ras_telefone .  $ras_celular . $ras_motivosaida . $ras_data_obito_alta . $ras_cidp . $ras_cids1 . $ras_cids2 . $ras_cids3 . $ras_cidca . $ras_carater . $ras_origem . $ficha[0][ras_cobertura_esf] . $ras_cnes_esf . $ras_total_acoes . $ras_destino . $ficha[0][ras_org] . $ras_situacao_rua . $ficha[0][ras_usu_droga] . $ras_usu_tipo_droga . $ras_autorizacao .$bairro .$tipologradouro .'      ' . $quebra;   
    }

    public function validaAcoes($acoes){
        $quebra = chr(13) . chr(10);

        //valida data
        $explode1 = explode("-", $acoes[ras_val_ini]);
        $tbProntAno1 = $explode1[0];
        $tbProntMes1 = $explode1[1];
        $tbProntDia1 = $explode1[2];
        $ras_val_ini = $tbProntAno1 . $tbProntMes1 . $tbProntDia1;
        
        $explode2 = explode("-", $acoes[ras_dataexe]);
        $tbProntAno2 = $explode2[0];
        $tbProntMes2 = $explode2[1];
        $tbProntDia2 = $explode2[2];
        $ras_dataexe = $tbProntAno2 . $tbProntMes2 . $tbProntDia2;

        //validar 3 caracteres maximo ou preencher com brancos
        $ras_servico = $acoes[ras_servico];
        if(strlen($ras_servico) < 3 ){
            while(strlen($ras_servico) < 3){
                $ras_servico = $ras_servico . ' ' ;
            }
        }

        $ras_class = trim($acoes[ras_class]);
        if($ras_class == "") $ras_class = "002";
        if(strlen($ras_class) < 3 ){
            while(strlen($ras_class) < 3){
                $ras_class = '0' . $ras_class ;
            }
        }
        //validar quantidade maximo 6 caracteres preencher com 0 a esquerda
        $ras_qnt = $acoes[ras_qnt];
        if(strlen($ras_qnt) < 6 ){
            while(strlen($ras_qnt) < 6){
                $ras_qnt = '0' . $ras_qnt ;
            }
        }

        $ras_cbos_usr = $acoes[ras_cbos_usr];
        if(strlen($ras_cbos_usr) < 6 ){
            while(strlen($ras_cbos_usr) != 6){
                $ras_cbos_usr = '0' . $ras_cbos_usr ;
            }
        }else if(strlen($ras_cbos_usr) > 6 ){
            substr($acoes[ras_cbos_usr],0,6);
        }

        $ras_servico = $acoes[ras_servico];


        return $a = $acoes[ras_codlinha_ad_acoes] . $acoes[ras_coduf] . $acoes[ras_anomes] . $acoes[ras_cnes] . $acoes[ras_cns] . $ras_val_ini . $acoes[ras_acao] . $ras_cbos_usr . $acoes[ras_cns_usr] . $ras_dataexe . $ras_servico . $ras_class . $ras_qnt . $acoes[ras_org] . $acoes[ras_local_realizacao] . $acoes[ras_filler] . chr(13) . chr(10);
    }


    public function geraArquivo($msgcbc,$msg,$data)
    {

        $tbConfig = new Application_Model_Configuracao();
        $ibge = $tbConfig->getConfig("CID_CODIGO_IBGE");

        $meses = array("01"=>"JAN", "02"=>"FEV", "03"=>"MAR", "04"=>"ABR", "05"=>"MAI", "06"=>"JUN", "07"=>"JUL", "08"=>"AGO", "09"=>"SET", "10"=>"OUT", "11"=>"NOV", "12"=>"DEZ");
        $data = explode("/", $data);
        //echo "<pre>";print_r($data[0]);die();
        //echo "<pre>";print_r($meses[$data[0]]);die();


        $path = $_SESSION["root"]."WebSocialSaude/zf/public/arqs/";
        
        $arq = $this->criaArquivo("AA".$ibge, $msgcbc,$msg, $path, "." . $meses[$data[0]]);

        //file_put_contents($filename, var_export($$msg, true));

        $nomeDoArquivo = "AA".$ibge.".".$meses[$data[0]];
        

        $this->downloadRaas($path, $nomeDoArquivo);
    }

    public function downloadRaas($path, $nomeDoArquivo ){
        $link = $path.$nomeDoArquivo;
        header("Content-Disposition: attachment; filename=".$nomeDoArquivo."");
        header("Content-Type: application/plain");
        readfile($link);
        
        //NECESSARIO DESABILITAR A VIEW...!!! OU O ARQUIVO IRA IMPRIMIR UM ERRO ZEND !!!!!
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function criaArquivo($nome,$msgcbc ,$msg, $path = "./", $ext = ".txt") {
        if (!is_dir($path)) {
            die($path);
            return "DIR '$path' nao existe";
        }

        $completePath = $path.$nome.$ext; 

        $fp = fopen($path.$nome.$ext,"w");
        //file_put_contents($nome, var_export($msg, true));
        fwrite($fp,$msgcbc);

        foreach ($msg as $msg) {
            fwrite($fp,$msg);
        }

        fclose($fp);
    }

}