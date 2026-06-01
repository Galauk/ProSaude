<?php

class FarmaciaController extends Zend_Controller_Action {

    public function init() {
        $this->view->title = "Farmacia";
        $this->_helper->acl->allow(NULL);
    }

    public function indexAction() {
        // action body
    }

    public function restritoAction() {
        $this->view->title = "Acesso restrito";
    }

    public function ajaxFormAction() {
        $this->_helper->layout->disableLayout();
    }

}
