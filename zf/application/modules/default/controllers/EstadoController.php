<?php

class EstadoController extends Zend_Controller_Action {

    public function buscarAction(){
        $tbEst = new Application_Model_TbEstado();
        $this->view->dados = $tbEst->buscar($this->_request->getParam("term"));
        return $this->render("dados",NULL,TRUE);
    }
    
    public function buscarPorNomeAction(){
        $tbEst = new Application_Model_Estado();
        $this->view->dados = $tbEst->buscar($this->_request->getParam("term"));
        return $this->render("dados",NULL,TRUE);
    }
    
    // Verifica se estado existe ou não
    public function validaEstadoAction() {
        $tbEst = new Application_Model_TbEstado();
        $estado = $this->_request->getPost("estado");
        $estado_codigo = $tbEst->getDadosEstado($estado)->co_uf;
        // Se bairro não existir, insere
        if (!empty($estado_codigo)){
            $this->view->dados = $estado_codigo;
        } else {
            $this->view->dados = "erro";
        }
        return $this->render("dados",NULL,TRUE);
    }
	
}

