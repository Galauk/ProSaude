<?php
class Domicilio_MicroAreaController extends Zend_Controller_Action {
    public function init() {
        $this->view->title = "Domicilio - Micro-Área";
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/domicilio/micro-area/form.js');
        parent::init();
    }
    
    public function indexAction(){
        $tbArea = new Application_Model_MicroArea();
        $this->view->itens = $tbArea->getMicroAreas();
    }
    
    public function novoAction(){
        $tbArea = new Application_Model_Area();
        $this->view->areas = $tbArea->getAreas();
        
        $this->render("form");
    }
    
    public function formAction(){
        
    }
    
    public function salvarAction(){
        $tbMic = new Application_Model_MicroArea();        
        $this->_helper->layout->disableLayout();
       
        $dados = array("mic_descricao"=>$this->_getParam("mic_descricao",FALSE),
                       "mic_responsavel"=>$this->_getParam("usr_codigo",FALSE),
                       "area_codigo"=>$this->_getParam("area_codigo",FALSE),
                       "mic_codigo"=>$this->_getParam("mic_codigo",FALSE));
        

        $tbMic->salvar($dados);
        $this->_redirect("domicilio/micro-area/");
    }
    
    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $tbMic = new Application_Model_MicroArea();
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbMic->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/domicilio/area/index");
        }
    }
    
    public function editarAction(){
        $mic_codigo = $this->_getParam("id");
        $tbMic = new Application_Model_MicroArea();
        $tbArea = new Application_Model_Area();
        $this->view->areas = $tbArea->getAreas();
        $this->view->dados = $tbMic->getMicroArea($mic_codigo);
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
