<?php

class Prontuario_InternacaoNovoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Internacao";
	}

	public function indexAction() {
		$age = Application_Model_Agendamento::usuEmAberto();
		$this->view->url = "/WebSocialSaude/prontuarioEletronico/interna_med.php?tp=pr&acao=novo&io_codigo=".$_REQUEST['io_codigo']."&age_codigo={$age->age_codigo}&usu_codigo={$age->usu_codigo}&uni_codigo={$age->uni_codigo}&med_codigo={$age->med_codigo}&esp_codigo={$age->esp_codigo}&age_data={$age->data}";
		$this->view->height = 580;
		return $this->render("iframe", NULL, TRUE);		
	}
}

