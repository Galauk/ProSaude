<?php

class Relatorio_IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Relatórios";
	}

	public function indexAction() {
		// listar os modulos que possuem relatorio
	}
        
        
	public function formProducaoPorProfissionalAction() {

	}

}