<?php

class Prontuario_AgendaDoDiaController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");
	}

	public function indexAction() {
            $this->view->title = "Pacientes Agendados";
        //     die("dasdsad");
            $tbAgenda = new Application_Model_Agendamento();
            $usr_codigo = $this->_getParam("usr_codigo", FALSE);
            $tbAte = new Application_Model_Atendimento();
            $this->view->retornos = $tbAte->getRetorno();
                // die("asfasdf");

            try{
                $this->view->itens = $tbAgenda->getAgenda($usr_codigo);

                    //echo "<pre>".print_r($this->view->itens)."</pre>";die();
            } catch (Zend_Validate_Exception $e){
                $this->view->erro = $e->getMessage();
                return $this->render("erro", NULL, TRUE);
            }

            $tbUsr = new Application_Model_Usuarios();
            $pacem = $this->_getParam("pacem", FALSE);
            $this->view->pacem = $pacem;
            $this->view->isMedico = $tbUsr->isMedico();
            if($pacem == 2){
                    $this->view->dialog = array("Alerta", "Esse paciente já está sendo atendido por outro médico!", 300, 140);
                    return;
            }
	}
        
        public function atendidosAction(){
		$this->view->title = "Pacientes Atendidos Hoje";
                $tbAte = new Application_Model_Atendimento();
                $this->view->itens = $tbAte->buscarAtendidos();
        }
	
	public function finalizarAction(){
                $retorno = $this->_getParam("retorno",1);
                $agess_codigo = $this->_getParam("age",1);
		$tbAge = new Application_Model_Agendamento();  
                $tbAte = new Application_Model_Atendimento(); 
                //echo "<pre>".print_r($_SESSION)."</pre>";die();
		$tbAge->finalizar();
                $ate_codigo = $ate_codigo[ate_codigo];
                //echo $ate_codigo;
                
                // Essa parte precisa ser arrumada porque ao finalizar um atendimento
                // que não é retorno da problema
                $retorno_origem = $tbAte->buscaRetornoOrigem($agess_codigo);
                $tbAte->finalizaRetorno($retorno_origem->ate_codigo);
                $tbCha = new Application_Model_Chamada();
                $tbCha->encerrarChamada($agess_codigo);
                //die("aaa");
                return $this->_redirect("/prontuario/agenda-do-dia/");
	}
        
        public function retornoAction(){

            $age_codigo = $this->_getParam("age",1);
            $ate_codigo = $this->_getParam("ate_codigo",1);
            $tbAge = new Application_Model_Agendamento();
            $tbAge->alteraSituacao("A", $age_codigo);
            
            $tbAte = new Application_Model_Atendimento();
            $dadosAte = array("ate_codigo"=>$ate_codigo,
                                      "ate_encaminhamento"=>"S");
            $tbAte->salvar($dadosAte);
            
        }
        
        public function buscarAction(){
            $dados = array("med_codigo" => $this->_request->getPost("usr_codigo", NULL),
                           "usu_codigo" => $this->_request->getPost("usu_codigo", NULL),
                           "ate_data" => $this->_request->getPost("ate_data", NULL),
                           "pre" => $this->_request->getPost("pre", NULL),
                           "ate" => $this->_request->getPost("ate", NULL));
            $tbAte = new Application_Model_Atendimento();
            $this->view->itens = $tbAte->buscarAtendidos($dados);
            $this->view->title = "Pacientes Agendados";
            return $this->render("atendidos");
        }
        
}

