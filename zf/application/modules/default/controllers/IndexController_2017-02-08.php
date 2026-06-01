<?php

class IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Página Inicial";
	}

	public function indexAction() {
		$this->_helper->layout->setLayout("simples");
                
		$tbMA = new Application_Model_MaisAcessados();
		$this->view->itens = $tbMA->getMaisAcessados();
                $tbUsr = new Application_Model_Usuarios();
                $tbUsr->getUsrAtual();
                //echo "<pre>".print_r($_SESSION,1);die();
	}

	public function testeAction() {
	}

}

