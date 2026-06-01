<?php

class CidController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->acl->allow(NULL,array("buscar"));
    }

    public function indexAction() {
            // action body
    }

    /**
     * Retorna a cidadades em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarAction(){
        $tbCid = new Application_Model_Cid();
        $term = $this->_getParam("term",FALSE);
        $this->view->dados = $tbCid->buscar($term);
        return $this->render("dados", NULL, TRUE);
    }   
}