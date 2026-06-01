<?php

class ConfiguracaoController extends Zend_Controller_Action {

    public function init() {
        $this->view->title = "Configurações";
    }

    public function indexAction() {
        $tbConifg = new Application_Model_Configuracao();
        $tbCac = new Application_Model_CategoriaConfiguracao();
        $categorias = $tbCac->getCategorias()->toArray();
        $array_categoria_config = array();
        foreach ($categorias as $categoria) {
            $array_categoria_config[$categoria[cac_codigo]] = array("cac_descricao" => $categoria[cac_descricao],
                "itens" => $tbConifg->getConfigPorCategoria($categoria[cac_codigo])->toArray());
        }
        
        $this->view->dados = $array_categoria_config;
        $this->view->categorias = $categorias;
        $this->_helper->layout->setLayout("simples");
    }

    public function salvarAction() {
        if ($this->_request->isPost()) {

            $dados = array(
                "config" => $this->_request->getPost("config", array()),
                "tipo" => $this->_request->getPost("tipo", array())
            );
            try {
                $tbConf = new Application_Model_Configuracao();
                $tbConf->salvar($dados);
                $this->view->dialog = array("Confirmação", "Configurações salvas com sucesso!", 300, 140);
                $tbConifg = new Application_Model_Configuracao();
                $this->view->dados = $tbConifg->fetchAll(NULL, "conf_codigo");
                $this->_redirect("/configuracao");
            } catch (Zend_Validate_Exception $exc) {
                $this->view->erro = $exc->getMessage();
                $this->view->dados = (object) array_merge($dados, $outros);
                $this->render("index");
            }
        } else {
            $this->_redirect("/configuracao");
        }
    }

    public function corrigeAction(){

        $tbMov = new Application_Model_Movimento();
        //die(var_dump("here"));
        $this->view->corrige = $tbMov->corrigeMovimento();
        $this->render("index");
    }

}

