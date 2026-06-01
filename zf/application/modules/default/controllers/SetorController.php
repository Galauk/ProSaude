<?php

class SetorController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL,array("buscar"));
	}

	public function indexAction() {
		// action body
	}

	/**
	 * Retorna os setores em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbSet = new Application_Model_Setor();
		
		$term = $this->_getParam("term",FALSE);
                $set_logado = $this->_getParam("set_logado",FALSE);
		$this->view->dados = $tbSet->buscar($term,$set_logado);
		return $this->render("dados", NULL, TRUE);
	}
        
        public function buscarUnidadeSetorAction(){
            $tbSet = new Application_Model_Setor();
		
		$term = $this->_getParam("term",FALSE);
                $array_uni = $this->_getParam("array_uni",FALSE);
		$this->view->dados = $tbSet->buscarUnidadeSetor($term,$array_uni);
		return $this->render("dados", NULL, TRUE);
        }
        
        public function buscarSetoresPorUnidadeAction(){
            $tbSet = new Application_Model_Setor();
            $tbUsr = new Application_Model_Usuarios();
            $uni_codigo = 570019;
            //$usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
            $usr_codigo = 649;
            // Se unidade for vazia, pega a última unidade que logou 
            /*if ($uni_codigo == "") {
                //die("aaa");
                $tbLog = new Application_Model_Logon();
                $uni_codigo = $tbLog->
                        getDadosPeloUsuario($usr_codigo)->uni_codigo;
            }*/
            $this->view->dados = $tbSet->
                buscarSetoresPorUnidade($uni_codigo,$usr_codigo)->toArray();
            return $this->render("dados",NULL,TRUE);
        }
        
}

