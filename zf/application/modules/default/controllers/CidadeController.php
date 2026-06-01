<?php

class CidadeController extends Zend_Controller_Action {

    public function init() {
            $this->_helper->acl->allow(NULL,array("buscar"));
    }

    public function indexAction() {
            // action body
    }

    /**
     * Retorna a cidadades em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarAction(){
        $tbCid = new Application_Model_Cidade();
        $term = $this->_getParam("term",FALSE);
        $this->view->dados = $tbCid->buscar($term);
        return $this->render("dados", NULL, TRUE);
    }

    /**
     * Retorna a cidadades em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarTbCidadeAction(){
        $tbCid = new Application_Model_TbLocalidade();
        $term = $this->_getParam("term",FALSE);
        $this->view->dados = $tbCid->buscar($term);
        return $this->render("dados", NULL, TRUE);
    }

    // Verifica se bairro existe ou não
    public function validaCidadeAction() {
        $tbLoc = new Application_Model_Cidade();
        $cidade = $this->_request->getPost("cidade");
        $cidade_codigo = $tbLoc->getDadosCidade($cidade)->co_localidade;
        // Se bairro não existir, insere
        if (!empty($cidade_codigo)){
            $this->view->dados = $cidade_codigo;
        } else {
            $this->view->dados = "erro";
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    public function buscaCidadePeloNomeAction(){
        $cidade = $this->_request->getPost("cidade");
        $tbCid = new Application_Model_Cidade();
        $this->view->dados = $tbCid->buscaCidadePeloNome($cidade)->toArray();
        return $this->render("dados",NULL,TRUE);
    }
    
    public function getCidadePorDistritoAction(){
        $dis_codigo = $this->_getParam("dis_codigo",FALSE);
        $tbCidade = new Application_Model_Cidade();
        $this->view->dados = $tbCidade->getCidadePorDistrito($dis_codigo)->toArray();
        return $this->render("dados",null,true);
        
    }
    
}

