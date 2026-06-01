<?php

class ProgramasFederais_CadastroIndividualController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "E-sus: Cadastro Individual";
    }
    
    public function indexAction() {
    }
    
    public function inconsistenciasAction() {
        $this->view->title = "E-SUS Inconsistências Cadastro Individual";
        $uuid = $this->_request->getPost("uuid");
        if ($uuid){
            $tbEsusCi = new Application_Model_EsusCadastroIndividual();
            $this->view->dados = $tbEsusCi->getDadosPorUuid($uuid);
        }
    }

}

?>