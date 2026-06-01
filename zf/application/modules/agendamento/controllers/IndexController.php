<?php

class Agendamento_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Página Inicial - Agendamento";
	}
        
        public function menuAction(){
            $this->view->title = "Consultas";
                    
        }
        
	public function indexAction() {
            $this->view->title = "Página Inicial - Agendamento";
            $tbTa = new Application_Model_TipoAtendimento();
            $tbConf = new Application_Model_Configuracao();
            $tbTc = new Application_Model_TipoConsulta();
                    
            if($tbConf->getConfig("TIPO_ATENDIMENTO_AGENDAMENTO") == 1){
                $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();
                $this->view->tipo_atendimento01 = $tbTa->getTiposDeAtendimento01();
                $this->view->tipo_atendimento02 = $tbTa->getTiposDeAtendimento02();
                $this->view->tipo_atendimento03 = $tbTa->getTiposDeAtendimento03();
            }
            if($tbConf->getConfig("DADOS_PACIENTE_AGENDAMENTO") == 1){
                $this->view->dados_paciente = 1;
            }
            if($tbConf->getConfig("IMPRIMIR_PRIMEIRO_HORARIO") == 1){
                $this->view->imprimir_primeiro_horario = 1;
            }
            $this->view->tipoConsulta = $tbTc->getDados();
            $tbEstra = new Application_Model_Estratificacao();
            //var_dump($tbEstra->getEstratificacoes())
            $this->view->estratificacoes = $tbEstra->getEstratificacoes();
            $session = new Zend_Session_Namespace();
            $this->view->dados_sessao = $session->dados;
            
	}
        
       

}

