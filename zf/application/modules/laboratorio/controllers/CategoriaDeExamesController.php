<?php

class Laboratorio_CategoriaDeExamesController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Configurações de Categorias de Exames";
        $tbTipMet = new Application_Model_TipoDeMetodos();
        $tbCatExa = new Application_Model_CategoriaDeExames();
        $tbTipMat = new Application_Model_TipoDeMaterial();
        $this->view->tipodemetodos = $tbTipMet->listaTiposDeMetodos();
        $this->view->tipodematerial = $tbTipMat->listaTipoDeMaterial();
        $this->view->categoriadeexames = $tbCatExa->listaCategoriaDeExames();
    }
    
    public function indexAction(){
        $tbTipExa = new Application_Model_TipoDeExame();
        $cteCodigo = $this->_request->getParam("cte_codigo");
        $this->view->dados = $tbTipExa->listaDadosConfiguracoesDeExames($cteCodigo);
        $this->view->dadosEdicao = $this->listaDadosEdicaoConfiguracao($this->_request->getParam("id"));
    }
    
    public function buscaConfiguracoesAction(){
        $tbTipExa = new Application_Model_TipoDeExame();
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $busca = $this->_request->getPost("busca",FALSE);
        $this->view->dados = $tbTipExa->buscaDadosConfiguracoesDeExames($tipoBusca,$busca); 
        $this->render("index");
    }
    
    public function listaConfiguracoesAction() {
        $tbTipExa = new Application_Model_TipoDeExame();
        $dadosTipExa = $tbTipExa->listaDadosConfiguracoesDeExames();
        return $dadosTipExa; 
    }
    
    public function salvarConfiguracoesAction() {
        // Utilizado em mais de um lugar por isso esta aqui
        $cteCodigo = $this->_request->getPost("categoria_de_exames",FALSE);
        $dados = array(
            "proc_codigo" => $this->_request->getPost("proc_codigo",FALSE),
            "cte_codigo" => $cteCodigo,
            "tma_codigo" => $this->_request->getPost("tipo_de_material") == "" ? NULL : $this->_request->getPost("tipo_de_material"),
            "tpm_codigo" => $this->_request->getPost("tipo_de_metodos") == "" ? NULL : $this->_request->getPost("tipo_de_metodos")
        );
        // Validando Edição
        if ($this->_request->getPost("txa_codigo")) {
            $dados["txa_codigo"] = $this->_request->getPost("txa_codigo"); 
        }
        $tbTipExa = new Application_Model_TipoDeExame();
        try {
            $txaCodigo = $tbTipExa->salvarTipoDeExame($dados);
            $this->salvarOrdemConfiguracaoAction($txaCodigo,$cteCodigo);
            $this->_redirect("/laboratorio/categoria-de-exames/index/cte_codigo/$cteCodigo");
        } catch(Exception $exc) {
            $this->view->erro = $exc->getMessage();
            $this->render("index");
        }
    }
    
    public function salvarOrdemConfiguracaoAction($txaCodigo,$cteCodigo){
        $tbTco = new Application_Model_TipoCategoriaOrdem();
        $ordem = $tbTco->getOrdemConfiguracaoExames($cteCodigo)->tco_ordem+1;
        $dados = array(
            "txa_codigo" => $txaCodigo,
            "cte_codigo" => $cteCodigo,
            "tco_ordem" => $ordem
        );
        $tbTco->salvarOrdemConfiguracaoExames($dados);
    }
    
    public function excluirConfiguracoesAction(){
        $tbTipExa = new Application_Model_TipoDeExame();
        $tbCatOrdem = new Application_Model_TipoCategoriaOrdem();
        $cteCodigo = $this->_request->getParam("cte_codigo",FALSE); 
        $txaCodigo = $this->_request->getParam("id",FALSE);
        $tbCatOrdem->excluirOrdemPorCategoria($txaCodigo);
        $tbTipExa->excluirConfiguracoesDeExamesAction($txaCodigo);
        $this->_redirect("/laboratorio/categoria-de-exames/index/cte_codigo/$cteCodigo");
    }
    
    public function atualizaOrdemConfiguracoesAction(){
        $tbTco = new Application_Model_TipoCategoriaOrdem();
        $ordem =  $this->_request->getPost("ordem",FALSE);
        $ordemCont = 1;
        foreach ($ordem as $item) {
            $tbTco->atualizaOrdemConfiguracoesExames($ordemCont,$item);
            $ordemCont++;
        }
        $this->view->dados = NULL;
        $this->render("dados",NULL,TRUE);
    }
    
    public function listaDadosEdicaoConfiguracao($txaCodigo=FALSE){
        if ($txaCodigo!=FALSE) {
            $tbTipExa = new Application_Model_TipoDeExame();
            $dadosEdicao = $tbTipExa->listaDadosEdicaoConfiguracoesDeExames($txaCodigo);
            return $dadosEdicao;
        }
    }
    
    public function listaCategoriasDeExamesPorProcedimentosAction(){
        $tbCatExa = new Application_Model_CategoriaDeExames();
        $procsCodigo = $this->_request->getPost("procs_codigo",FALSE);
        $dados = $tbCatExa->getCategoriaPorProcedimentos($procsCodigo)->toArray();
        $this->view->dados = $dados;
        return $this->render("dados",NULL,TRUE);
    }
         
}

