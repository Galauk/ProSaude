<?php

class Agenda_CategoriaDeConveniosController extends Zend_Controller_Action {
    
    public function init(){
    }
    
    public function indexAction(){
        $this->view->title = "Cadastro de Categoria de Convênios";
        $tbCatConv = new Application_Model_CategoriaConvenios();
        $catc_codigo = $this->_request->getParam("catc_codigo"); 
        $this->view->dados = $tbCatConv->listaDados();
        $this->view->dadosEdicao = $tbCatConv->listaDadosEdicao($catc_codigo);
    }
    
    public function salvarAction() {
        $this->view->title = "Cadastro de Categoria de Convênios";
        $catc_nome = $this->_request->getPost("catc_nome");
        $catc_codigo = $this->_request->getPost("catc_codigo",FALSE);
        $dados = array(
            "catc_nome" => $catc_nome
        );
        // Validando Edição
        if ($catc_codigo) {
            $dados["catc_codigo"] = $catc_codigo; 
        }
        $tbCatConv = new Application_Model_CategoriaConvenios();
        if (count($tbCatConv->listaDadosPeloNome($catc_nome))>0 && 
                $catc_codigo == "") {
            $this->view->erro = "Erro! Categoria de convênio já existe!";
            $this->view->dados = $tbCatConv->listaDados();
            return $this->render("categoria-de-convenios/index",NULL,TRUE);
        } else {
            $tbCatConv->salvar($dados);
            $this->view->dados = $tbCatConv->listaDados();
            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
            return $this->render("categoria-de-convenios/index",NULL,TRUE);
        }
    }
    
    public function excluirAction(){
        $tbCatConv = new Application_Model_CategoriaConvenios();
        $catc_codigo = $this->_request->getParam("catc_codigo",FALSE);
        $tbCatConv->excluir($catc_codigo);
    }
    
    public function categoriaConveniosProcedimentosAction(){
        $this->view->title = "Modúlo Convênios Procedimentos";
        $tbCatConv = new Application_Model_CategoriaConvenios();
        $tbCatConvProc = new Application_Model_CategoriaConveniosProcedimentos();
        //$catc_codigo = $this->_request->getParam("catc_codigo",FALSE);
        $catcp_codigo = $this->_request->getParam("id",FALSE);
        $this->view->lista_categorias = $tbCatConv->listaDados();
        //$this->view->dados = $tbCatConvProc->listaDados($catc_codigo);
        $this->view->dados = $tbCatConvProc->listaDados();
        if ($catcp_codigo)
            $this->view->dadosEdicao = $tbCatConvProc->listaDadosEdicao($catcp_codigo);
    }
    
    public function salvarCategoriaConveniosProcedimentosAction() {
        $this->view->title = "Modúlo Convênios Procedimentos";
        $tbCatConv = new Application_Model_CategoriaConvenios();
        $this->view->lista_categorias = $tbCatConv->listaDados();
        $catcp_codigo = $this->_request->getPost("catc_codigo");
        $dados = array(
            "proc_codigo" => $this->_request->getPost("proc_codigo",FALSE),
            "catc_codigo" => $this->_request->getPost("catc_codigo",FALSE)
        );
        // Validando Edição
        if ($this->_request->getPost("catcp_codigo",FALSE)) {
            $dados["catcp_codigo"] = $this->_request->getPost("catcp_codigo",FALSE); 
        }
        try {
            $tbCatConvProc = new Application_Model_CategoriaConveniosProcedimentos();
            $tbCatConvProc->salvar($dados);
            $this->view->dados = $tbCatConvProc->listaDados();
            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
            return $this->render("categoria-de-convenios/categoria-convenios-procedimentos",NULL,TRUE);
        } catch(Exception $exc) {
            $this->view->title = "Modúlo Convênios Procedimentos";
            $tbCatConvProc = new Application_Model_CategoriaConveniosProcedimentos();
            $catc_codigo = $this->_request->getParam("catcp_codigo",FALSE);
            $this->view->dados = $tbCatConvProc->listaDados($catc_codigo);
            $this->view->erro = $exc->getMessage();
            $this->render("categoria-convenios-procedimentos");
        }
    }
    
    public function excluirCategoriaConveniosProcedimentosAction(){
        //$catc_codigo = $this->_request->getParam("catc_codigo",FALSE);
        $catcp_codigo = $this->_request->getParam("id",FALSE);
        $tbCatConvProc = new Application_Model_CategoriaConveniosProcedimentos();
        $tbCatConvProc->excluir($catcp_codigo);
    }
    
    public function buscaConfiguracoesAction(){
        $this->view->title = "Modúlo Convênios Procedimentos";
        $tbCatConv = new Application_Model_CategoriaConvenios();
        $this->view->lista_categorias = $tbCatConv->listaDados();
        $tbCatConvProc = new Application_Model_CategoriaConveniosProcedimentos();
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $busca = $this->_request->getPost("busca",FALSE);
        $this->view->dados = $tbCatConvProc->buscaDadosConfigGrupoDeExames($tipoBusca,$busca); 
        $this->render("categoria-convenios-procedimentos");
    }
    
}

