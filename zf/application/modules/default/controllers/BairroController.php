<?php

class BairroController extends Zend_Controller_Action {

    public function init(){
        $this->_helper->acl->allow(NULL,array("buscar"));
        $this->view->title = "Cadastro de Bairro";
    }
    
    public function buscarAction(){
        $tbBai = new Application_Model_TbBairro();
        $rua_codigo = $this->_getParam("rua_codigo",false);
        $this->view->dados = $tbBai->buscar($this->_request->getParam("term"),$rua_codigo);
        return $this->render("dados",NULL,TRUE);
    }
	
    // Verifica se bairro existe ou não
    public function validaBairroAction() {
        $tbBai = new Application_Model_TbBairro();
        $bairro = $this->_request->getPost("bairro");
        $bairro_codigo = $tbBai->getDadosBairro($bairro)->co_bairro;
        // Se bairro não existir, insere
        if (!empty($bairro_codigo)){
            $this->view->dados = $bairro_codigo;
        } else {
            $this->view->dados = "erro";
        }
        return $this->render("dados",NULL,TRUE);
    }
}

