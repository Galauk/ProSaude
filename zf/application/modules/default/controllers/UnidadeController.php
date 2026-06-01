<?php

class UnidadeController extends Zend_Controller_Action {

	public function init() {
    $this->_helper->acl->allow(NULL,array("buscar","carrega-cnes","carrega-equipes","inativa","get-unidades-por-profissional","buscar-raas"));
            
	}

	public function indexAction() {
		// action body
	}

	/**
	 * Retorna as especilidades em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
        // die("sadasda");
		$tbUni = new Application_Model_Unidade();
		
		$term = $this->_getParam("term",FALSE);
		$this->view->dados = $tbUni->buscar($term);
		return $this->render("dados", NULL, TRUE);
	}

    public function buscarRaasAction(){
        // die("alouuu");
        // die("sadasda");
        $tbUni = new Application_Model_Unidade();
        
        $term = $this->_getParam("term",FALSE);
        $this->view->dados = $tbUni->buscarRaas($term);
        return $this->render("dados", NULL, TRUE);
    }  



    public function inativaAction(){
            $uni_codigo = $this->_getParam("uni_codigo",FALSE);	
            $array_uni = array("uni_codigo"=>$uni_codigo,   
                               "cnes_ativo" => "I");
            $tbUni = new Application_Model_Unidade();
            try{
                $tbUni->salvar($array_uni);
                 $this->view->dados = 1;
            } catch (Exception $ex) {
                $this->view->dados = $exc->getMessage();
            }
            return $this->render("dados",null,true);
        }
        
         public function verificaSeExisteCnesAction(){
           $cnes = $this->_getParam("cnes",FALSE);	
           if(empty($cnes))
               return false;
           
           $tbUsr = new Application_Model_Unidade();
           $verifica = $tbUsr->verificaSeJáExiste($cnes);
           $this->view->dados = $verifica->qtd;
           return $this->render("dados",null,true);
       }
        
        public function carregaCnesAction(){
            $usrCodigo = $this->_request->getPost("usr_codigo");
            $tbUniUsr = new Application_Model_UnidadeUsuarios();
            $this->view->dados = $tbUniUsr->getUnidadeUsuarios($usrCodigo)->toArray();
            return $this->render("dados",NULL,TRUE);
        }  
        
        public function getUnidadesPorProfissionalAction(){
            $usr_codigo = $this->_getParam("usr_codigo",FALSE);	
            $tbUnu = new Application_Model_UnidadeUsuarios();
            $this->view->dados = $tbUnu->getUnidadesProfissional($usr_codigo)->toArray();
            return $this->render("dados",null,true);
        }

}

