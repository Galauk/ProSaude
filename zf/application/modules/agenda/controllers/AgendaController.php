<?php

class Agenda_AgendaController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Fazer Agendamento";
	}

	public function indexAction() {
		// action body
	}
        
        public function novoAction() {
		// action body
	}
	
	public function selecionarDataAction(){
		$this->_helper->layout->disableLayout();
		
		$coni_codigos = $this->_getParam("procs", FALSE);
		$this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));
                
		
		if(!$coni_codigos)
			return $this->_helper->viewRenderer->setNoRender(true);
		
		$coni_codigos = explode(",", $coni_codigos);
		
		$tbAge = new Application_Model_Agenda();
		$tbConI = new Application_Model_ConvenioItens();
                $this->view->data_final = $tbAge->calculaDataFinal($this->view->data_inicial);
                
		$this->view->vagas = $tbAge->getVagas($coni_codigos, $this->view->data_inicial, $this->view->data_final);
              
		$this->view->nomeProcs = $tbConI->getNomeProcedimentos($coni_codigos);
	}
        
        public function selecionarDataNovoAction(){
		$this->_helper->layout->disableLayout();
		
		$coni_codigos = $this->_getParam("procs", FALSE);
		$this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));
		
		if(!$coni_codigos)
			return $this->_helper->viewRenderer->setNoRender(true);
		
		$coni_codigos = explode(",", $coni_codigos);
		
		$tbAge = new Application_Model_Agenda();
		$tbConI = new Application_Model_ConvenioItens();
                $this->view->data_final = $tbAge->calculaDataFinal($this->view->data_inicial);
                
		$this->view->vagas = $tbAge->getVagasNovo($coni_codigos, $this->view->data_inicial, $this->view->data_final);
              
		$this->view->nomeProcs = $tbConI->getNomeProcedimentos($coni_codigos);
		$this->view->valorProcs = $tbConI->getValorProcedimentos($coni_codigos);
	}
        
        /**
	 * Salvar
	 * Acessar por post/ajax
	 * @return json
	 */
	public function salvarAction(){
		if ($this->_request->isPost()) {
			$dados = array(
				"usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
				"usr_codigo_medico" => $this->_request->getPost("usr_codigo_medico", FALSE),
				"ate_codigo" => $this->_request->getPost("ate_codigo", FALSE),
				"interno" => $this->_request->getPost("interno", FALSE),
				"itens" => $this->_request->getPost("coni", array())
			);

			try {
				$tbAge = new Application_Model_Agenda();
				$age_codigo = $tbAge->salvar($dados);
				$this->view->dados = array("success"=>TRUE,"age_codigo"=>$age_codigo);
				
			} catch (Zend_Validate_Exception $exc) { // Exceção de validação
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
				
			} catch (Zend_Exception $exc) { // Exceção de login
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Faça login", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
			}
			
			return $this->render("dados", NULL, TRUE);
		} else {
			$this->_redirect("/agenda/agenda");
		}
	}
        
        public function salvarnovoAction(){
		if ($this->_request->isPost()) {
			$dados = array(
				"usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
				"usr_codigo_medico" => $this->_request->getPost("usr_codigo_medico", FALSE),
				"ate_codigo" => $this->_request->getPost("ate_codigo", FALSE),
				"interno" => $this->_request->getPost("interno", FALSE),
                            	"uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
				"itens" => $this->_request->getPost("coni", array())
			);

			try {
				$tbAge = new Application_Model_Agenda();
				$age_codigo = $tbAge->salvar($dados);
				$this->view->dados = array("success"=>TRUE,"age_codigo"=>$age_codigo);
				
			} catch (Zend_Validate_Exception $exc) { // Exceção de validação
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
				
			} catch (Zend_Exception $exc) { // Exceção de login
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Faça login", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
			}
			
			return $this->render("dados", NULL, TRUE);
		} else {
			$this->_redirect("/agenda/agenda/novo");
		}
	}

	public function imprimirAction(){
		$age_codigo = $this->_getParam("age", FALSE);
        $coletados = $this->_getParam("coletados", FALSE);
		$this->_helper->layout->setLayout("modelo-print");
		$tbAge = new Application_Model_Agenda();
        $tbUsr = new Application_Model_Usuarios();
        $tbUsuario =  new Application_Model_Usuario();

		$age = $tbAge->getAgendamento($age_codigo,$coletados);
       	$dadosUsuario = $tbUsuario->getInfo($age->current()[usu_codigo]);

        $this->view->cnsUsuario = $dadosUsuario["usu_cartao_sus"];
        $this->view->dataNascUsuario = $dadosUsuario["usu_datanasc"];
        $this->view->telefoneUsuario = $dadosUsuario["dom_telefone"];
        $this->view->emissor = $tbUsr->getUsrAtual()->usr_nome;
		$this->view->usu_codigo = $age->current()->usu_codigo;
		$this->view->usu_nome = $age->current()->usu_nome;
		$this->view->age = $age;
        $this->view->medico = ($age->current()->usr_nome ? $age->current()->usr_nome: $age->current()->medico_e);
		$this->view->orientacoes = $tbAge->getOrientacoes($age_codigo);
        $this->view->coletados =$coletados;
	}

	/**
	 * Histórico de agendamento de exames por paciente
	 */
	public function historicoAction(){
		$this->_helper->layout->disableLayout();
		
		$usu_codigo = $this->_getParam("usu", FALSE);
		if(!$usu_codigo)
			return $this->_redirect ("/agenda/agenda");
		
		$tbAge = new Application_Model_Agenda();
		$this->view->itens = $tbAge->getHistoricoDeExames($usu_codigo);
	}
	
	public function excluirAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$agei_codigo = $this->_request->getPost("agei_codigo", FALSE);
		if(!$agei_codigo)
			return $this->_redirect ("/agenda/agenda");
		
		$tbAgei = new Application_Model_AgendaItens();
		$tbAgei->excluir($agei_codigo);
	}
        
   

}

