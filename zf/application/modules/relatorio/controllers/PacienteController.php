<?php

class Relatorio_PacienteController extends Elotech_Controller_Action_Relatorio {
    
    public function init() {
        
    }
    
    public function indexAction(){
        Zend_Layout::getMvcInstance()->setLayout("retrato-print");
        $this->view->title = "Relatório total de pacientes ativos";
        $tbUsr = new Application_Model_Usuarios();
        $tbSec = new Application_Model_Secretaria();
        $tbUsu = new Application_Model_Usuario();
        $this->view->usr = $tbUsr->getUsrAtual();
        $this->view->secretaria  = $tbSec->getDadosSec();
        $this->view->tipo_impressao = "PACIENTES";
        $this->view->title = "Imprimir Encaminhamento Médico";
        $this->view->dados = $tbUsu->getQtdUsuariosAtivo();
    }

}

