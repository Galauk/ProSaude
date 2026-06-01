<?php

class Laboratorio_GrupoDeExamesController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Configurações de Categorias de Exames";
        $tbGrupoEx = new Application_Model_GrupoExame();
        $this->view->grupoexame = $tbGrupoEx->getGrupos();
    }

    public function indexAction(){
        $tbTipGruEx = new Application_Model_TipoExameGrupo();
        $gruexCodigo = $this->_request->getParam("gruex_codigo"); 
        $this->view->gruex_codigo = $gruexCodigo;
        $this->view->dados = $tbTipGruEx->listaDadosConfigGrupoDeExames($gruexCodigo);
        $this->view->dadosEdicao = $this->listaDadosEdicaoConfiguracao($this->_request->getParam("id"));
    }
        public function grupoPrenatalAction(){
        $this->view->title = "Procedimentos do Grupo de Exames";
        $this->_helper->layout->setLayout("vazio");
        $codGrupo = $this->_request->getParam("gruex_codigo");
        $inserir = $this->_request->getParam("inserir");
        $tbGruex = new Application_Model_GrupoExame();
        $tbGruProc = new Application_Model_TipoExameGrupo();
        if ($codGrupo) {

            $this->view->grupo_exame = $tbGruex->getGruposPorId($codGrupo);
            $this->view->procedimentos = $tbGruProc->listaDadosConfigGrupoDeExames($codGrupo);
        }
        if($inserir){
            $this->view->dados = $tbGruProc->listaDadosConfigGrupoDeExames($codGrupo)->toArray();
            return $this->render("dados", NULL,TRUE);
        }
    }
    
    
    public function salvarConfigGrupoDeExamesAction() {
        // Utilizado em mais de um lugar por isso esta aqui
        $tbGruex = new Application_Model_TipoExameGrupo();
        $gruexCodigo = $this->_request->getPost("grupo_de_exames",FALSE);
        $ordemGruEx = $tbGruex->getOrdemConfigGrupoDeExames($gruexCodigo)->tcg_ordem+1;
        $dados = array(
            "proc_codigo" => $this->_request->getPost("proc_codigo",FALSE),
            "gruex_codigo" => $gruexCodigo,
            "tcg_ordem" => $ordemGruEx
        );
        // Validando Edição
        if ($this->_request->getPost("txg_codigo",FALSE)) {
            $dados["txg_codigo"] = $this->_request->getPost("txg_codigo",FALSE); 
        }
        try {
            $tbGruex = $tbGruex->salvarConfigGrupoDeExames($dados);
            $this->_redirect("/laboratorio/grupo-de-exames/index/gruex_codigo/$gruexCodigo");
        } catch(Exception $exc) {
            $this->view->erro = $exc->getMessage();
            $this->render("index");
        }
    }
    
    public function atualizaOrdemConfiguracoesAction(){
        $tbTipGruex = new Application_Model_TipoExameGrupo();
        $ordem =  $this->_request->getPost("ordem",FALSE);
        $ordemCont = 1;
        foreach ($ordem as $item) {
            $tbTipGruex->atualizaOrdemConfigGrupoDeExames($ordemCont,$item);
            $ordemCont++;
        }
        $this->view->dados = NULL;
        $this->render("dados",NULL,TRUE);
    }
    
    public function excluirConfiguracoesAction(){
        $tbTipGruex = new Application_Model_TipoExameGrupo();
        $gruexCodigo = $this->_request->getParam("gruex_codigo",FALSE);
        $txgCodigo = $this->_request->getParam("id",FALSE);
        $tbTipGruex->excluirConfigGrupoDeExamesAction($txgCodigo);
        $this->_redirect("/laboratorio/grupo-de-exames/index/gruex_codigo/$gruexCodigo");
    }
    
    public function listaDadosEdicaoConfiguracao($txgCodigo=FALSE){
        if ($txgCodigo!=FALSE) {
            $tbTipGruex = new Application_Model_TipoExameGrupo();
            $dadosEdicao = $tbTipGruex->listaDadosEdicaoConfigGrupoDeExames($txgCodigo);
            return $dadosEdicao;
        }
    }
    
    public function buscaConfiguracoesAction(){
        $tbTipGruex = new Application_Model_TipoExameGrupo();
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $busca = $this->_request->getPost("busca",FALSE);
        $this->view->dados = $tbTipGruex->buscaDadosConfigGrupoDeExames($tipoBusca,$busca); 
        $this->render("index");
    }
    
    public function formAction(){
        $this->_helper->layout->disableLayout();
	$this->view->title = "Cadastro de Grupo de Exames";
    }
    
    public function salvarFormGrupoDeExameAction(){
        $grupoDescricao = $this->_request->getPost("grupo_exame_nome",FALSE);
        try {
            // Validando se já existe grupo cadastrado
            $tbGruEx = new Application_Model_GrupoExame();
            if (count($tbGruEx->getGruposPorNome($grupoDescricao)) == 0){
                $dados = array("gruex_descricao"=>$grupoDescricao);
                $codGruEx = $tbGruEx->salvar($dados);
                $this->view->dados = trim($codGruEx);
            } else {
                $this->view->dados = "Erro! Grupo cadastrado já existe!";
            }
            return $this->render("dados", NULL, TRUE);
        } catch (Exception $exc) {
            $this->view->dados = $exc->getMessage();
            return $this->render("dados", NULL, TRUE);
        }
    }
}

