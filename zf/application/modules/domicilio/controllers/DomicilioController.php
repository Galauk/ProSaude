<?php

class Domicilio_DomicilioController extends Zend_Controller_Action {

    public function init() {

        $this->view->title = "Cadastro de Domicilio";
        parent::init();
    }

    public function indexAction() {
        // die('oi');
        error_reporting(E_ALL);
        $tbBai = new Application_Model_Bairro();
        $this->view->itens = $tbBai->getBairros();
    }

    public function novoAction() {
        // die('oi pessoa');
        $dom_codigo = $this->_getParam("id")
;        $this->view->popup = $term = $this->_getParam("popup", 0);
        $tbTipoDom = new Application_Model_TbTipoDomicilio();
        $tbSituacaoMoradia = new Application_Model_TbSituacaoMoradia();
        $tbDist = new Application_Model_Distrito();
        $tbConf = new Application_Model_Configuracao();
        $tbCid = new Application_Model_Cidade();
        $tbDom = new Application_Model_Domicilio();
        $cid_codigo_ibge = $tbConf->getConfig("CID_CODIGO_IBGE");
        $this->view->cidade = $tbCid->getCidadePeloCodigoIbge($cid_codigo_ibge);
        $this->view->sitmoradia = $tbSituacaoMoradia->getDescricao();
        $this->view->tipodom = $tbTipoDom->getDescricao();
        $this->view->distritos = $tbDist->fetchAll();
        
        if ($dom_codigo){
            $this->view->dados = $tbDom->getDomicilio($dom_codigo);
        }
    }

    public function salvarAction() {
        $tbDom = new Application_Model_Domicilio();
        $tbUsu = new Application_Model_Usuario();
        $this->_helper->layout->disableLayout();

        if ($this->_request->getPost("sn", "") == 1) {
            $dom_numero = "0";
        } else {
            $dom_numero = $this->_request->getPost("dom_numero", "");
        }

        $dados = array(
            // "usu_codigo_responsavel" => $this->_request->getPost("usu_codigo", FALSE),

            "usu_codigo_responsavel" => $this->_request->getPost("codigoResponsavelFamiliar", FALSE),
            "tcadf_prontuario_familiar" => $this->_getParam("prontuarioFamiliar", FALSE),
            "rua_codigo" => mb_strtoupper($this->_request->getPost("rua_codigo", FALSE), "UTF-8"),
            "dom_numero" => $dom_numero,
            "dom_complemento" => $this->_request->getPost("dom_complemento", FALSE),
            "tb_tipo_domicilio" => $this->_request->getPost("tb_tipo_domicilio", FALSE),
            "tb_situacao_moradia" => $this->_request->getPost("tb_situacao_moradia", FALSE),
            "dom_localizacao" => $this->_request->getPost("dom_localizacao", FALSE),
            "dom_ponto_referencia" => $this->_request->getPost("dom_ponto_referencia", FALSE),
            "dom_telefone" => $this->_request->getPost("dom_telefone", FALSE)
        );
        
        // echo "<pre>";print_r($dados);die();

        if ($this->_request->getPost("dom_codigo", false))
            $dados["dom_codigo"] = $this->_request->getPost("dom_codigo", false);

        try {
            $dom_codigo = $tbDom->salvar($dados);
            
            if(!$this->_request->getPost("dom_complemento", FALSE)){
                $tbDom->deletaComplementoDoDomicilio($dom_codigo);
            }
        
            // $data_usu = array("usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            //     "dom_codigo" => $dom_codigo);
            // $tbUsu->salvar($data_usu);

            $this->view->dados = array("dom_codigo" => $dom_codigo);
        } catch (Exception $ex) {

            $this->view->dados = array("msg" => $ex->getMessage());
        }

        return $this->render("dados", null, true);
    }

    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $tbBairro = new Application_Model_Bairro();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->itens = $tbBairro->pesquisar($this->view->busca);
            $this->render("index");
        } else {
            $this->_redirect("/domicilio/area/index");
        }
    }

    public function verificaVinculoAction() {
        $tbBai = new Application_Model_Domicilio();
        $rua_codigo = $this->_getParam("rua_codigo", false);
        $dom_numero = $this->_getParam("dom_numero", false);
        $codigoResponsavelFamiliar = $this->_getParam("codigoResponsavelFamiliar", false);
        $dom_complemento = $this->_getParam("dom_complemento", false);

        // die(
        //     "Rua codigo :".$rua_codigo.
        //     "DOM CODIGO :".$dom_numero.
        //     "Dom complemento :".$dom_complemento.
        //     "Codigo Responsavel :".$codigoResponsavelFamiliar
        // );
        
        $qtde = $tbBai->verificaVinculo($rua_codigo, $dom_numero, $codigoResponsavelFamiliar, $dom_complemento)->toArray();
        $this->view->dados = $qtde["qtde"];
        return $this->render("dados", null, true);
    }

    

}

?>
