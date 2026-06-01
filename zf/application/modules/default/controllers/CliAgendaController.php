<?php
// error_reporting(E_ALL);
class CliAgendaController extends Zend_Controller_Action {
    
    public function init() {
        $this->_helper->acl->allow(NULL);
    }

	public function indexAction() {
		// action body
	}
    
    public function buscarAction(){
    
        $tbCliAgenda = new Application_Model_CliAgenda();
        $term = $this->_getParam("term",FALSE);
        
        $this->view->dados = $tbCliAgenda->buscarAgenda($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarPacienteAction(){
    
        $tbCliAgenda = new Application_Model_CliAgenda();
        $term = $this->_getParam("term",FALSE);
        
        $this->view->dados = $tbCliAgenda->buscarPaciente($term);
        return $this->render("dados", NULL, TRUE);
    }

}

?>