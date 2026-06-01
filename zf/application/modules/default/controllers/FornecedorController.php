<?php

class FornecedorController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL,array("buscar"));
	}

	public function indexAction() {
		// action body
	}

	/**
	 * Retorna os setores em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbSet = new Application_Model_Fornecedor();
		
		$term = $this->_getParam("term",FALSE);
		$this->view->dados = $tbSet->buscar($term);
		return $this->render("dados", NULL, TRUE);
	}
}

