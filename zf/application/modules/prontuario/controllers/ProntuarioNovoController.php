<?php

class Prontuario_ProntuarioNovoController extends Zend_Controller_Action {
	
	private $_p;

	public function init() {		
		Zend_Layout::getMvcInstance()->setLayout("layout");
		$this->_p = new Zend_Session_Namespace("prontuario");
	}

	public function indexAction(){
		$tbUsr = new Application_Model_Usuarios();
		$tbAte = new Application_Model_Atendimento();
		$tbPC  = new Application_Model_PreConsulta();
		
		if(isset($this->_p->age)){
			$this->view->classi = $tbPC->getHistorico();
			$this->view->age = $this->_p->age;
			$this->view->temAtendimento = $tbAte->temAtendimentoMedico($this->_p->age->age_codigo);
			$this->view->temPreConsulta = $tbPC->temPreConsulta($this->_p->age->age_codigo);
			$this->view->isConsultaComEnfermeiro = ($this->_p->age->med_codigo==$tbUsr->getUsrAtual()->usr_codigo);
		}
		$this->view->title = "Prontuário Eletrônico";
		$this->_helper->layout->setLayout("layout");
		
		// ha paciente em atendimento?
		if(false !== ($age = Application_Model_Agendamento::usuEmAberto())){
			$this->view->age = $age;
			
			// filtrar atendimentos?
			$this->view->term = $this->_request->getPost("term", FALSE);
			
		} else // vai para "agenda do dia"
			return $this->_redirect ("/prontuario/agenda-do-dia");
	}
	

	public function iniciarAction(){		
		$age_codigo = $this->_getParam("cod",FALSE);
		$io_codigo = $this->_getParam("inter",FALSE);
		$tbAge = new Application_Model_Agendamento();
		
		if($age_codigo && !$tbAge->usuEmAberto()){
			$tbAge->iniciar($age_codigo);
        }
		
		return $this->_redirect ("/prontuario/prontuario-novo");		
	}
	
	public function cancelarAction(){
		
		Application_Model_Agendamento::cancelarAgendaAtual();
		
		$tbAge = new Application_Model_Agendamento();
		$age_codigo = $this->_getParam("age",FALSE);
	
	
		$tbAte = new Application_Model_Atendimento();
		$ate = $tbAte->estaEmAtendimento($age_codigo);
		if($ate->age_atendido == "E"){
			
			$tbAge->alteraMedico($age_codigo, '99999', 'P');
		}
		
		$this->_redirect("/prontuario/agenda-do-dia");
	}
	
	public function destroiSessionAction(){
		$s = new Zend_Session_Namespace("logon");
		$s->unsetAll();
		$s = new Zend_Session_Namespace("prontuario");
		$s->unsetAll();
		return $this->_redirect("/prontuario/agenda-do-dia");
	}

}

