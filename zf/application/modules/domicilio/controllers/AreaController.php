<?php
class Domicilio_AreaController extends Zend_Controller_Action {
    public function init() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/domicilio/area/form.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.cookie.js');
        $this->view->title = "Módulo Domicilio";
        parent::init();
    }
    
    public function indexAction(){
        $tbArea = new Application_Model_Area();
        $this->view->itens = $tbArea->getAreas();
    }
    
    public function novoAction(){
        
        $this->render("form");
    }
    
    public function formAction(){
    }
    
    public function salvarAction(){
        $tbArea = new Application_Model_Area();        
        $this->_helper->layout->disableLayout();
       
        $dados = array("area_desc"=>$this->_getParam("area_desc",FALSE),
                       "area_responsavel"=>$this->_getParam("usr_codigo",FALSE),
                       "area_obs"=>$this->_getParam("area_obs",FALSE),
                       "area_codigo"=>$this->_getParam("area_codigo",FALSE));
        
        
        $tbArea->salvar($dados);
        $this->_redirect("domicilio/area/");
    }
    
    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $tbArea = new Application_Model_Area();
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbArea->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/domicilio/area/index");
        }
    }
    
    public function editarAction(){
        $area_codigo = $this->_getParam("id");
        $tbArea = new Application_Model_Area();
        $this->view->dados = $tbArea->getArea($area_codigo);
        return $this->render("form");
    }
    
    public function excluirAction(){
          $id = $this->_getParam("id",false);        
          $tbArea = new Application_Model_Area();
          $tbArea->excluir($id);
  
          $this->_redirect("domicilio/area/");
    }
    
}
?>
