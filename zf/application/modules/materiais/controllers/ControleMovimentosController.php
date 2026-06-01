<?php

class Materiais_ControleMovimentosController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Controle de Movimentos";
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/materiais/movimentacao.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/materiais/movimentacao.css','all');
    }
    public function indexAction() {
    // action body
        $tbMov = new Application_Model_Movimento();
        $this->view->itens = $tbMov->getMovimentos(15);
    
    }
    
    public function novoAction(){
        return $this->_redirect("materiais/movimentacao/index");
    }
   
    public function editarAction(){
        $mov_codigo = $this->_getParam("id",FALSE);
        $mov_tipo = $this->_getParam("tipo",FALSE);
        if($mov_tipo == "S"){
            return $this->_redirect("materiais/saida/index/id/$mov_codigo");
        }else if($mov_tipo == "E"){
            return $this->_redirect("materiais/entrada/index/id/$mov_codigo");
        }else if($mov_tipo == "T"){
            return $this->_redirect("materiais/transferencia/index/id/$mov_codigo");
        }
    }
    
    public function buscarAction(){
        if ($this->_request->isPost()) {
            $tbMov = new Application_Model_Movimento();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->mov_tipo = $this->_request->getPost("mov_tipo");
            $this->view->itens = $tbMov->getMovimentos(NULL,$this->view->busca,$this->view->mov_tipo);
            $this->render("index");
        } else {
             $this->_redirect("/materiais/controle-movimentos/index");
        }
    }
}

