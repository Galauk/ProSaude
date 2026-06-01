<?php

class Prontuario_EnfermagemController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");// essa linha fala que vou utilizar o layout do prontuário(zf/aplication/layout)
		$this->view->title = "Enfermagem";
	}
	
	/**
	 * Responsavel por gerar a view index da enfermagem
	 */
	public function indexAction(){
		$tbUsr = new Application_Model_Usuarios();
		if($tbUsr->fazPreConsulta()){
			return $this->_redirect("/prontuario/enfermagem/listar");
		}
		$tbPE = new Application_Model_PostoEnfermagem(); 
		$this->view->dados = $tbPE->buscarAtual();
	}// Geralmente o index não faz nada, ele faz quando tiver algum processamento faz aqui
	
	public function listarAction() {
		$this->view->title = "Posto de Enfermagem";
                $tbUsr = new Application_Model_Usuarios();
                $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;

		$tbPosto = new Application_Model_PostoEnfermagem();
		$this->view->itens = $tbPosto->getLista($uni_codigo,"PE");
                
	}
	
	public function verAction(){
		$pe_codigo = $this->_getParam("cod", FALSE);
                
		if(!$pe_codigo)
			return $this->_redirect ("/prontuario");
		
		$tbPe = new Application_Model_PostoEnfermagem();
        $tbUsr = new Application_Model_Usuarios();
        
		$this->view->pe_codigo = $pe_codigo;
		$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
		$this->view->dados = $tbPe->buscar($pe_codigo);
	}

	
	public function salvarAction() {
		if ($this->_request->isPost()) {

			$dados = array(
				"pe_descricao" => $this->_request->getPost("pe_descricao", FALSE),
				"pe_codigo" => $this->_request->getPost("pe_codigo",FALSE)
			);
			try {
				$tbPE = new Application_Model_PostoEnfermagem();
				$this->view->dados = (object) $dados;
				$this->view->dados->pe_codigo = $tbPE->salvar($dados);
				$this->view->dialog = array("Confirmação", "Encaminhado com sucesso!", 300, 140);
				$this->render("index"); // faz com que na ação salvar ele tras o código pronto 
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->render("index");
			}
		} else {
			$this->_redirect("/prontuario/enfermagem");
		}
	}
	
	public function salvarPostoAction(){
		if ($this->_request->isPost()) {

			$dados = array(
				"pe_codigo" => $this->_request->getPost("pe_codigo",FALSE),
				"proc_codigo" => $this->_request->getPost("proc_codigo",FALSE),
				"pe_observacao" => $this->_request->getPost("pe_observacao",FALSE),
				"pe_descricao" => $this->_request->getPost("pe_descricao",FALSE)
			);
                        
			try {
                           
				$tbPE = new Application_Model_PostoEnfermagem();
				$tbPE->salvar($dados);
                                 
				$this->view->dialog = array("Confirmação", "Salvo com sucesso!", 300, 140);
				$this->view->dados = (object) $dados;
				$tbUsr = new Application_Model_Usuarios();
                                
				$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
				$this->render("ver"); // faz com que na ação salvar ele tras o código pronto 
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$tbUsr = new Application_Model_Usuarios();
				$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
				$this->render("ver");
			}
		} else {
			$this->_redirect("/prontuario/enfermagem");
		}
	}
	
	public function itensAction(){
		$pe_codigo = $this->_getParam("cod", FALSE);
		
		if(empty($pe_codigo))
			return $this->_redirect("/prontuario/enfermagem");
			
		$tbPat = new Application_Model_ProcedimentoAtendimento();
		$this->view->itens = $tbPat->getHistorico($pe_codigo,Application_Model_ProcedimentoAtendimento::POSTO_ENFERMAGEM);
	}

	public function excluirAction(){
		$pe_codigo = $this->_getParam("cod"); // cod = pe_codigo	
		$id = (int) $this->_getParam("id",0); // id = pat_codigo
		if(!$id)
			return $this->_redirect ("/prontuario/enfermagem");
		
		$tbPA = new Application_Model_ProcedimentoAtendimento();
		$tbPA->excluir($id);
			
		return $this->_redirect("/prontuario/enfermagem/ver/cod/$pe_codigo");
	}
	
	public function finalizarAction(){
		$pe_codigo = $this->_getParam("id"); // cod = pe_codigo
		if(!$pe_codigo)
			return $this->_redirect ("/prontuario/enfermagem");
		
		$tbPe = new Application_Model_PostoEnfermagem();
		$tbPe->finalizar($pe_codigo);
		return $this->_redirect ("/prontuario/enfermagem");
	}
	public function quantidadeAction(){
                $tbUsr = new Application_Model_Usuarios();
                $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;
		$tbPosto = new Application_Model_PostoEnfermagem();
		$this->view->quantidade = $tbPosto->getLista($uni_codigo);
	}
	
}

