<?php

class ProgramasFederais_CadastroDomiciliarController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        
    }

    public function inconsistenciasAction() {
        $this->view->title = 'E-SUS Inconsistências Cadastro Domiciliar';
        $uuid = $this->_request->getPost("uuid");
        if ($uuid) {
            $tbDr = new Application_Model_TbCdsDomicilioResposta();
            $this->view->dados = $tbDr->getDadosPorUuid($uuid);
        }
    }

}

?>