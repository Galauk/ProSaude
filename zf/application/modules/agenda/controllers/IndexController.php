<?php

class Agenda_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Página Inicial - Agendamento";
	}

	public function indexAction() {
		// action body
	}

}

