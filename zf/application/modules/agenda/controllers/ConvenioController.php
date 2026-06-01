<?php

class Agenda_ConvenioController extends Zend_Controller_Action {

    public function init() {
        $this->view->title = "Convênios";
    }

    /* -----------------------------------------------------------------
     * MÉTODOS CONVÊNIOS AGENDAMENTO ESTABELECIMENTOS DE SAUDE
     * ---------------------------------------------------------------- */

    // Lista as unidades que podem possuir convênio com os profissionais
    public function agendamentoEstabelecimentosDeSaudeAction() {
        $this->view->title = "Estabelecimentos de Saúde Vinculados ao Agendamento";
        $tbConv = new Application_Model_Convenio();
        $this->view->itens = $tbConv->pesquisaAgendamentoEstabelecimentosDeSaude(FALSE, 1);
    }

    // Pesquisa convênio de unidades com profissionais
    public function pesquisaAgendamentoEstabelecimentosDeSaudeAction() {
        $this->view->title = "Estabelecimentos de Saúde Vinculados ao Agendamento";
        if ($this->_request->isPost()) {
            $tbConv = new Application_Model_Convenio();
            $this->view->itens = $tbConv->pesquisaAgendamentoEstabelecimentosDeSaude($this->_request->getPost("busca"));
            $this->render("agendamento-estabelecimentos-de-saude");
        } else {
            $this->_redirect("convenio/agendamento-estabelecimentos-de-saude");
        }
    }

    // Chama a nova tela de cadastro de convênio de unidades com os profissionais
    public function agendamentoNovoVinculoEstabelecimentoDeSaudeAction() {
        $this->view->title = "Novo Vinculo Estabelecimento de Saúde Agendamento";
    }

    // Busca Genérica dos Estabelecimentos de Saúde(Unidades) cadastradas
    public function buscarEstabelecimentosDeSaudeAction() {
        $term = $this->_getParam("term", FALSE);
        $tbConv = new Application_Model_Convenio();
        $this->view->dados = $tbConv->buscarEstabelecimentosDeSaude($term);
        return $this->render("dados", NULL, TRUE);
    }

    // Cadastro de vinculo do Agendamento(Convênio) com o Estabelecimento(Unidade)
    public function salvarVinculoAgendamentoEstabelecimentoDeSaudeAction() {
        $dados = array(
            "uni_codigo" => $this->_request->getPost("codigo_convenio"),
            "conv_sabado" => $this->_request->getPost("sabado", "F"),
            "conv_domingo" => $this->_request->getPost("domingo", "F"),
            "conv_status" => true
        );
        try {
            $tbConv = new Application_Model_Convenio();
            $pk = $tbConv->salvarVinculoAgendamentoEstabelecimentoDeSaude($dados);
            $this->_redirect("agenda/convenio-itens/agendamento-estabelecimentos-de-saude-profissionais/conv/$pk");
        } catch (Zend_Validate_Exception $exc) {
            $this->view->erro = $exc->getMessage();
            $this->view->dados = (object) $dados;
            $this->render("agendamento-novo-vinculo-estabelecimento-de-saude");
        }
    }

    // Desativa o vinculo de Agendamento(Convênio) com o Estabelecimento(Unidade)
    public function excluirVinculoAgendamentoEstabelecimentoDeSaudeAction() {
        $convCodigo = $this->_request->getPost("conv_codigo");
        $uniCodigo = $this->_request->getPost("uni_codigo");
        $tbConv = new Application_Model_Convenio();
        $tbConv->excluirVinculoAgendamentoEstabelecimentoDeSaude($convCodigo, $uniCodigo);
        $this->view->dados = "excluido";
        $this->render("dados", NULL, TRUE);
    }

    // Retorna número de agendamento para os proximos dias no Estabelecimento(Unidade)
    public function getNumAgendamentoEstabelecimentoDeSaudeAction() {
        $uniCodigo = $this->_request->getPost("uni_codigo");
        $tbConv = new Application_Model_Convenio();
        $numAge = $tbConv->getNumAgendamentoEstabelecimentoDeSaude($uniCodigo)->numAge;
        $this->view->dados = $numAge;
        return $this->render("dados", NULL, TRUE);
    }

    public function getDadosConvAgendamentoEstabelecimentoDeSaudeAction() {
        $uniCod = $this->_request->getPost("uni_codigo");
        $usrCod = $this->_request->getPost("usr_codigo");
        $espCod = $this->_request->getPost("esp_codigo");
        $tbConv = new Application_Model_Convenio();
        $this->view->dados = $tbConv->getDadosConvAgendamentoEstabelecimentoDeSaude($uniCod, $usrCod, $espCod)->toArray();
        return $this->render("dados", NULL, TRUE);
    }

    /* -----------------------------------------------------------------
     * MÉTODOS CONVÊNIOS 
     * ---------------------------------------------------------------- */

    // Métod o responsavel por chama a listagem de laboratórios conveniados
    public function indexAction() {
        $this->view->title = "Convênios";
        $tbConv = new Application_Model_Convenio();
       // die("asdfasdf");
      //  $this->view->convenio = $tbConv->selectTag();
        $this->view->itens = $tbConv->pesquisar(FALSE, 1);
    }

    // Métod o responsavel por chamar a tela de cadastro de um novo convênio de laboratório
    public function novoAction() {
        $conv_codigo = $this->_getParam("conv_codigo", FALSE);
        if ($conv_codigo) {
            $this->view->title = "Edição de Convênio";
            $tbConv = new Application_Model_Convenio();
            $this->view->dados = $tbConv->buscaDados($conv_codigo);
     //   die("asdfasdf");
        } else {
            $this->view->title = "Cadastro de Convênio";
        }
    }

    // Realiza a busca de convênios
    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $this->view->busca = $this->_request->getPost("busca");
            $tbProc = new Application_Model_Convenio();
            $this->view->itens = $tbProc->pesquisar($this->view->busca);
            $this->render("index");
        } else {
            $this->_redirect("agenda/convenio");
        }
    }

    // Realiza as busca pelos laboratório de acordo com os termos digitados
    public function buscarConveniosAction() {
        $term = $this->_getParam("term", FALSE);
        $tbConv = new Application_Model_Convenio();
        $this->view->dados = $tbConv->buscarConvenios($term);
        return $this->render("dados", NULL, TRUE);
    }

    // Métod o responsavel por salvar o novo convênio
    public function salvarAction() {
        $conv_codigo = $this->_getParam("conv_codigo", FALSE);
        
        if ($conv_codigo) {
            $this->view->title = "Edição de Convênio";
        } else {
            $this->view->title = "Cadastro de convênio";
        }
        $codConv = $this->_request->getPost("codigo_convenio");
        $dados = array(
            "med_codigo" => $codConv,
            "conv_sabado" => $this->_request->getPost("sabado", "F"),
            "conv_domingo" => $this->_request->getPost("domingo", "F"),
            "conv_status" => $this->_request->getPost("conv_status"),
            "conv_valor_total" => $this->_request->getPost("conv_valor_total"),
            "conv_valor_mensal" => $this->_request->getPost("conv_valor_mensal"),
            "dia_mes" => $this->_request->getPost("dia_mes"),
            "data_inicial" => $this->_request->getPost("data_inicial"),
            "data_final" => $this->_request->getPost("data_final"),
            "tipo_convenio" => $this->_request->getPost("tipo_convenio"),
            "max_dia" => $this->_request->getPost("max_dia"),
            "max_mes" => $this->_request->getPost("max_mes"),
            "max_total" => $this->_request->getPost("max_total"),
            "margem_mensal" => $this->_request->getPost("margem_mensal"),
            "margem_total" => $this->_request->getPost("margem_total")
        );
        if($this->_request->getPost("conv_codigo")){
            $dados["conv_codigo"] = $this->_request->getPost("conv_codigo");
        }
        try {
            $tbConv = new Application_Model_Convenio();
            $pk = $tbConv->salvarConvenio($dados);
            return $this->_redirect("/agenda/convenio-itens/index/conv/$pk");
        } catch (Zend_Validate_Exception $exc) {
            $this->view->erro = $exc->getMessage();
            $this->view->dados = (object) $dados;
            $this->render("novo");
        }
    }

    public function excluirAction() {
        $convCodigo = $this->_request->getPost("conv_codigo");
        $tbConv = new Application_Model_Convenio();
        $tbConv->excluir($convCodigo);
        $this->view->dados = "excluido";
        $this->render("dados", NULL, TRUE);
    }
    
    
    public function liberarCotaAction() {
        $convCodigo = $this->_request->getPost("conv_codigo");
        $medCodigo = $this->_request->getPost("med_codigo");
        $dataInicial = $this->_request->getPost("data_inicio");
        $dataFinal = $this->_request->getPost("data_fim");
        $tbConv = new Application_Model_AgendaItens();
        $tbConv->liberarCota($convCodigo,$medCodigo,$dataInicial,$dataFinal);
        $this->view->dados = "liberado";
        $this->render("dados", NULL, TRUE);
    }

    public function getNumConvAgendadosAction() {
        $medCodigo = $this->_request->getPost("med_codigo");
        $tbConv = new Application_Model_Convenio();
        $numConvAgendado = $tbConv->getNumConvAgendados($medCodigo)->numConvAgendado;
        $this->view->dados = $numConvAgendado;
        return $this->render("dados", NULL, TRUE);
    }

    /* -----------------------------------------------------------------
     * OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
     * ---------------------------------------------------------------- */

    /**
     * Busca as unidades, laboratórios e hospitais conveniados
     * @example Para buscar somente os já cadastrados: /WebSocialSaude/zf/agenda/convenio/buscar/?term=nome
     * @example Para buscar todos os prestadores e unidades: /WebSocialSaude/zf/agenda/convenio/buscar/?term=nome&todos=1
     */
    public function buscarAction() {
        $term = $this->_getParam("term", FALSE);
        if (!$term)
            return false;

        $tbConv = new Application_Model_Convenio();
        $limite = $this->_getParam("limite", FALSE);
        $somenteConveniados = !$this->_getParam("todos", FALSE);

        $this->view->dados = $tbConv->buscar($term, $limite, $somenteConveniados);

        return $this->render("dados", NULL, TRUE);
    }

    public function atendeAction() {
        $this->_helper->layout->disableLayout();
        /*
          conv_codigo: id,
          tipo: 'sabado',
          to: to
         */
        $dados = array(
            "conv_codigo" => $this->_request->getPost("conv_codigo", NULL),
            "to" => $this->_request->getPost("to", NULL),
            "tipo" => $this->_request->getPost("tipo", NULL),
        );

        try {
            $tbConv = new Application_Model_Convenio();
            $pk = $tbConv->salvar($dados);
            $this->view->dados = $dados["to"];
        } catch (Zend_Validate_Exception $exc) {
            die($exc->getMessage());
            $this->view->dados = "Ocorreu um erro desconhecido";
            if (APPLICATION_ENV == "development")
                $this->view->dados = $exc->getMessage();
        }
    }

}
