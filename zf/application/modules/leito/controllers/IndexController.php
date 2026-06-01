<?php

class Leito_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Leitos";
	}

	public function indexAction() {
		// action body
	}
        
        public function iniciarAction(){
            $ate_codigo = $this->_getParam("cod",null);
            //die($ate_codigo);
        }
	
}

