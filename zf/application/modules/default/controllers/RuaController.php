<?php

class RuaController extends Zend_Controller_Action {

	public function init(){
		$this->_helper->acl->allow(NULL,array("buscar"));
                $this->view->title = "Cadastro de Logradouro";
	}
        
        public function indexAction() {
           $tbFab = new Application_Model_Rua();
           $this->view->itens = $tbFab->getRuas();     
	}

	/**
	 * Retorna as ruas em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbEsp = new Application_Model_Rua();
		
		$term = $this->_getParam("term",FALSE);
		$this->view->dados = $tbEsp->buscar($term);
		return $this->render("dados", NULL, TRUE);
	}
        
        public function novoAction() {
            $this->view->title = "Cadastro de Logradouro";
            
            $this->view->popup = $term = $this->_getParam("popup",0);
            $tbTpLogr = new Application_Model_TbMsTipoLogradouro();
            $this->view->tp_lograd = $tbTpLogr->getTiposLogradouro();
            $this->render("form");

        }
        
        public function buscaTipoLogradouroAction(){
            $term = $this->_getParam("term",FALSE);
            $tbTpLogr = new Application_Model_TbMsTipoLogradouro();
            $this->view->dados = $tbTpLogr->buscar($term);
            return $this->render("dados", NULL, TRUE);
        }
        
        public function salvarAction(){      
            $tbRua = new Application_Model_Rua();
            $tbBai = new Application_Model_Bairro();
            $dados = array("rua_nome"=>mb_strtoupper($this->_request->getPost("rua_nome",FALSE), "UTF-8"),
                           "rua_cep"=>$this->_request->getPost("rua_cep",FALSE),
                           "co_tipo_logradouro"=>$this->_request->getPost("co_tipo_logradouro",FALSE),
                           "bai_codigo"=>$this->_request->getPost("bai_codigo",FALSE));
            
            if($this->_getParam("rua_codigo",FALSE))
                    $dados[rua_codigo] = $this->_getParam("rua_codigo",FALSE);

            try{
                $id = $tbRua->salvar($dados);
                $dados_bairro = $tbBai->getBairro($this->_request->getPost("bai_codigo",FALSE));
                if($dados_bairro->cid_nome != ""){
                    $cidade = $dados_bairro->cid_nome;
                }else{
                    $cidade = $dados_bairro->cid_distrito;
                }
                $this->view->dados = array("id"=>$id,
                                           "nome"=>mb_strtoupper($this->_request->getPost("rua_nome",FALSE),"UTF-8"),
                                           "rua_cep"=>$this->_request->getPost("rua_cep",FALSE),
                                           "bai_codigo"=>$this->_request->getPost("bai_codigo",FALSE),
                                           "bai_nome" => $dados_bairro->cid_nome,
                                           "cid" => $cidade,
                                           "dist" => ($dados_bairro->dis_nome ? $dados_bairro->dis_nome : "Não possui "));
                
            }catch (Zend_Validate_Exception $exc){
                $this->view->dados = array("msg"=>$exc->getMessage());
                
            }
            return $this->render("dados",null,true);
        }
      
      public function excluirAction(){
            $id = $this->_getParam("id",false);
            $tbFab = new Application_Model_Rua();
            try{
                $tbFab->excluir($id); 
                $this->view->dados = 0;
            }  catch (Zend_Validate_Exception $exc){
                $this->view->dados = 1;
            }
         
           return $this->render("dados");
    }
    
    public function editarAction() {
        $tbRua = new Application_Model_Rua();
        $rua_codigo = $this->_getParam("id",FALSE);  
        $this->view->popup = $term = $this->_getParam("popup",0);
       // die($this->_getParam("popup",0)."a");

        if (!$rua_codigo)
            return $this->_redirect("/default/rua");
        
        $rua = $tbRua->getRua($rua_codigo);
       
        $this->view->dados = $rua;
        $tbTpLogr = new Application_Model_TbMsTipoLogradouro();
        $this->view->tp_lograd = $tbTpLogr->getTiposLogradouro();
        return $this->render("form");
    }
    
    public function pesquisaAction() {
        $tbFab = new Application_Model_Rua();
        if ($this->_request->isPost()) {
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbFab->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/default/fabricante/index");
        }
    }
}

