<?php

class ProgramasFederais_AtualizarController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->title = "Atualizar";
        $this->_helper->acl->allow(NULL);
    }
    
    public function indexAction(){
        $this->view->title = "Atualiar Esus";
        $this->render("index");

    }  

    public function recuperaDadosAtendimentoAction(){
        $recebeDataInicial = $this->_getParam("dataInicial");
        $recebeDataFinal = $this->_getParam("dataFim");

        $tipoAtendimento = new Application_Model_Atendimento;
        $tipoAtendimento->coletarDadosDoAtendimento();
    }

}
?>
