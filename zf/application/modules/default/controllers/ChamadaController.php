<?php

class ChamadaController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Chamada";
                $this->_helper->acl->allow(NULL,array('index'));
	}

	public function indexAction() {
		
	}
	
        public function buscarChamadasAction() {
            $tbUsu= new Application_Model_Usuarios();
            $uni_codigo = $tbUsu->getUsrAtual()->uni_codigo;
            $tbCha = new Application_Model_Chamada();
            $chamadas = $tbCha->buscarChamadas($uni_codigo)->toArray();
            $this->view->dados = $chamadas;
            return $this->render("dados", NULL, TRUE);
        }
        
        public function alteraStatusAction() {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
	        $age_codigo = $this->_getParam("age_codigo");
            $tbCha = new Application_Model_Chamada();
            $tbCha->encerrarChamada($age_codigo);
            $this->render("index");
	}
        
        public function chamarAction(){
            $age_codigo = $this->_getParam("age_codigo");
            $tbCha = new Application_Model_Chamada();
            $tbCha->encerrarChamada($age_codigo,"C");
            $this->render("index");
            
        }
        public function lerAction() {
            $usu_nome = $this->_getParam("usu_nome","maria");
	        $tbCha = new Application_Model_Chamada();
            $chamadas = $tbCha->ler($usu_nome);
            //echo "<pre>".print_r($chamadas,1); die();
            $this->view->dados = $chamadas;
            return $this->render("dados", NULL, TRUE);
            
	}
        
}

