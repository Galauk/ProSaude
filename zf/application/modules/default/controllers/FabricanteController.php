<?php

class FabricanteController extends Zend_Controller_Action {

	public function init() {
            $this->view->title = "Cadastro de Fabricante";
	}

	public function indexAction() {
           $tbFab = new Application_Model_Fabricante();
           $this->view->itens = $tbFab->getFabricantes();     
          
	}

	public function buscarAction(){
            $tbFab = new Application_Model_Fabricante();

            $term = $this->_getParam("term",FALSE);
            $this->view->dados = $tbFab->buscar($term);
            return $this->render("dados", NULL, TRUE);
	}
        
        public function novoAction() {
             $this->render("form");

        }
        
        public function salvarAction(){      
            $tbFab = new Application_Model_Fabricante();
            $dados = array("fab_descricao"=>$this->_getParam("fab_descricao",FALSE),
                           "fab_cnpj"=>$this->_getParam("fab_cnpj",FALSE),
                           "fab_endereco"=>$this->_getParam("fab_endereco",FALSE));

            if($this->_getParam("fab_codigo",FALSE))
                    $dados[fab_codigo] = $this->_getParam("fab_codigo",FALSE);

            try{
                $tbFab->salvar($dados);
                $this->_redirect("default/fabricante/");
            }catch (Zend_Validate_Exception $exc){
                $exc->getMessage();
            }
        }
      
      public function excluirAction(){
            $id = $this->_getParam("id",false);
            $tbFab = new Application_Model_Fabricante();
            try{
                $tbFab->excluir($id); 
                $this->view->dados = 0;
            }  catch (Zend_Validate_Exception $exc){
                $this->view->dados = 1;
            }
         
           return $this->render("dados");
    }
    
    public function editarAction() {
        $tbFab = new Application_Model_Fabricante();
        $fab_codigo = $this->_getParam("id",FALSE);     

        if (!$fab_codigo)
            return $this->_redirect("/default/fabricante");
        
        $fabricante = $tbFab->getFabricante($fab_codigo);
        $this->view->fab_descricao = $fabricante->fab_descricao;
        $this->view->fab_cnpj = $fabricante->fab_cnpj;
        $this->view->fab_endereco = $fabricante->fab_endereco;
        $this->view->fab_codigo = $fabricante->fab_codigo;
        return $this->render("form");
    }
    
    public function pesquisaAction() {
        $tbFab = new Application_Model_Fabricante();
        if ($this->_request->isPost()) {
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbFab->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/default/fabricante/index");
        }
    }
    

}

