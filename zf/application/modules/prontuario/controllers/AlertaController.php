<?php

class Prontuario_AlertaController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Alertas";
	}

	public function indexAction() {
		
	}

	public function itensAction() {
		$tbAle = new Application_Model_Alerta();
		$this->view->itens = $tbAle->getItens();
	}

	public function salvarAction() {
                
		if ($this->_request->isPost()) {
                        
			$json = $this->_request->getPost("json", FALSE);

			$dados = array(
				"ale_desc" => $this->_request->getPost("ale_desc", NULL),
                                "usu_codigo" => ($this->_request->getPost("usu_codigo") != NULL ? $this->_request->getPost('usu_codigo', NULL) : ""),
                                "usr_codigo" => ($this->_request->getPost("usr_codigo") != NULL ? $this->_request->getPost('usr_codigo', NULL) : ""),
                                "ale_data" => ($this->_request->getPost("ale_data") != NULL ? $this->_request->getPost('ale_data', NULL) : "NOW()")
			);

			try {
				$tbAle = new Application_Model_Alerta();
				$pk = $tbAle->salvar($dados);
 
				if ($json) {
					$dados['ale_codigo'] = $pk;
					$this->view->dados = $dados;
					return $this->render("dados", NULL, TRUE);
				}else{
                                    $this->view->dialog = array("Confirmação", "Alerta registrado com sucesso!", 300, 140);
                                    $this->render("index");
                                }
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->render("index");
			}
		} else {
			$this->_redirect("/prontuario/alerta");
		}
	}

	public function excluirAction() {
		$id = (int) $this->_getParam("id", 0);
                
		if (!$id)
			return $this->_redirect("/prontuario/alerta");

		$tbAle = new Application_Model_Alerta();
		$tbAle->excluir($id);

		if ($this->_getParam("json", FALSE)) {
			$this->view->dados = array("success" => TRUE);
			return $this->render("dados", NULL, TRUE);
		}

		return $this->_redirect("/prontuario/alerta");
	}

}

