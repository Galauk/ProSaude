<?php

class AgendamentoExternoController extends Zend_Controller_Action {

	public function init() {
            $this->_helper->acl->allow(NULL,array('itens'));	
            $this->view->title = "Agendamento Externo";
                
	}

	public function indexAction() {
		// action body
	}

	public function editarAction(){
		$agee_codigo = $this->_getParam("id",FALSE);
		if(!$agee_codigo)
			$this->_redirect ("/agendamento-externo");
		
		$tbAgee = new Application_Model_AgendamentoExterno();
		$this->view->dados = $tbAgee->buscar($agee_codigo);
		
		$this->render("index");
	}
	
	public function salvarAction(){
		if ($this->_request->isPost()) {

			$dados = array(
				"agee_codigo" => $this->_request->getPost("agee_codigo", FALSE),
				"usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
				"esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
				"med_codigo" => ($this->_request->getPost("med_codigo", FALSE) == '' ? 1 : $this->_request->getPost("med_codigo", FALSE)),
				"proc_codigo" => $this->_request->getPost("proc_codigo", FALSE),
				"usr_codigo_solicitante" => $this->_request->getPost("usr_codigo_solicitante", FALSE),
				"interno" => $this->_request->getPost("interno", FALSE),
				"med_codigo_prestador" => 199,
				"agee_situacao" => $this->_request->getPost("agee_situacao", 0),
				"agee_data" => $this->_request->getPost("agee_data", FALSE),
				"agee_hora" => $this->_request->getPost("agee_hora", FALSE),
				"agee_num_reg" => $this->_request->getPost("agee_num_reg", FALSE),
				"nivel" => $this->_request->getPost("nivel", FALSE),
				"agee_observacao" => $this->_request->getPost("agee_observacao", FALSE)
			);
			
			// echo "<pre>";print_r($dados);die();
			
			// usado para que os inputs voltem preenchido em caso de erro
			$outros = array(
				"buscar1" => $this->_request->getPost("buscar1", ""),
				"usu_nome" => $this->_request->getPost("usu_nome", ""),
				"usu_carto_sus" => $this->_request->getPost("usu_carto_sus", ""),
				"usu_datanasc" => $this->_request->getPost("usu_datanasc", ""),
				"usu_end_rua" => $this->_request->getPost("usu_end_rua", ""),
				"esp_nome" => $this->_request->getPost("esp_nome", ""),
				"med_destino" => $this->_request->getPost("med_destino", ""),
				"proc_nome" => $this->_request->getPost("proc_nome", ""),
				"usr_nome" => $this->_request->getPost("usr_nome", ""),
				"med_prestador" => $this->_request->getPost("med_prestador", "")
			);

			$recebeUsuCodigoTelefone = array(
				"usu_fone" => $this->_request->getPost("usu_fone", ""),
				"usu_codigo" => $this->_request->getPost("usu_codigo", FALSE)
			);
			// echo "<pre>";print_r($recebeUsuCodigoTelefone);die();
			try {

				$tbAgee = new Application_Model_AgendamentoExterno();
				$tbUsuario = new Application_Model_Usuario();

				$tbUsuario->atualizaTelefone($recebeUsuCodigoTelefone);

                $agee_codigo = $tbAgee->salvar($dados);

                if ($agee_sit==2) { 
                	$this->view->print = $agee_codigo; 
                }

                $this->view->agee_codigo = $agee_codigo;
                $this->render("index");
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) array_merge($dados,$outros);
				$this->render("index");
			}
		} else {
			$this->_redirect("/agendamento-externo");
		}
		
	}

	public function itensAction(){		
		// Para usar com ajax, desabilitar o layout
		$this->_helper->layout()->disableLayout();
		
		$usu_codigo = $this->_getParam("usu_codigo",FALSE);
		if(!$usu_codigo){
			return false;
		}
		
		$tbAgee = new Application_Model_AgendamentoExterno();
		$this->view->itens = $tbAgee->getHistorico($usu_codigo);
	}	

	public function imprimirAction($agee_codigo){
		if ($agee_codigo == "") {
			$agee_codigo = $this->_getParam('agee_codigo', FALSE);
		}
		
		Zend_Layout::getMvcInstance()->setLayout("print");
		$this->view->title = "Imprimir Encaminhamento";
		$tbAgee = new Application_Model_AgendamentoExterno();

		$recebeAgeeCodigo = intval($agee_codigo);

		if ($recebeAgeeCodigo == '') {
			
			$agee_codigo = $this->_getParam("id",FALSE);
			
			// if(!$agee_codigo){
			// 	return $this->_redirect ("/agendamento-externo");
			// }
			$this->view->dados = $tbAgee->imprimir($agee_codigo);
		} else{
			$this->view->dados = $tbAgee->imprimir($recebeAgeeCodigo);

			return $this->render("imprimir");

		}

    }

    public function imprimir2Action(){
		Zend_Layout::getMvcInstance()->setLayout("print");
		$this->view->title = "Imprimir Encaminhamento";
		
		$agee_codigo = $this->_getParam("id",FALSE);
		if(!$agee_codigo)
			return $this->_redirect ("/agendamento-externo");
		
		$tbAgee = new Application_Model_AgendamentoExterno();
		$this->view->dados = $tbAgee->imprimir($agee_codigo);
    }
	
	public function excluirAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$agee = $this->_getParam("id", FALSE);
		if(!$agee)
			return $this->_redirect ("/agendamento-externo");
		
		$tbAgee = new Application_Model_AgendamentoExterno();
		$tbAgee->excluir($agee);
	}
	
}

