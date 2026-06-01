<?php

class MedicoExternoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL,array("buscar"));
	}

	public function indexAction() {
		// action body
	}

	/**
	 * Retorna as especilidades em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbMed = new Application_Model_Medico();
		
		$term = $this->_getParam("term",FALSE);
		$prestador = $this->_getParam("prestador",array("M"));
		$this->view->dados = $tbMed->buscar($term,$prestador);
		return $this->render("dados", NULL, TRUE);
	}
}

