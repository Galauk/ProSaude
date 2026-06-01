<?php

class Materiais_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Index";
	}
	public function indexAction() {
	// action body
	}

}

