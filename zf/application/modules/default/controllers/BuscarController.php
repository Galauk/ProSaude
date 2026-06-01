<?php

class BuscarController extends Zend_Controller_Action {

	public function init(){
		$this->_helper->acl->allow(NULL);
	}

	public function formAction() {
		// action body
	}
}

