<?php

class ProcedimentoController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->acl->allow(NULL, array("buscar"));
        $this->view->title = "Procedimento";
    }

    public function indexAction() {
        $tbProc = new Application_Model_Procedimento();
        $this->view->itens = $tbProc->getItensCadastrados();
        //$tbAte = new Application_Model_Atendimento();
        //$ate = $tbAte->temAtendimento();
        // die("asdfas");
    }

    public function salvarAction() {
        Zend_Registry::get("logger")->log("teste", Zend_Log::INFO);

        if ($this->_request->isPost()) {

            $dados = array(
                "proc_nome" => $this->_request->getPost("proc_nome"),
                "proc_vlsa" => $this->_request->getPost("proc_vlsa", NULL),
                "proc_idade_maxima" => $this->_request->getPost("proc_idade_maxima", null),
                "proc_idade_minima" => $this->_request->getPost("proc_idade_minima", NULL),
                "proc_sexo_novo" => $this->_request->getPost("proc_sexo_novo", NULL),
                "proc_cadastrado_manualmente" => $this->_request->getPost("proc_cadastrado_manualmente", NULL),
                "proc_codigo" => $this->_request->getPost("proc_codigo", NULL)
            );

            try {
                $tbProc = new Application_Model_Procedimento();
                $tbProc->salvar($dados);

                if ($this->_request->getPost("fechaPopUp", 0)) {
                    return;
                }
                $this->_redirect("/agenda/procedimento/");
            } catch (Zend_Validate_Exception $exc) {
                $this->view->erro = $exc->getMessage();
                $this->view->dados = (object) $dados;
                $this->render("novo");
            }
        } else {
            $this->_redirect("agenda/procedimento");
        }
    }

    public function excluirAction() {
        $id = (int) $this->_getParam("id", 0);
        if (!$id)
            return $this->_redirect("/agenda/procedimento");

        $tbProc = new Application_Model_Procedimento();
        $tbProc->excluir($id);

        if ($this->_getParam("json", FALSE)) {
            $this->view->dados = array("success" => TRUE);
            return $this->render("dados", NULL, TRUE);
        }

        return $this->_redirect("/agenda/procedimento");
    }

    public function novoAction() {
        $this->view->fechaPopUp = $this->_getParam("pop", 0);
    }

    public function editarAction() {
        $id = (int) $this->_getParam("id", 0);
        if (!$id)
            return $this->_redirect("/agenda/procedimento");
        $tbProc = new Application_Model_Procedimento();

        $this->view->dados = $tbProc->find($id)->current();
        return $this->render("novo");
    }

    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost("busca");
            $tbProc = new Application_Model_Procedimento();
            $ouch = $tbProc->pesquisar($dados);
            $this->view->itens = $ouch;
            $this->render("index");
        } else {
            $this->_redirect("agenda/procedimento");
        }
    }

    /**
     * Retorna os procedimentos em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarAction() {
        $url = $_SERVER['HTTP_REFERER'];
        $tbProc = new Application_Model_Procedimento();
        $term = $this->_getParam("term", FALSE);
        $esp_codigo = $this->_getParam("esp", FALSE);
        if ((strpos(strtoupper($url), 'REL_') == true)||(strpos(strtoupper($url), 'RELATORIO') == true)){
            $this->view->dados = $tbProc->buscar($term, $esp_codigo);
        } else {
            $this->view->dados = $tbProc->buscarAtivos($term, $esp_codigo);
        }
        return $this->render("dados", NULL, TRUE);
    }

    public function apelidoAction() {
        $tbProc = new Application_Model_Procedimento();
        $this->view->itens = $tbProc->getProcedimentosComApelidos();
    }

    public function formApelidoAction() {
        
    }

    public function editarApelidoAction() {
        $id = (int) $this->_getParam("id", 0);
        if (!$id)
            return $this->_redirect("/agenda/procedimento");
        $tbProc = new Application_Model_Procedimento();

        $this->view->dados = $tbProc->find($id)->current();
        return $this->render("form-apelido");
    }

    public function salvarApelidoAction() {
        if ($this->_request->isPost()) {
            $dados = array(
                "proc_codigo" => $this->_request->getPost("proc_codigo"),
                "proc_apelido" => $this->_request->getPost("proc_apelido", NULL)
            );

            try {
                $tbProc = new Application_Model_Procedimento();
                $tbProc->salvarApelido($dados);

                $this->_redirect("/default/procedimento/apelido");
            } catch (Zend_Validate_Exception $exc) {
                $this->view->erro = $exc->getMessage();
                $this->view->dados = (object) $dados;
                $this->render("apelido");
            }
        } else {
            $this->_redirect("default/procedimento/apelido");
        }
    }

    public function excluirApelidoAction() {
        $id = (int) $this->_getParam("id", 0);
        $dados = array(
            "proc_codigo" => $id,
            "proc_apelido" => null
        );
        $tbProc = new Application_Model_Procedimento();
        $tbProc->salvarApelido($dados);
        $this->_redirect("/default/procedimento/apelido");
    }

    public function buscarApelidoAction() {

        $term = $this->_request->getPost("buscar");
        $tbProc = new Application_Model_Procedimento();
        $result = $tbProc->buscarProcedimentosComApelidos($term);
        $this->view->itens = $result;
        return $this->render("apelido");
    }

    public function removeProcedimentosDuplicadosAction() {
        $procedimento = new Application_Model_Procedimento();
        $procedimentoOndonto = new Application_Model_OdontoProcedimentosRealizados();
        $procedimentoEsus = new Application_Model_EsusFichaProcedimento();
        $procedimentoAgendamento = new Application_Model_AgendamentoExterno();
        
        $result = $procedimento->listaProcedimentosDuplicados();
        
        for ($i=0; $i < count($result); $i++) {
            if (($i % 2) == 1) {
                $resultOdonto = $procedimentoOndonto->listaProcedimentoRealizadoPorCodigo($result[$i]["proc_codigo"]);
                $resultEsus = $procedimentoEsus->listaDadosPorProcedimento($result[$i]["proc_codigo"]);
                $resultAgendamento = $procedimentoAgendamento->listaProcedimentoPorCodigo($result[$i]["proc_codigo"]);
                
                if (count($resultOdonto > 1)) {
                    for ($j=0; $j < count($resultOdonto); $j++) {
                        $procedimentoOndonto->atualizaProcedimentoOdontologico($result[$i-1]["proc_codigo"], $resultOdonto[$j]["proc_codigo"]);
                    }
                }
                if (count($resultEsus > 1)) {
                    for ($k=0; $k < count($resultEsus); $k++) {
                        //die($result[$i-1]["proc_codigo"]." - ". $resultEsus[$k]["proc_codigo"]);
                        $procedimentoEsus->atualizaProcedimentoDadosEsus($result[$i-1]["proc_codigo"], $resultEsus[$k]["proc_codigo"]);
                    }
                }
                if (count($resultAgendamento > 1)) {
                    for ($l=0; $l < count($resultAgendamento); $l++) {
                        //die($result[$i-1]["proc_codigo"]." - ". $resultEsus[$k]["proc_codigo"]);
                        $procedimentoAgendamento->atualizaProcedimentoAgendamentoExterno($result[$i-1]["proc_codigo"], $resultAgendamento[$k]["proc_codigo"]);
                    }
                }
                $procedimento->excluir($result[$i]["proc_codigo"]);
            }          
        }
        
        return die("Procedimentos Duplicados Excluídos com Sucesso!");
    }


    public function buscarExamesAction() {
        $tbProc = new Application_Model_Procedimento();
        $term = $this->_getParam("term", FALSE);
        $esp_codigo = $this->_getParam("esp", FALSE);
        $this->view->dados = $tbProc->buscarExames($term, $esp_codigo);
        return $this->render("dados", NULL, TRUE);
    }
    
    public function recuperaProcedimentosOdontoAction(){
        $tbProc = new Application_Model_Procedimento();
        $term = $this->_getParam("term", FALSE);
        // $this->view->procedimentosOdontoAb = $tbProc->recuperaProcedimentosOdonto($term);
        $this->view->dados = $tbProc->recuperaProcedimentosOdonto($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function recuperaBeneficioConcedidoAction(){
        $tbProc = new Application_Model_Procedimento();
        $term = $this->_getParam("term", FALSE);
        // $this->view->procedimentosOdontoAb = $tbProc->recuperaProcedimentosOdonto($term);
        $this->view->dados = $tbProc->recuperaBeneficioConcedido($term);
        return $this->render("dados", NULL, TRUE);
    }    

    public function buscarAcoesRaasAction(){
        $tbProc = new Application_Model_Procedimento();
        $term = $this->_getParam("term", FALSE);

        $this->view->dados = $tbProc->buscaAcoesRaas($term);
        return $this->render("dados", NULL, TRUE);
    }

}
