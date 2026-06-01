<?php

class Prontuario_CidController extends Zend_Controller_Action {
	
	public function init(){
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
	}

	/**
	 * Retorna os CID's do procedimento informado
	 * O retorno é formado por tags <option>'s para preencher um select via ajax
	 * A tag <select> não é enviada na resposta.
	 */
	public function procedimentoAction(){
		// die("aqui ?");
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$procedimento = $this->_getParam("id",FALSE);
		if(!$procedimento){
			return false;
		}
		
		$tbCID = new Application_Model_Cid();		
		echo $tbCID->selectTag($procedimento);
	}
	 
	/**
	 * Retorna os CID's em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbCid = new Application_Model_Cid();
		
		$term = $this->_getParam("term",FALSE);
		$this->view->dados = $tbCid->buscar($term);
		return $this->render("dados", NULL, TRUE);
	}

}

