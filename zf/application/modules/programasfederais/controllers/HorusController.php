<?php

class Programasfederais_HorusController extends Zend_Controller_Action {
    
    public function init(){
        ini_set('memory_limit','-1');
        set_time_limit(100000000000);
    }
    
    public function indexAction(){
        $this->view->title = "HORUS - Exportação de Movimentações";
    }
    
    public function usuariosAction(){
        $this->view->title = "HORUS - Listagem de Usuários";
        $tbHorCad = new Application_Model_HorusCadastro();
        $this->view->dados = $tbHorCad->listaUsuariosHorus();
    }
    
    public function cadastroAction(){
        $this->view->title = "HORUS - Cadastro";
        $codCad = $this->_getParam("id");
        if ($codCad) {
            $tbHorCad = new Application_Model_HorusCadastro();
            $this->view->dados = $tbHorCad->getDadosUsuarioHorus($codCad);
        }   
    }
    
    // Salavando o cadastro do municipio, somente um ativo
    public function cadastroSalvarAction(){
        $this->view->headScript()->appendFile($this->view->baseUrl()."/public/js/programasfederais/horus/cadastro.js");
        $tbHorCad = new Application_Model_HorusCadastro();
        if ($this->_request->getPost("hor_cad_ativo") == 0) {
            $this->insereCadastroAction();
        } else {
            if ($tbHorCad->getQtdUsuariosHorusAtivos()->qtd_usuativos == 0) {
                $this->insereCadastroAction();
            } else {
                $this->view->erro = "Máximo de apenas 1 usuário ativo!";
                $this->render("cadastro");
            }
        }
    }
    
    public function insereCadastroAction(){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosCad = array(
            "hor_cad_login" => $this->_request->getPost("hor_cad_login"),
            "hor_cad_senha" => $this->_request->getPost("hor_cad_senha"),
            "hor_cad_ambiente" => $this->_request->getPost("hor_cad_ambiente"),
            "hor_cad_ativo" => $this->_request->getPost("hor_cad_ativo")
        );
        if ($this->_request->getPost("hor_cad_codigo")){
            $dadosCad["hor_cad_codigo"] = $this->_request->getPost("hor_cad_codigo");
        }
        $tbHorCad->salvar($dadosCad);
        $this->_redirect("programasfederais/horus/usuarios");
    }
    
    public function excluirUsuarioAction(){
        $tbHorCad = new Application_Model_HorusCadastro();
        $codUsu = $this->_request->getPost("hor_cad_codigo");
        $tbHorCad->excluiUsuarioHorus($codUsu);
    }
    
    // Função Responsavel por chamar a tela que informa o mês de exportação e envia por jQuery para as movimentações
    public function informaMesDeExportacaoAction(){
        $this->_helper->layout->disableLayout();
    }
    
    // Função responsável pela conexão ao webservice
    protected function conexaoWebService(){
        try {
            $tbConfig = new Application_Model_Configuracao();
            $tbHorCad = new Application_Model_HorusCadastro();
            $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
            // Verifica em que modo é realizada a execução
            if ($tbConfig->getConfig("MODO_PRODUCAO_HORUS") == 1){
                //die("PRODUÇÃO");
                if($tbConfig->getConfig("PROXY_PORTA") != "" && $tbConfig->getConfig("PROXY_ENDERECO") != "" && $tbConfig->getConfig("PROXY_SENHA") != "" && $tbConfig->getConfig("PROXY_USUARIO") != ""){
                    $client = new SoapClient("http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl", array(
                        "proxy_host"     => $tbConfig->getConfig("PROXY_ENDERECO"),
                        "proxy_port"     => $tbConfig->getConfig("PROXY_PORTA"),
                        "proxy_login"    => $tbConfig->getConfig("PROXY_USUARIO"),
                        "proxy_password" => $tbConfig->getConfig("PROXY_SENHA"),
                        "login"          => $dadosHorCad->hor_cad_login,
                        "password"       => $dadosHorCad->hor_cad_senha,
                        "exceptions"     => "0",
                        "trace"          =>true
                    ));
                } else {
                    $client = new SoapClient("http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl", array(
                        "login"          => $dadosHorCad->hor_cad_login,
                        "password"       => $dadosHorCad->hor_cad_senha,
                        "exceptions"     => "0",
                        "trace"          =>true
                    ));
                }
            } else {
                if($tbConfig->getConfig("PROXY_PORTA") != "" && $tbConfig->getConfig("PROXY_ENDERECO") != "" && $tbConfig->getConfig("PROXY_SENHA") != "" && $tbConfig->getConfig("PROXY_USUARIO") != ""){
                    $client = new SoapClient("http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl", array(
                        "proxy_host"     => $tbConfig->getConfig("PROXY_ENDERECO"),
                        "proxy_port"     => $tbConfig->getConfig("PROXY_PORTA"),
                        "proxy_login"    => $tbConfig->getConfig("PROXY_USUARIO"),
                        "proxy_password" => $tbConfig->getConfig("PROXY_SENHA"),
                        "login"          => $dadosHorCad->hor_cad_login,
                        "password"       => $dadosHorCad->hor_cad_senha,
                        "exceptions"     => "0",
                        "trace"          =>true
                    ));
                } else {
                    $client = new SoapClient("http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl", array(
                        "login"          => $dadosHorCad->hor_cad_login,
                        "password"       => $dadosHorCad->hor_cad_senha,
                        "exceptions"     => "0",
                        "trace"          =>true
                    ));
                }
            }
            return $client;
         } catch (Exception $exc) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->view->dados = array("error" => $exc->getMessage());
            return $this->render("dados", NULL, TRUE);
         }
    }
    
    protected function getDataInicialExp($mesExp, $anoExp){
        $tbConfig = new Application_Model_Configuracao();
        $diaExpHorus = $tbConfig->getConfig("DIA_EXPORTACAO_HORUS");
        // Validação Mês de Janeiro
        
        $dtInicialExp = $diaExpHorus."/".$mesExp."/".$anoExp;
        
        return $dtInicialExp;
    }
    
    protected function getDataFinalExp($mesExp, $anoExp){
        $tbConfig = new Application_Model_Configuracao();
        $diaExpHorus = $tbConfig->getConfig("DIA_EXPORTACAO_HORUS");
        $data = $anoExp."-".$mesExp."-".$diaExpHorus;
        $ultimo_dia = date("t", strtotime($data));
        $dtFinalExp = $ultimo_dia."/".$mesExp."/".$anoExp;
        return $dtFinalExp;
    }

    // Função responsável pelo envio das movimentações de entrada para o HORUS
    public function geraMovimentacaoEntradaAction(){
        $tpXml = 'E';
        $mesExp = $this->_request->getPost("mesExp");
        $anoExp = $this->_request->getPost("anoExp");
        $dtInicioExpHorus = $this->getDataInicialExp($mesExp, $anoExp);
        $dtFinalExpHorus = $this->getDataFinalExp($mesExp, $anoExp);

        
        
        // print_r($this->validaNumRegistros($tpXml,$dtInicioExpHorus,$dtFinalExpHorus));
        // die();

        if($this->validaConfiguracao() == true) {
        //die("asdf".$this->validaNumRegistros($tpXml,$dtInicioExpHorus,$dtFinalExpHorus));
            if ($this->validaNumRegistros($tpXml,$dtInicioExpHorus,$dtFinalExpHorus) > 0) {
                if ($this->validaCabecalhoXml()==true){
                    $this->_helper->layout->disableLayout();
                    $xml = $this->geraXml($tpXml, $mesExp, $anoExp);
                    $result = $this->executaMovimentacao($xml);
                    //echo "<pre>" . print_r($result, 1);
                    $respWebServ = $this->getResultado($result);
                    $this->atualizaDadosMovEntradaAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus);
                    $this->view->dados = $respWebServ;
                } else {
                    $this->view->dados = "errocabecalho";
                }
            } else { 
                $this->view->dados = "erronumregistro";
            }
        } else {
            $this->view->dados = "erroconfiguracao";
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    // Função responsável por atualizar o status e outros dados depois do envio
    public function atualizaDadosMovEntradaAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus){
        // Se resposta do webservice for inteira =  num do protocolo, atualiza
        if ((int)$respWebServ) {
            $tbHorDados = new Application_Model_HorusDados();
            $dados = array(
                "hor_dad_dtenvio" => "'".date("Y-m-d H:i:s")."'",
                "hor_dad_numprotocolo_envio" => "$respWebServ", 
                "hor_dad_status_envio" => "T",
                "hor_dad_nome_respenvio" => $this->getDadosResponsavelPeloEnvio()->usr_nome  
            );
            
            $tbHorDados->atualizaDadosMovEntradas($dados, $dtInicioExpHorus, $dtFinalExpHorus); 
        }
    }
    
    // Função responsável pelo envio das movimentações de saída para o HORUS
    public function geraMovimentacaoSaidaAction(){
        $tpXml = 'S';
        $mesExp = $this->_request->getPost("mesExp");
        $anoExp = $this->_request->getPost("anoExp");

        
        $dtInicioExpHorus = $this->getDataInicialExp($mesExp, $anoExp);
        $dtFinalExpHorus = $this->getDataFinalExp($mesExp, $anoExp);
        
        if($this->validaConfiguracao() == true) {
            if ($this->validaNumRegistros($tpXml,$dtInicioExpHorus,$dtFinalExpHorus) > 0) {
                if ($this->validaCabecalhoXml()==true){
                    $this->_helper->layout->disableLayout();
                    $xml = $this->geraXml($tpXml,$mesExp, $anoExp);
                    $result = $this->executaMovimentacao($xml);
                    $respWebServ = $this->getResultado($result);
                    $this->atualizaDadosMovSaidaAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus);
                    $this->view->dados = $respWebServ;
                } else {
                    $this->view->dados = "errocabecalho";
                }
            } else { 
                $this->view->dados = "erronumregistro";
            }
        } else {
            $this->view->dados = "erroconfiguracao";
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    // Função responsável por atualizar o status e outros dados depois do envio
    public function atualizaDadosMovSaidaAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus){
        // Se resposta do webservice for inteira =  num do protocolo, atualiza
        if ((int)$respWebServ) {
            $tbHorDados = new Application_Model_HorusDados();
            $dados = array(
                "hor_dad_dtenvio" => "'".date("Y-m-d H:i:s")."'",
                "hor_dad_numprotocolo_envio" => "$respWebServ", 
                "hor_dad_status_envio" => "T",
                "hor_dad_nome_respenvio" => $this->getDadosResponsavelPeloEnvio()->usr_nome  
            );
            $tbHorDados->atualizaDadosMovSaidas($dados, $dtInicioExpHorus, $dtFinalExpHorus); 
        }
    }
    
    // Função responsável pelo envio das movimentações de dispensação para o HORUS
    public function geraMovimentacaoDispensacaoAction(){
        $mesExp = $this->_request->getPost("mesExp");
        $anoExp = $this->_request->getPost("anoExp");
        $dtInicioExpHorus = $this->getDataInicialExp($mesExp, $anoExp);
        $dtFinalExpHorus = $this->getDataFinalExp($mesExp, $anoExp);

        // echo $dtInicioExpHorus;
        // echo $dtFinalExpHorus;
        // die();
        if($this->validaConfiguracao() == true) {
            if ($this->validaNumRegistros($tpXml = 'D',$dtInicioExpHorus,$dtFinalExpHorus) > 0) {
                if ($this->validaCabecalhoXml()==true){
                    $this->_helper->layout->disableLayout();
                    $xml = $this->geraXml($tpXml = 'DP',$mesExp, $anoExp);
                    $result = $this->executaMovimentacao($xml); 
                    $respWebServ = $this->getResultado($result);
                    $this->atualizaDadosMovDispensacaoAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus);
                    $this->view->dados = $respWebServ;
                } else {
                    $this->view->dados = "errocabecalho";
                }
            } else { 
                $this->view->dados = "erronumregistro";
            }
        } else {
            $this->view->dados = "erroconfiguracao";
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    // Função responsável por atualizar o status e outros dados depois do envio
    public function atualizaDadosMovDispensacaoAction($respWebServ,$dtInicioExpHorus,$dtFinalExpHorus){
        // Se resposta do webservice for inteira =  num do protocolo, atualiza
        if ((int)$respWebServ) {
            $tbHorDados = new Application_Model_HorusDados();
            $dados = array(
                "hor_dad_dtenvio" => "'".date("Y-m-d H:i:s")."'",
                "hor_dad_numprotocolo_envio" => "$respWebServ", 
                "hor_dad_status_envio" => "T",
                "hor_dad_nome_respenvio" => $this->getDadosResponsavelPeloEnvio()->usr_nome  
            );
            $tbHorDados->atualizaDadosMovDispensacaoAction($dados, $dtInicioExpHorus, $dtFinalExpHorus); 
        }
    }
    
    // Executa Movimentação no webservice e devolve o retorno
    protected function executaMovimentacao($xml){
        $tbConfig = new Application_Model_Configuracao();
        // Verifica em que modo é realizada a execução
        if ($tbConfig->getConfig("MODO_PRODUCAO_HORUS") == 1){
            $server = "http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        } else {
            $server = "http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        }
        $client = $this->conexaoWebService();
        $result = $client->__doRequest($xml, $server, "recebeDados", "1.2");
        return $result;
    }
    
    // Verifica se existe registros a serem importado, se não existir retorna erro
    protected function validaNumRegistros($tpXml,$dtInicioExpHorus,$dtFinalExpHorus){
        $tbHorDad = new Application_Model_HorusDados();

        $numRegExp = $tbHorDad->getNumRegistrosAExportar($tpXml,$dtInicioExpHorus,$dtFinalExpHorus)->total_ent;

         //die($numRegExp);
        return $numRegExp;
    }
    
    // Valida se a configuração de data de início da exportação horus esta ativa
    protected function validaConfiguracao(){
        $tbConfig = new Application_Model_Configuracao();
        if($tbConfig->getConfig("DIA_EXPORTACAO_HORUS")){
            return true;
        } else {
            return false;
        }
    }
    
    public function validaCabecalhoXml(){
        $tbUsu = new Application_Model_Usuarios();
        $usr_codigo = $this->getDadosResponsavelPeloEnvio()->usr_codigo;
        $respEnvio = $this->getDadosResponsavelPeloEnvio()->usr_nome;
        $uniCodIbge = substr($tbUsu->getDadosCidadeUsrLogado($usr_codigo)->uni_codigo_ibge,0,6);
        if ($usr_codigo != "" && $respEnvio != "" && $uniCodIbge != "") {
            return true;
        } else {
            return false;
        }
    }
    
    // Gera o XML para exportar os dados pro HORUS
    protected function geraXml($tpXml,$mesExp, $anoExp){
        $cabXml = $this->geraCabecalhoXml($tpXml);
        $contXml = $this->geraConteudoXml($tpXml,$mesExp, $anoExp);
        $rodXml = $this->geraRodapeXml();
        //Une o Cabeçalho, Contéudo, Rodapé e criptografa pra 64 bits
        $xmlBase64 = base64_encode($cabXml.$contXml.$rodXml);
        // Envelopa o XML com o SOAP ENVELOPE e retorna o XML completo p/ ENVIO
        $xml = $this->geraConteudoSoapXml($xmlBase64);

        // echo "<pre>";
        // print_r($xml);
        // die();
        return $xml;
    }
    
    // Gera Cabeçalho para os 3 tipos de XML
    protected function geraCabecalhoXml($tpXml) {
        $usr_codigo = $this->getDadosResponsavelPeloEnvio()->usr_codigo;
        $respEnvio = $this->getDadosResponsavelPeloEnvio()->usr_nome;
        // Somente 6 digitos, por isso da validação
        $tbUsu = new Application_Model_Usuarios();
        //$uniCodIbge = substr($this->getDadosResponsavelPeloEnvio()->uni_codigo_ibge,0,6);
        $uniCodIbge = substr($tbUsu->getDadosCidadeUsrLogado($usr_codigo)->uni_codigo_ibge,0,6);
        // Cabeçalho XML
        $CabXml = "<root>
                    <identificador>
                        <stEsferaEnvio>M</stEsferaEnvio>
                        <coMunicipioIbge>$uniCodIbge</coMunicipioIbge>
                        <noUsuario>$respEnvio</noUsuario>
                        <tpXML>$tpXml</tpXML>
                        <stHorus>N</stHorus>
                    </identificador>";
        return $CabXml;
    }
    
    // Gera conteúdo para os 3 tipos de XML
    protected function geraConteudoXml($tpXml,$mesExp, $anoExp){
        $contXml = "";
        $tbHorDad = new Application_Model_HorusDados();

        // echo "mes: ".$mesExp;
        // echo "ano: ".$anoExp;
        // die();

        $dtInicioExpHorus = $this->getDataInicialExp($mesExp, $anoExp);
        $dtFinalExpHorus = $this->getDataFinalExp($mesExp, $anoExp);
        switch ($tpXml) {
            // Conteúdo XML ENTRADAS
            case E:
                $dadosContXml = $tbHorDad->listaMovEntradasParaExportar($dtInicioExpHorus,$dtFinalExpHorus);
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<registro>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtAdquirida>".trim($qtAdqu)."</qtAdquirida>
                                    <dtRecebimento>".trim($cont->hor_dad_dtrecebimentoprod)."</dtRecebimento>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                    <tpMovimentacao>".trim($cont->hor_dad_tpmovimentacao)."</tpMovimentacao>
                                </registro>";
                }
                //echo "<pre>".print_r($contXml,1)."</pre>";
                return $contXml;
            break;
            // Conteúdo XML SAÍDAS
            case S:
                $dadosContXml = $tbHorDad->listaMovSaidasParaExportar($dtInicioExpHorus,$dtFinalExpHorus);
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<registro>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtSaida>".trim($qtAdqu)."</qtSaida>
                                    <dtSaida>".trim($cont->hor_dad_dtrecebimentoprod)."</dtSaida>
                                    <tpMovimentacao>".trim($cont->hor_dad_tpmovimentacao)."</tpMovimentacao>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                </registro>";
                }
                return $contXml;
            break;
            // Conteúdo XML DISPENSAÇÕES
            case DP:
                $dadosContXml = $tbHorDad->listaMovDispensacoesParaExportar($dtInicioExpHorus,$dtFinalExpHorus);
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<dispensacao>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtMedicamentoDispensada>".trim($qtAdqu)."</qtMedicamentoDispensada>
                                    <dtDispensacao>".trim($cont->hor_dad_dtrecebimentoprod)."</dtDispensacao>
                                    <nuCnsPaciente>".trim($cont->hor_dad_nucnspaciente)."</nuCnsPaciente>
                                </dispensacao>";
                }
                return $contXml;
            break;
        }
    }
    
    // Gera o rodapé para os 3 tipos de XML
    protected function geraRodapeXml(){
        $rodXml = "</root>";
        return $rodXml;
    }
    
    // Gera XML completo já com o soap envelope para envio
    protected function geraConteudoSoapXml($xmlBase64){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:recebeDados>
                                <source>
                                    $xmlBase64
                                </source>
                            </hor:recebeDados>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    // Pega o dia de exportação do Horus
    protected function getDiaExportacaoHorusAction(){
        $tbConfig = new Application_Model_Configuracao();
        $diaExpHorus = $tbConfig->getConfig("DIA_EXPORTACAO_HORUS");
        return $diaExpHorus;
    }
    
    // Verifica quem é o responsavel pelo envio e encaminha para o banco
    protected function getDadosResponsavelPeloEnvio(){
        $tbUsu = new Application_Model_Usuarios();
        $dadosUsu = $tbUsu->getUsrAtual();
        return $dadosUsu; 
    }
    
    // Verifica se os dados foram enviado ou não e retorna o resultado
    protected function getResultado($result){
        //echo "<pre>".print_r($result);die();
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        $resultado = trim($DOMDocument->getElementsByTagName('mensagem-global')->item(0)->nodeValue);
        if (substr_count($resultado,"Dados recebidos com sucesso!") >= 1) {
            return $this->retornaNumeroDoProtocolo($result);
        } else {
            return "errohorus-".$resultado;
        }
        
    }

    // Retorna o código do protocolo gerado pelo WebService do HORUS
    protected function retornaNumeroDoProtocolo($result){
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        $numProtocolo = $DOMDocument->getElementsByTagName('horus-ws-numero-protocolo')->item(0)->nodeValue;
        return $numProtocolo;
    }
    
    public function consultaDadosHorusAction(){
        $this->view->title = "Consulta de dados enviados para o Horus";
    }
    
    public function enviaConsultaDadosHorusAction(){
        $numProtocolo = $this->_request->getPost("num_protocolo",FALSE);
        $this->view->numProtocolo = $numProtocolo; 
        $this->view->qtdDadosEnviado = $this->getDadosEnviadosComSucessoAction($numProtocolo);
        $this->view->dadosInconsistente = $this->getDadosEnviadosComFalhaAction($numProtocolo);
        $this->render("consulta-dados-horus");
    }
    
    public function getDadosEnviadosComSucessoAction($numProtocolo){
        $this->view->title = "Consulta de dados enviados para o Horus";
        $tbConfig = new Application_Model_Configuracao();
        // Verifica em que modo é realizada a execução
        if ($tbConfig->getConfig("MODO_PRODUCAO_HORUS") == 1){
            $server = "http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        } else {
            $server = "http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        }
        $client = $this->conexaoWebService();
        $xml = $this->geraXmlConsultaDadosDefinitivoMun($numProtocolo);
        $result = $client->__doRequest($xml, $server, "consultarDadosDefinitivosPorMunicipio", "1.2");
        return $this->leQtdRetornoSucesso($result);
    }
    
    // Retorna a quantidade de produtos que o webservice registro como enviado
    protected function leQtdRetornoSucesso($result){
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        $qtdSucesso = $DOMDocument->getElementsByTagName('horus-ws-consulta-dados')->length;
        return $qtdSucesso;
    }
    
    // Gera XML completo já com o soap envelope para envio
    protected function geraXmlConsultaDadosDefinitivoMun($numProtocolo){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:consultarDadosDefinitivosPorMunicipio>
                                <numeroProtocolo>$numProtocolo</numeroProtocolo>
                            </hor:consultarDadosDefinitivosPorMunicipio>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    // Gera XML completo já com o soap envelope para envio (AllDados)
    protected function geraXmlConsultaAllDados($numProtocolo){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:consultarAllDadosPorMunicipio>
                                <numeroProtocolo>$numProtocolo</numeroProtocolo>
                            </hor:consultarAllDadosPorMunicipio>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    public function getDadosEnviadosComFalhaAction($numProtocolo){
        $tbConfig = new Application_Model_Configuracao();
        // Verifica em que modo é realizada a execução
        if ($tbConfig->getConfig("MODO_PRODUCAO_HORUS") == 1){
            $server = "http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        } else {
            $server = "http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        }
        $client = $this->conexaoWebService();
        $xml = $this->geraXmlConsultaInconsistencia($numProtocolo);
        $result = $client->__doRequest($xml, $server, "consultarInconsistenciasPorMunicipio", "1.2");
        $qtdFalha = $this->leQtdRetornoFalha($result);
        return $this->leRetornoInconsistentes($result,$qtdFalha);
    }
    
    
    // Gera XML completo já com o soap envelope para envio
    protected function geraXmlConsultaInconsistencia($numProtocolo){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:consultarInconsistenciasPorMunicipio>
                                <numeroProtocolo>$numProtocolo</numeroProtocolo>
                            </hor:consultarInconsistenciasPorMunicipio>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    // Retorna o código do protocolo gerado pelo WebService do HORUS
    protected function leQtdRetornoFalha($result){
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        $qtdFalha = $DOMDocument->getElementsByTagName('horus-ws-inconsistencia-arquivo')->length;
        return $qtdFalha;
    }
    
    public function leRetornoInconsistentes($result,$qtdFalha){
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        for ($i=0; $i<$qtdFalha; $i++) {
            $dados[] = array(
                "dsCampo" => $DOMDocument->getElementsByTagName('dsCampo')->item($i)->nodeValue,
                "dsMotivo" => $DOMDocument->getElementsByTagName('dsMotivo')->item($i)->nodeValue,
                "tipoXml" => $DOMDocument->getElementsByTagName('tipoXml')->item($i)->nodeValue,
                "valorInconsistente" => $DOMDocument->getElementsByTagName('valorInconsistente')->item($i)->nodeValue
            );
        }
        $dados["qtdFalha"] = $qtdFalha;
        return $dados;
    }
    
    public function leRetornoConsistentes($result,$qtdSucesso){
        $DOMDocument = new DOMDocument('1.0','UTF-8');
        @$DOMDocument->loadXML($result);
        for ($i=0; $i<$qtdSucesso; $i++) {
            $dados[] = array(
                "tipoXml" => $DOMDocument->getElementsByTagName('tipoXml')->item($i)->nodeValue,
                "coUnidadeCnes" => $DOMDocument->getElementsByTagName('coUnidadeCnes')->item($i)->nodeValue,
                "vlItem" => $DOMDocument->getElementsByTagName('vlItem')->item($i)->nodeValue,
                "tpProduto" => $DOMDocument->getElementsByTagName('tpProduto')->item($i)->nodeValue,
                "dtValidade" => $DOMDocument->getElementsByTagName('dtValidade')->item($i)->nodeValue,
                "dtValidade" => $DOMDocument->getElementsByTagName('dtValidade')->item($i)->nodeValue    
            ); 
        }
        return $dados;
    }
    
    public function enviaDeletaProtocoloAction(){
        $tbConfig = new Application_Model_Configuracao();
        // Verifica em que modo é realizada a execução
        if ($tbConfig->getConfig("MODO_PRODUCAO_HORUS") == 1){
            $server = "http://aplicacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        } else {
            $server = "http://aplicacao-homologacao.saude.gov.br/horus-ws-basico/RecebeDadosWS?wsdl";
        }
        $numProtocolo = $this->_request->getParam("numProtocolo");
        // Busca dados para exclusão do protocolo
        $tbHorDad = new Application_Model_HorusDados();
        $dadosResp = $tbHorDad->getDadosRespProtocolo($numProtocolo);
        $client = $this->conexaoWebService();
        $xml = $this->geraXmlDeletaProtocolo($dadosResp);
        $result = $client->__doRequest($xml, $server, "deletarDadosDefinitivos", "1.2");
        // echo "<pre>" . print_r($result, 1);
        // die();
        if ($result) {
            
        } else {
            $this->atualizaDadosProtocolo($numProtocolo);
        }
    }
    
    public function atualizaDadosProtocolo($numProtocolo){
        $tbHorDados = new Application_Model_HorusDados();
        $dados = array(
            "hor_dad_dtenvio" => "null",
            "hor_dad_numprotocolo_envio" => "null", 
            "hor_dad_status_envio" => "f",
            "hor_dad_nome_respenvio" => "null"  
        );
        $tbHorDados->atualizaDadosMovEntradas($dados, $numProtocolo); 
    }
    
    // Gera XML completo já com o soap envelope para envio
    protected function geraXmlDeletaProtocolo($dadosResp){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:deletarDadosDefinitivos>
                                <numeroProtocolo>".trim($dadosResp->hor_dad_numprotocolo_envio)."</numeroProtocolo>
                                <dataEnvio>".trim(date("d/m/Y",strtotime($dadosResp->hor_dad_dtenvio)))."</dataEnvio>
                                <nomeUsuario>".trim($dadosResp->hor_dad_nome_respenvio)."</nomeUsuario>
                                <tpXml>".trim($dadosResp->hor_dad_tpxml)."</tpXml>
                            </hor:deletarDadosDefinitivos>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    public function listaProtocolosAction(){
        $this->view->title = "Listagem de protocolos enviados.";
        $tbHorDad = new Application_Model_HorusDados();
        $this->view->dados = $tbHorDad->listaProtocolos();
    }
    
    public function buscarProtocoloPorDataAction(){
        $data = $this->_request->getPost("data",FALSE);
        $tbHorDad = new Application_Model_HorusDados();
        $this->view->dados = $tbHorDad->listaProtocolosPorData($data);
        return $this->render("lista-protocolos");
    }
    
    /* ----------------------------------------------------------------------/
     * HORUS - MÉTODOS DE CONSULTA ATRAVÉS DOS CÓDIGOS DE PROTOCOLO          / 
     * ---------------------------------------------------------------------*/
    
    public function consultaPorProtocoloAction(){
        $this->view->title = "Consulta por Protocolo"; 
    }
    
    public function enviaConsultaPorProtocoloAction(){
        $this->view->title = "Consulta por Protocolo"; 
        $tbHorDados = new Application_Model_HorusDados();
        $numProtocolo = $this->_request->getPost("num_protocolo");
        $numProduto = $this->_request->getPost("num_produto");
        $this->view->numProtocolo = $numProtocolo;
        $this->view->dados = $tbHorDados->getConteudoPorProtocoloProduto($numProtocolo,$numProduto);
        $this->render("consulta-por-protocolo");
    }
    
    // Gera o XML para caso precise o suporte mande para o HORUS
    public function geraXmlPorProtocoloAction(){
        $numProtocolo = $this->_request->getParam("num_protocolo");
        $tbHorDad = new Application_Model_HorusDados();
        // Verifica se protocolo existe 
        if ($tbHorDad->verificaProtocolo($numProtocolo)->qtd_protocolo > 0) {
            $tpXml = $tbHorDad->getDadosCabecalhoXmlPorProtocolo($numProtocolo)->hor_dad_tpxml;
            if ($tpXml = 'D') { $tpXml = 'DP'; }
            $cabXml = $this->geraCabecalhoXmlPorProtocolo($numProtocolo);
            $contXml = $this->geraConteudoXmlPorProtocolo($numProtocolo,$tpXml);
            $rodXml = $this->geraRodapeXmlPorProtocolo();
            $xml = $cabXml.$contXml.$rodXml;
            // Salvando arquivo XML sem criptografia
            $fp = fopen('C://horus_'.$tpXml.'_'.$numProtocolo.'.xml', 'w+');
            fwrite($fp, $xml);
            fclose($fp);
            //Une o Cabeçalho, Contéudo, Rodapé e criptografa para XML 64 bits
            $xmlBase64 = base64_encode($cabXml.$contXml.$rodXml);
            // Envelopa o XML com o SOAP ENVELOPE e retorna o XML completo p/ ENVIO
            $xmlCript = $this->geraConteudoSoapXmlPorProtocolo($xmlBase64);
            // Salvando arquivo XML com criptografia
            $fp = fopen('C://horus_'.$tpXml.'64_'.$numProtocolo.'.xml', 'w+');
            fwrite($fp, $xmlCript);
            fclose($fp);
            // Enviando Mensagem para o Jquery
            $this->view->dados = "XML gerado com sucesso! <br /> Localize-o em C://horus_$tpXml.64_$numProtocolo.xml";
            return $this->render("dados",NULL,TRUE);
        } else {
            // Enviando Mensagem para o Jquery
            $this->view->dados = "ERRO! Falha ao baixar XML!";
            return $this->render("dados",NULL,TRUE);
        }
    }
    
    // Gera Cabeçalho para os 3 tipos de XML
    protected function geraCabecalhoXmlPorProtocolo($numProtocolo) {
        $tbUsu = new Application_Model_Usuarios();
        $tbHorDad = new Application_Model_HorusDados();
        $dadosCab = $tbHorDad->getDadosCabecalhoXmlPorProtocolo($numProtocolo);
        $usr_codigo = $this->getDadosResponsavelPeloEnvio()->usr_codigo;
        // Cabeçalho XML
        $CabXml = "<root>
                    <identificador>
                        <stEsferaEnvio>M</stEsferaEnvio>
                        <coMunicipioIbge>".trim(substr($tbUsu->getDadosCidadeUsrLogado($usr_codigo)->uni_codigo_ibge,0,6))."</coMunicipioIbge>
                        <noUsuario>".trim($dadosCab->hor_dad_nome_respenvio)."</noUsuario>
                        <tpXML>".trim($dadosCab->hor_dad_tpxml)."</tpXML>
                        <stHorus>N</stHorus>
                    </identificador>";
        return $CabXml;
    }
    
    // Gera conteúdo para os 3 tipos de XML
    protected function geraConteudoXmlPorProtocolo($numProtocolo,$tpXml){
        $contXml = "";
        $tbHorDad = new Application_Model_HorusDados();
        $dadosContXml = $tbHorDad->getConteudoXmlPorProtocolo($numProtocolo);
        switch ($tpXml) {
            // Conteúdo XML ENTRADAS
            case E:
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<registro>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtAdquirida>".trim($qtAdqu)."</qtAdquirida>
                                    <dtRecebimento>".trim($cont->hor_dad_dtrecebimentoprod)."</dtRecebimento>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                    <tpMovimentacao>".trim($cont->hor_dad_tpmovimentacao)."</tpMovimentacao>
                                </registro>";
                }
                return $contXml;
            break;
            // Conteúdo XML SAÍDAS
            case S:
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<registro>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtSaida>".trim($qtAdqu)."</qtSaida>
                                    <dtSaida>".trim($cont->hor_dad_dtrecebimentoprod)."</dtSaida>
                                    <tpMovimentacao>".trim($cont->hor_dad_tpmovimentacao)."</tpMovimentacao>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                </registro>";
                }
                return $contXml;
            break;
            // Conteúdo XML DISPENSAÇÕES
            case DP:
                foreach ($dadosContXml as $cont) {
                    // Validando 4 casas após a virgula
                    $vlItem = number_format($cont->hor_dad_vlitem,4,".","");
                    // Validando somente números inteiros
                    $qtAdqu = number_format($cont->hor_dad_qtd,0,".","");
                    // Valores de data no formato ano, mês, dia, formatado no postgres já
                    $contXml .= "<dispensacao>
                                    <coUnidadeCnes>".trim($cont->hor_dad_counidadecnes)."</coUnidadeCnes>
                                    <nuProduto>".trim($cont->hor_dad_nuproduto)."</nuProduto>
                                    <tpProduto>".trim($cont->hor_dad_tpproduto)."</tpProduto>
                                    <vlItem>".trim($vlItem)."</vlItem>
                                    <dtValidade>".trim($cont->hor_dad_dtvalidade)."</dtValidade>
                                    <nuLote>".trim($cont->hor_dad_nulote)."</nuLote>
                                    <qtMedicamentoDispensada>".trim($qtAdqu)."</qtMedicamentoDispensada>
                                    <dtDispensacao>".trim($cont->hor_dad_dtrecebimentoprod)."</dtDispensacao>
                                    <nuCnsPaciente>".trim($cont->hor_dad_nucnspaciente)."</nuCnsPaciente>
                                </dispensacao>";
                }
                return $contXml;
            break;
        }
    }
    
    // Gera o rodapé para os 3 tipos de XML
    protected function geraRodapeXmlPorProtocolo(){
        $rodXml = "</root>";
        return $rodXml;
    }
    
    // Gera XML completo já com o soap envelope para envio
    protected function geraConteudoSoapXmlPorProtocolo($xmlBase64){
        $tbHorCad = new Application_Model_HorusCadastro();
        $dadosHorCad = $tbHorCad->getDadosUsuarioAtivoHorus(); 
        $soapXml = "<?xml version='1.0'?>
                    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:hor='http://www.saude.gov.br/horus-ws-basico'>
                        <soapenv:Header>
                            <wsse:Security xmlns:wsse='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'>
                                <wsse:UsernameToken wsu:Id='UsernameToken-1' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                                   <wsse:Username>$dadosHorCad->hor_cad_login</wsse:Username>
                                   <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>$dadosHorCad->hor_cad_senha</wsse:Password>
                                </wsse:UsernameToken>
                            </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                            <hor:recebeDados>
                                <source>
                                    $xmlBase64
                                </source>
                            </hor:recebeDados>
                        </soapenv:Body>
                    </soapenv:Envelope>";
        return $soapXml;
    }
    
    public function editaDadosHorusAction(){
        $horDadCodigo = $this->_request->getPost("hor_dad_codigo");
        $tbHorDad = new Application_Model_HorusDados();
        $dados = $tbHorDad->getDadosPorCodigo($horDadCodigo);
        $this->view->dados = $dados; 
    }
    
    /* ----------------------------------------------------------------------/
     * FIM HORUS - MÉTODOS DE CONSULTA ATRAVÉS DOS CÓDIGOS DE PROTOCOLO      / 
     * ---------------------------------------------------------------------*/
    
    
}

?>
