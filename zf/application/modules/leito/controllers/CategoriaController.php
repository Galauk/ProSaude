<?php
class Leito_CategoriaController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Cadastro de Categoria";
	}

	public function indexAction() {
           $tbLGC = new Application_Model_LeitoGradeCategoria();
	   $this->view->itens = $tbLGC->getCategorias();
	}      
        /**
         *ESSE METODO SALVA AS CATEGORIAS NOVAS 
         */
        public function salvarAction(){

            $oper = $this->_request->getPost("oper", FALSE);
            $dados = array("lgc_codigo"=>$this->_request->getPost("lgc_codigo",FALSE),
                           "lgc_descricao"=>$this->_request->getPost("lgc_descricao",FALSE));
            $tbLGC = new Application_Model_LeitoGradeCategoria();
            $tbLGC->salvar($dados);
            $this->_redirect("leito/categoria/index");
        }
        
        public function editarAction(){
            $id = (int) $this->_getParam("id", 0);
            $tbLGC = new Application_Model_LeitoGradeCategoria();
            $this->view->dados = $tbLGC->find($id)->current();
            return $this->render("novo");        
        }
        
	public function novoAction() {
		$this->view->fechaPopUp = $this->_getParam("pop", 0);
	}
        
        public function pesquisaAction() {
		if ($this->_request->isPost()) {
			$dados = $this->_request->getPost("busca");
			$tbProc = new Application_Model_LeitoGradeCategoria();
			$ouch = $tbProc->pesquisarCategoria($dados);
			$this->view->itens = $ouch;
			$this->render("index");
		} else {
			$this->_redirect("agenda/procedimento");
		}
	}
        
        public function excluirAction(){
            $id = $this->_getParam("id",0);
            try {
                $tbLGC = new Application_Model_LeitoGradeCategoria();
                $tbLGC->excluir($id);
                return $this->_redirect("leito/categoria/index");
            }catch (Zend_Validate_Exception $exc){
                $this->view->erro = $exc->getMessage();;
		return $this->render("categoria/index", NULL, TRUE);
            }
        }

}

