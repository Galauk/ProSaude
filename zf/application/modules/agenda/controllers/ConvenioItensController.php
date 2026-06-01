<?php

class Agenda_ConvenioItensController extends Zend_Controller_Action {

	public function init() {
        $this->view->title = "Itens do Convênios";
	}
        
    /* -----------------------------------------------------------------
        * MÉTODOS CONVÊNIOS ITENS AGENDAMENTO ESTABELECIMENTOS DE SAUDE
        * ----------------------------------------------------------------*/
    
    public function agendamentoEstabelecimentosDeSaudeProfissionaisAction(){
        $conv_codigo = $this->carregaDadosEstabelecimentosDeSaude();
        $tbConv = new Application_Model_Convenio();	
        $tbConI = new Application_Model_ConvenioItens();
        $this->view->fds = $tbConv->atendeSabadoEDomingo($conv_codigo);
        $tbEstra = new Application_Model_Estratificacao();
        //var_dump($tbEstra->getEstratificacoes())
        //  $this->view->estratificacoes = $tbEstra->getEstratificacoes();
        // Tratamento de Avisos
        if ($this->_getParam("aviso")!=""){
            $this->view->erro = $this->retornaAvisosAction($this->_getParam("aviso"));
        }
    }
        
    public function agendamentoEstabelecimentosDeSaudeProfissionaisItensAction(){
        $conv_codigo = $this->_getParam("conv", FALSE);
        if (!$conv_codigo){
            return $this->_redirect("/agenda/convenio");
        }
        $tbConI = new Application_Model_ConvenioItens();
        $tbConv = new Application_Model_Convenio();
        $this->view->prestador = $tbConv->getDadosAgendamentoEstabelecimentoDeSaude($conv_codigo)->prestador_servico;
        $this->view->itens = $tbConI->buscarPeloConvenio($conv_codigo);
    }
        
    public function editarAgendamentoEstabelecimentosDeSaudeProfissionaisAction() {
		$conv_codigo = $this->carregaDadosEstabelecimentosDeSaude();
		$coni_codigo = $this->_getParam("id", 0);
        //die($coni_codigo);
		if (!$coni_codigo){
            return $this->_redirect("/agenda/convenio-itens");
        }

		$tbConi = new Application_Model_ConvenioItens();
		$tbDias = new Application_Model_ConvenioDiasSemana();
		$tbDiasAge = new Application_Model_ConvenioDiasSemanaAgendamento();
        $tbHora = new Application_Model_ConvenioHorarios();

		$this->view->itens = $tbConi->buscarPeloConvenio($conv_codigo);
                
        $this->view->dados = $tbConi->buscaEstabelecimentoDeSaude($coni_codigo);
        
        $this->view->dadosDia = $tbDiasAge->listaDadosPordia($coni_codigo);
                
        // Refazer
        //$this->view->atende = $tbDias->getDiasDeAtendimentoArray($coni_codigo);

        $this->view->hora = $tbHora->getHorariosEstabelecimentoDeSaudeArray($coni_codigo);

        $tbConv = new Application_Model_Convenio();
        $this->view->fds = $tbConv->atendeSabadoEDomingo($conv_codigo);    
        
        return $this->render("agendamento-estabelecimentos-de-saude-profissionais");
	}
        
    public function excluirAgendamentoEstabelecimentosDeSaudeProfissionaisAction() {
        $id = (int) $this->_getParam("id", 0);
        $conv = (int) $this->_getParam("conv", 0);

        if (!$id) {
            return $this->_redirect("/agenda/convenio/agendamento-novo-vinculo-estabelecimento-de-saude");
        }

        $tbHora = new Application_Model_ConvenioHorarios();
        $tbHora->excluir($id);

        $tbDias = new Application_Model_ConvenioDiasSemanaAgendamento();
        $tbDias->excluir($id);

        $tbConi = new Application_Model_ConvenioItens();
        $tbConi->excluir($id);

        return $this->_redirect("/agenda/convenio-itens/agendamento-estabelecimentos-de-saude-profissionais/conv/$conv");
	}
        
    private function carregaDadosEstabelecimentosDeSaude(){
        $conv_codigo = $this->_getParam("conv", FALSE);
        if (!$conv_codigo) {
            return $this->_redirect("agenda/convenio/agendamento-novo-vinculo-estabelecimento-de-saude");
        }
        $tbConv = new Application_Model_Convenio();
        $this->view->conv = $tbConv->getDadosAgendamentoEstabelecimentoDeSaude($conv_codigo);
        return $conv_codigo;
    }
        
    public function retornaAvisosAction($aviso){
        switch ($aviso) {
            case "1":
                return "Agenda do profissional já foi criada para esta unidade, por favor realize a sua edição!";
            break;
            case "2":
                return "Agenda do profissional editada com sucesso!<br /> Por favor verifique se algum agendamento foi perdido para as proximas datas! <a href=".$this->view->baseUrl()."/agenda/recepcao".">Clique aqui para verificar</a>";
            break;
        }
    }
        
        
    /* -----------------------------------------------------------------
        * MÉTODOS CONVÊNIOS 
        * ----------------------------------------------------------------*/
    
    public function indexAction() {
        $conv_codigo= $this->carregaDadosConvenio();
        $tbConv = new Application_Model_Convenio();	
        $tbConI = new Application_Model_ConvenioItens();
        $tbGruex = new Application_Model_GrupoExame();
        $this->view->grupos = $tbGruex->getGrupos();
        $this->view->fds = $tbConv->atendeSabadoEDomingo($conv_codigo);
	}
        
    public function itensAction() {
        $conv_codigo = $this->_getParam("conv", FALSE);
        if (!$conv_codigo){
            return $this->_redirect("/agenda/convenio");
        }

        $tbConI = new Application_Model_ConvenioItens();
        $tbConv = new Application_Model_Convenio();
        $this->view->prestador = $tbConv->buscarPeloConv($conv_codigo)->prestador_servico;
        $this->view->itens = $tbConI->buscarPeloConvenio($conv_codigo);
	}
        
    public function getDadosConiCodigoAction(){
        $tbConvIte = new Application_Model_ConvenioItens();
        $coni_codigo = $this->_request->getPost("coni_codigo");
        $this->view->dados = $tbConvIte->getNomeProfissional($coni_codigo)->usr_codigo;
        return $this->render("dados", NULL, TRUE);
    }
        
    /* -----------------------------------------------------------------
    * OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
    * ----------------------------------------------------------------*/
    
    public function buscarAction() {
		$conv_codigo = $this->_getParam("conv_codigo", FALSE);
		$term = $this->_getParam("term", FALSE);
		$tbConI = new Application_Model_ConvenioItens();
		$this->view->dados = $tbConI->buscaSelectProcedimento($conv_codigo, $term);
		//echo "<pre>".  print_r($res,1);

		return $this->render("dados", NULL, TRUE);
		//$this->view->itens = $res;
	}

	/**
	 * Listar os procedimentos* de um convenio
	 * Chamar por post
	 * @param int conv_codigo
	 * @return json
	 */
	public function procedimentosAjaxAction() {
        // die('caiu aqui');
		$conv_codigo = $this->_request->getPost("conv_codigo", FALSE);
		if (!$conv_codigo){
			return $this->_redirect("/agenda/convenio/novo");
        }

		$tbConvI = new Application_Model_ConvenioItens();

		$this->view->dados = $tbConvI->buscarPeloConvenio($conv_codigo, "S");//->toArray();

		return $this->render("dados", NULL, TRUE);
	}

	private function carregaDadosConvenio() {
		$conv_codigo = $this->_getParam("conv", FALSE);
		if (!$conv_codigo) {
			return $this->_redirect("/agenda/convenio/novo");
        }
        
		$tbConv = new Application_Model_Convenio();
		$this->view->conv = $tbConv->buscarPeloConv($conv_codigo);
		return $conv_codigo;
	}

	public function editarAction() {
		$conv_codigo = $this->carregaDadosConvenio();
		$coni_codigo = (int) $this->_getParam("id", 0);
        
        if (!$coni_codigo){
            return $this->_redirect("/agenda/convenio-itens");
        }
        
        $tbConi = new Application_Model_ConvenioItens();
		$tbDias = new Application_Model_ConvenioDiasSemana();
		$tbHora = new Application_Model_ConvenioHorarios();

		$this->view->itens = $tbConi->buscarPeloConvenio($conv_codigo);
		$this->view->dados = $tbConi->busca($coni_codigo);

		$this->view->atende = $tbDias->getDiasDeAtendimentoArray($coni_codigo);
		
        $this->view->hora = $tbHora->getHorariosArray($coni_codigo);
        $tbGruex = new Application_Model_GrupoExame();
        $this->view->grupos = $tbGruex->getGrupos();

        $tbConv = new Application_Model_Convenio();
        $this->view->fds = $tbConv->atendeSabadoEDomingo($conv_codigo);    

		return $this->render("index");
	}

	public function excluirAction() {
		$id = (int) $this->_getParam("id", 0);
		$conv = (int) $this->_getParam("conv", 0);

		if (!$id) {
			return $this->_redirect("/agenda/convenio-itens");
		}

		$tbHora = new Application_Model_ConvenioHorarios();
		$tbHora->excluir($id);

		$tbDias = new Application_Model_ConvenioDiasSemana();
		$tbDias->excluir($id);

		$tbConi = new Application_Model_ConvenioItens();
		$tbConi->excluir($id);

		if ($this->_getParam("json", FALSE)) {
			$this->view->dados = array("success" => TRUE);
			return $this->render("dados", NULL, TRUE);
		}

		return $this->_redirect("/agenda/convenio-itens/index/conv/$conv");
	}

	public function carregaEspecialidadeAction() {
		$usr_codigo = (int) $this->_getParam("usr_codigo", false);
		$conv_codigo = (int) $this->_getParam("conv_codigo", false);
		$tbMes = new Application_Model_MedicoEspecialidade();
		$this->view->dados = $tbMes->getEspecialidadePorMedico($usr_codigo)->toArray();
        
        return $this->render("dados", NULL, TRUE);
	}

	public function carregaEspecialidadePorConvenioAction() {
		$conv_codigo = $this->_getParam("conv_codigo", false);
		$usr_codigo = $this->_getParam("usr_codigo", false);
		$tbMes = new Application_Model_MedicoEspecialidade();
		$this->view->dados = $tbMes->getEspecialidadePorConvenio($conv_codigo, $usr_codigo)->toArray();
        
        return $this->render("dados", NULL, TRUE);
	}
        
    public function salvarAction(){
        $tbConi = new Application_Model_ConvenioItens();
        $tbCondi = new Application_Model_ConvenioDiasSemanaAgendamento();
        $tbConHor = new Application_Model_ConvenioHorarios();
        // Validando Inserção do Convênio Itens
        // Verifica se não é edição
        if ($this->_request->getPost("coni_codigo") == "" && $this->_request->getPost("conv_codigo") != "" && $this->_request->getPost("esp_codigo") != "" && $this->_request->getPost("usr_codigo") != ""){
            // Se não for edição, verifica se esta duplicado
            if($tbConi->confereConvItens($this->_request->getPost("conv_codigo"), $this->_request->getPost("esp_codigo"), $this->_request->getPost("usr_codigo"))->qtd_conv > 0){
                return $this->_redirect("agenda/convenio-itens/agendamento-estabelecimentos-de-saude-profissionais/conv/".$this->_request->getPost("conv_codigo")."/aviso/01");
            }
        }

        // Salvando o convênio itens
        $array_convenio_itens = array(
            "usr_codigo" => $this->_request->getPost("usr_codigo"),
            "esp_codigo" => $this->_request->getPost("esp_codigo"),
            "conv_codigo" => $this->_request->getPost("conv_codigo"),
            "coni_tipo_origem" => "P",
            "coni_tipo_prestador" => "Q",
            "coni_data_inicio" => ($this->_request->getPost("coni_data_inicio") ? $this->_request->getPost("coni_data_inicio") : NULL),
            "coni_data_termino" => ($this->_request->getPost("coni_data_termino") ? $this->_request->getPost("coni_data_termino") : NULL),
            "coni_ativo" => ($this->_request->getPost("coni_ativo") ? $this->_request->getPost("coni_ativo") : "S")
        );

        // Tratando a edição do convênio itens, dias e horários
        if ($this->_request->getPost("coni_codigo") != ""){
            $array_convenio_itens["coni_codigo"] = $this->_request->getPost("coni_codigo");
            $tbHora = new Application_Model_ConvenioHorarios();
            $tbHora->excluir($this->_request->getPost("coni_codigo"));
            $tbDias = new Application_Model_ConvenioDiasSemanaAgendamento();
            $tbDias->excluir($this->_request->getPost("coni_codigo"));
            // Gera um aviso pra conferir se não existe horários a serem realocados
            $aviso2 = "/aviso/02";
        }
        // Validando a inserção do convênio itens
        try{
            $coni_codigo = $tbConi->salvar($array_convenio_itens);
        } catch (Zend_Validate_Exception $exc) {
            die($exc->getMessage());
        }
        
        //Tirando os campos em branco do POST que não são utilizado           
        foreach ($_POST as $ind => $val){
            if(($_POST[$ind] == null || $_POST[$ind] == "" )){
                unset($_POST[$ind]);
            }
        }
        // Lendo POSTS já limpo
        $valor_dia = 0;
        foreach ($_POST as $ind => $val){
            // Verificando se indice é numerico
            if(is_numeric($ind[0])) {
                // Conferindo pra passar apenas uma vez e salvar o que tem que salvar
                if($ind[0] != $valor_dia){
                    // Array de dias da semana que o profissional atende
                    $valor_dia = $_POST[$ind[0]."dia"];
                    $data_convenio_dias_semanas_agendamento = array(
                        "condi_age_dia" => $ind[0],
                        "coni_codigo" => $coni_codigo,
                        "condi_age_cota_dia" => $_POST[$ind[0]."condi_age_cota_dia"],
                        "condi_age_intervalo" => $_POST[$ind[0]."condi_age_intervalo"],
                        "condi_age_encaixe" => ($_POST[$ind[0]."condi_age_encaixe"] ? $_POST[$ind[0]."condi_age_encaixe"] : "0"));
                    // Validando a inserção de dias de atendimento
                    try{
                        $condi_age_codigo = $tbCondi->salvar($data_convenio_dias_semanas_agendamento);
                    } catch (Zend_Validate_Exception $exc) {
                        die("Dias".$exc->getMessage());
                    }
                    // Indice Auxiliar de Horas
                    for($i=1; $i<=5; $i++){
                        if ($_POST[$valor_dia."_hora_inicial_".$i] != "" && $_POST[$valor_dia."_hora_final_".$i] != ""){
                            // Montando array de horários através do convênio item e o dia da semana
                            $data_convenio_horarios = array(
                                "hora_inicial"=>$_POST[$valor_dia."_hora_inicial_".$i],
                                "hora_final"=>$_POST[$valor_dia."_hora_final_".$i],
                                "coni_codigo"=>$coni_codigo,
                                "condi_age_codigo"=>$condi_age_codigo
                            );
                            // Validando a inserção em banco 
                            try {
                                $tbConHor->salvarConvHorAgendamentoEstDeSaude($data_convenio_horarios);
                            } catch (Zend_Validate_Exception $exc) {
                                die("Horários".$exc->getMessage());
                            }
                        }
                    }
                }
            }
        }
        //$this->view->dialog = array("Confirmação","Encaminhamento registrado com sucesso!",300,140);
        return $this->_redirect("agenda/convenio-itens/agendamento-estabelecimentos-de-saude-profissionais/conv/".$this->_request->getPost("conv_codigo")."$aviso2");
    }
        
    public function salvarProcedimentoLaboratorioAction() {
        $this->view->title = "Cadastro de convênio";
        // echo "<pre>".print_r($_POST,1);exit;

        // Validando se está vindo dados via post
        if ($this->_request->isPost()) {
            // Criando o array com os dados para inserir no banco
            $tbGruex = new Application_Model_GrupoExame();
            $this->view->grupos = $tbGruex->getGrupos();
            $dados = array(
                "proc_codigo" => $this->_request->getPost("proc_codigo", NULL),
                "esp_codigo" => $this->_request->getPost("esp_codigo", NULL),
                "usr_codigo" => $this->_request->getPost("usr_codigo", NULL),
                "conv_codigo" => $this->_request->getPost("conv_codigo", FALSE),
                "coni_codigo" => $this->_request->getPost("coni_codigo", FALSE),
                "coni_valor" => $this->_request->getPost("coni_valor", NULL),
                "coni_cota_mes" => $this->_request->getPost("coni_cota_mes", -1),
                "coni_cota_dia" => $this->_request->getPost("coni_cota_dia", -1),
                "coni_cota_mes_original" => $this->_request->getPost("coni_cota_mes_original", -1),
                "coni_cota_dia_original" => $this->_request->getPost("coni_cota_dia_original", -1),
                "coni_tipo_origem" => $this->_request->getPost("prestador", "P"),
                "coni_tipo_prestador" => $this->_request->getPost("coni_tipo_prestador", "Q"),
                "coni_ativo" => $this->_request->getPost("coni_ativo"),
                "coni_encaixe" => $this->_request->getPost("coni_encaixe", NULL),
                "coni_intervalo" => $this->_request->getPost("coni_intervalo", NULL),
                "tipo_form"=>"P",
                "gruex_codigo" => ($this->_request->getPost("gruex_codigo") != "" ? $this->_request->getPost("gruex_codigo") : null) 
            );

            // Verifica se tem as datas se tiver inclui
            if ($this->_request->getPost("coni_data_inicio")) {
                $dados["coni_data_inicio"] = $this->_request->getPost("coni_data_inicio");
            }

            if ($this->_request->getPost("coni_data_termino")) {
                $dados["coni_data_termino"] = $this->_request->getPost("coni_data_termino");
            }

            try {
                // Chamando o model de convênio itens
                $tbConv = new Application_Model_ConvenioItens();
                // Enviando os dados para salvar no banco
                $coni_codigo = $tbConv->salvar($dados);
                // Remove os horários dos convênios pra atualizar posteriormente
                $tbHora = new Application_Model_ConvenioHorarios();
                $tbHora->excluir($coni_codigo);
                // Colocando os horários em array e salvando em banco	
                for ($i = 1; $i <= $this->_request->getPost("qtd_hr", 0); $i++) {
                    $dadosHora = array(
                        "coni_codigo" => $coni_codigo,
                        "hora_inicial" => $this->_request->getPost("hr_inicio" . $i, FALSE),
                        "hora_final" => $this->_request->getPost("hr_fim" . $i, FALSE)
                    );

                    $tbHora->salvar($dadosHora);
                }
                // Chamando o model de dias da semana
                $tbDias = New Application_Model_ConvenioDiasSemana();
                // Removendo os dias cadastrados, para atualizar posteriormente 
                $tbDias->excluir($coni_codigo);
                $dias = $this->_request->getPost("dias", 0);
                foreach ($dias as $value) {
                    $dadosDias = array(
                        "coni_codigo" => $coni_codigo,
                        "condi_dia" => $value
                    );

                    $tbDias->salvar($dadosDias);
                }
                // Redirecionando a página, tratamento de erros
                return $this->_redirect("/agenda/convenio-itens/index/conv/" . $dados["conv_codigo"]);
                // Tratamento de erro
            } catch (Zend_Validate_Exception $exc) {
                $this->view->erro = $exc->getMessage();
                $this->view->dados = (object) $dados;
                $this->carregaDadosConvenio();
                return $this->render("index");
            }
        } else {
            $this->_redirect("/agenda/convenio/novo");
        }
    }
}