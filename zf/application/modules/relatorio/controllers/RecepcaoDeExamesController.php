<?php

class Relatorio_RecepcaoDeExamesController extends Elotech_Controller_Action_Relatorio {

    public function init(){
        $this->_helper->acl->allow(NULL);
        $this->view->title = "Recepção de Exames";
    }

    public function indexAction(){
        $tbMedico = new Application_Model_Medico();

        $recebePrestadorTipoL = $tbMedico->recuperaPrestadorTipoL();
        
        $this->view->recebePrestadorTipoL = $recebePrestadorTipoL;
    }

    public function gerarRelatorioRecepcaoDeExamesAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Faturamento de exames";

        $recebeCodigoLocal = $this->_request->getParam("recebeCodigoLocal");
        $recebeDataInicial = $this->_request->getParam("recebeDataInicial");
        $recebeDataFinal = $this->_request->getParam("recebeDataFinal");
        
        // var_dump($recebeDataFinal);die();

        $tbMedico = new Application_Model_Medico();

        $recebeResultado = $tbMedico->gerarRelatorioRecepcaoDeExames($recebeCodigoLocal, $recebeDataInicial, $recebeDataFinal);
        
        $this->view->recebeResultado = $recebeResultado = $tbMedico->gerarRelatorioRecepcaoDeExames($recebeCodigoLocal, $recebeDataInicial, $recebeDataFinal);

        $this->view->recebeDataInicial = $recebeDataInicial;
        
        $this->view->recebeDataFinal = $recebeDataFinal;
        
        return $this->render("rl-recepcao-de-exames");  
        
    }


}

