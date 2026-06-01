<?php

class Plantao_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Página Inicial - Plantão";
	}

	public function indexAction() {
            $this->view->title = "Página Inicial - Plantão";

            $session = new Zend_Session_Namespace();
            $this->view->dados_sessao = $session->dados;

	}

}
