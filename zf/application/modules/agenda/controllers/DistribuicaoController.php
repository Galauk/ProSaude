<?php

class Agenda_DistribuicaoController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Distribuição de Vagas";
        }

	public function indexAction() {
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.contextMenu.js');
                $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/agenda/convenio-itens.js');
		$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/jquery.contextMenu.css','all');
	}
	
	public function selecionarDataAction(){
		$this->_helper->layout->disableLayout();
		$coni_codigos = $this->_getParam("procs", FALSE);
		$this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));
		if(!$coni_codigos)
			return $this->_helper->viewRenderer->setNoRender(true);
		$coni_codigos = explode(",", $coni_codigos);
                $tbAge = new Application_Model_Agenda();
		$tbAgen = new Application_Model_Agendamento();
		$tbConI = new Application_Model_ConvenioItens();
		$this->view->data_final = $tbAge->calculaDataFinal($this->view->data_inicial,true);
		// Verifica a quantidade de vagas e monta um array com as informações para exibição, dia, horário, qtd agendado, profissional e etc ..
                $this->view->vagas = $tbAge->getTotalVagasArr($coni_codigos, $this->view->data_inicial, $this->view->data_final);
                
                $this->view->nomeProcs = $tbConI->getNomeProcedimentos($coni_codigos);
	}
        
        public function selecionarHorarioAction(){
            $this->_helper->layout->disableLayout();
            // Passa por post código do convênio, código do convênio item e data inicial
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $conv_codigo = $this->_getParam("conv_codigo", FALSE);
            $this->view->data_inicial = $this->_getParam("ds", date("Y-m-d"));
            // Se código do convênio item não existir redireciona a página
            if(!$coni_codigo){
                return $this->_helper->viewRenderer->setNoRender(true);
            }
            $tbAge = new Application_Model_Agendamento();
            $tbConI = new Application_Model_ConvenioItens();
            $tbConH = new Application_Model_ConvenioHorarios();
            $tbConvAge = new Application_Model_ConvenioDiasSemanaAgendamento();
            $tbFun = new Application_Model_Funcoes();
            $tbGrap = new Application_Model_GradePeriodo();
            // Criar array de horários com hora inicial e final, se for exceção pega da grade periodo, se não pega da tabela de convenio_horários
            // Inverte para o formato correto pra que se retornasse o ano corretamente
            //echo $atendeQueDia = $tbFun->diaSemana($tbFun->invertData($this->view->data_inicial,"/","-"));
            $atendeQueDia = $tbFun->diaSemana($this->view->data_inicial);
            $condiAgeCod = $tbConvAge->getDadosDia($coni_codigo,$atendeQueDia)->condi_age_codigo;
            $condiAgeEnc = $tbConvAge->getDadosDia($coni_codigo, $atendeQueDia)->condi_age_encaixe;
            $horarios = $tbGrap->getHorariosDia($coni_codigo, $this->view->data_inicial,$condiAgeCod);
            // Se existir Horários exibe
            if ($horarios) {
                // Calcula quantidade de intervalo dos horários e joga em um array
                $quantidades = $tbFun->calculaQuantidadePorIntervalo($horarios,$coni_codigo,$this->view->data_inicial,$condiAgeCod);
                // Realiza um arry de distribuição de horários pela quantidade, horarios e etc ..
                $distribuicao = $tbFun->distribuicao($quantidades,$horarios,$coni_codigo,$this->view->data_inicial,$condiAgeCod);
                $this->view->distribuicao = $distribuicao;
                if($this->_getParam("disponiveis", FALSE)){
                    $this->view->dados = $distribuicao;
                    return $this->render("dados",NULL,TRUE);
                }
            } else {
                $this->view->dados = "Horarios nao cadastrado, realize uma troca ou regularize a agenda!";
                return $this->render("dados",NULL,TRUE);
            }
        }

	/**
	 * Salvar
	 * Acessar por post/ajax
	 * @return json
	 */
	public function salvarAction(){
		if ($this->_request->isPost()) {
			
			$conis = $this->_request->getPost("coni", array());
			$original = $this->_request->getPost("original", array());
			$mes = $this->_request->getPost("mes", array());
			$mesOriginal = $this->_request->getPost("mes_original", array());
				
			try {		
				$tbGrad = new Application_Model_GradeDia();
				$alterados = $tbGrad->salvarDoArray($conis, $original);
				
				$tbGram = new Application_Model_GradeMes();
				$alterados += $tbGram->salvarDoArray($mes, $mesOriginal);				
				
				$this->view->dados = array("success"=>TRUE,"alterados"=>$alterados);
				
			} catch (Zend_Validate_Exception $exc) { // Exceção de validação
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
				
			} catch (Zend_Exception $exc) { // Exceção de login
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Faça login", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
			}
			
			return $this->render("dados", NULL, TRUE);
		} else {
			$this->_redirect("/agenda/distribuicao");
		}
	}
        
        public function salvarPeriodoAction(){
            $tbGra = new Application_Model_GradePeriodo();
            $tbGrad = new Application_Model_GradeDia();
            $dia = $this->_getParam("dia", FALSE);
            $intervalo = $this->_getParam("intervalo", FALSE);
            $horarios = $this->_getParam("horarios", FALSE);
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $qtde = $this->_getParam("qtde",FALSE);
            // Removendo horários antigo da grade
            if($horarios) {
                $tbGra->excluir($coni_codigo, $dia);
            }
            foreach($horarios as $horario){
                $horario = explode("|", $horario);
                $horario_inicial = $horario[0];
                $horario_final = $horario[1];
                $dados = array("grap_hora_inicial"=>$horario_inicial,
                               "grap_hora_final"=>$horario_final,
                               "grap_dia"=>$dia,
                               "coni_codigo"=>$coni_codigo);
                if($horario[2])
                    $dados["grap_codigo"] = $horario[2];
                $grap_codigo = $tbGra->salvar($dados);
            }
            //gerar grade dia
            $dadosGrade = array("coni_codigo"=>$coni_codigo,
                                "grad_cota_dia"=>$qtde,
                                "grad_intervalo_horario"=>$intervalo,
                                "grad_dia"=>$dia);
            $tbGrad->atualizaIntervalo($dadosGrade);
            $this->salvarHorariosDiasAction($dia,$coni_codigo);
            return $this->render("dados", NULL, TRUE);
        }
        
        public function salvarHorariosDiasAction($dia,$coni_codigo){
            
           
            $tbConvAge = new Application_Model_ConvenioDiasSemanaAgendamento();
            $tbConvHorDia = new Application_Model_ConvenioHorariosDias();
            $tbFun = new Application_Model_Funcoes();
            $tbGrap = new Application_Model_GradePeriodo();

            $atendeQueDia = $tbFun->diaSemana($dia);
            $condiAgeCod = $tbConvAge->getDadosDia($coni_codigo,$atendeQueDia)->condi_age_codigo;
            $horarios = $tbGrap->getHorariosDia($coni_codigo, $dia,$condiAgeCod);
            // Se existir Horários exibe
            if ($horarios) {
                    // Apaga horários antigo do dia e do coni codigo
                    $tbConvHorDia->excluir($condiAgeCod,$coni_codigo);
                    // Calcula quantidade de intervalo dos horários e joga em um array
                    $quantidades = $tbFun->calculaQuantidadePorIntervalo($horarios,$coni_codigo,$dia,$condiAgeCod);
                    // Realiza um arry de distribuição de horários pela quantidade, horarios e etc ..
                    $distribuicao = $tbFun->montaArrayDeHorarios($quantidades,$horarios,$coni_codigo,$dia,$condiAgeCod);
            }
            $tbConvHorDia = new Application_Model_ConvenioHorariosDias();
            foreach($distribuicao as $item) {
                $data = array(
                    "coni_codigo" => $coni_codigo,
                    "condi_age_codigo" => $condiAgeCod,
                    "hora" => $item
                );
                $tbConvHorDia->salvar($data);
            }
        }
        
        public function salvarHorarioAction(){
            $tbGrah = new Application_Model_GradeHorario();
            $dia = $this->_getParam("dia", FALSE);
            $horas = $this->_getParam("hora", FALSE);
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $grah_motivo = $this->_getParam("motivo", FALSE);
            $mof_codigo = $this->_getParam("mof_codigo", FALSE);
            $grah_codigos = array();
            foreach ($horas as $hora){
                $hora_grah = explode("|",$hora);
                $dados = array("coni_codigo"=>$coni_codigo,
                               "grah_dia"=>$dia,
                               "grah_hora"=>$hora_grah[0],
                               "grah_motivo"=>$grah_motivo,
                               "mof_codigo"=>$mof_codigo);
                if($hora_grah[1]){
                    $dados["grah_codigo"] = $hora_grah[1];
                }
                $grah_codigo = $tbGrah->salvar($dados);
                array_push($grah_codigos, $grah_codigo);
            }
            $this->view->dados = $grah_codigos;
            return $this->render("dados", NULL, TRUE);
        }
        
        public function getPeriodosAction(){
            $dia = $this->_getParam("dia", FALSE);
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $grah_codigos = $this->_getParam("grah_codigos", FALSE);
            $tbGrah = new Application_Model_GradeHorario();
            $registros = $tbGrah->getHorarios($dia, $coni_codigo,$grah_codigos)->toArray();
            $this->view->dados = $registros;
            return $this->render("dados", NULL, TRUE);
        }
        
        public function getMotivosAction(){
            $dia = $this->_getParam("dia", FALSE);
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $tbMof = new Application_Model_MotivosFaltas();
            $registros = $tbMof->getMotivos()->toArray();
            $this->view->dados = $registros;
            return $this->render("dados", NULL, TRUE);
            
        }
        
        public function deleteGradeHorarioAction(){
            $grah_horarios = $this->_getParam("grah_codigos", FALSE);
            $grah_codigo_array = explode(",",$grah_horarios);
            $tbGrah = new Application_Model_GradeHorario();
            foreach($grah_codigo_array as $grah_codigo){
                $tbGrah->deleteHorarios($grah_codigo);
            }
            return $this->render("dados", NULL, TRUE);
        }
        
        public function getGradePeriodoAction(){
            $dia = $this->_getParam("dia", FALSE);
            $coni_codigo = $this->_getParam("coni_codigo", FALSE);
            $tbGrad = new Application_Model_GradeDia();
            $tbGrap = new Application_Model_GradePeriodo();
            $periodo_intervalo = array();
            $intervalo = $tbGrad->getGradeDia($coni_codigo, $dia)->grad_intervalo_horario;
            $periodo_intervalo = $tbGrap->getPeriodosGrade($coni_codigo, $dia)->toArray();
            $periodo_intervalo["intervalo"] = $intervalo;
            $this->view->dados = $periodo_intervalo;
            return $this->render("dados", NULL, TRUE);
        }
        
        public function deletarHorarioAction(){
            $horas = $this->_getParam("hora", FALSE);
            $tbGrah = new Application_Model_GradeHorario();
            foreach($horas as $hora){
                $tbGrah->deleteHorarios($hora);
            }
            return $this->render("dados", NULL, TRUE);
        }
        
        public function getAgendadosAction(){
            $coni_codigo = $this->_request->getPost("coni_codigo");
            $dia = $this->_request->getPost("dia");
            $codsAge = $this->_request->getPost("codsAge");
            $tbAge = new Application_Model_Agendamento();
            $this->view->dados = $tbAge->getAgendamentos($coni_codigo, $dia, $codsAge)->toArray();
            return $this->render("dados",null,true);
        }
        
        /*public function getAgendadosAction(){
            $coni_codigo = $this->_getParam("coni_codigo",false);
            $dia = $this->_getParam("dia",false);
            $tbAge = new Application_Model_Agendamento();
            $this->view->dados = $tbAge->getAgendamentos($coni_codigo, $dia)->toArray();
            return $this->render("dados",null,true);
        }*/
        
        public function transferenciaAction(){
            $agendamentos = $this->_getParam("agendamentos",FALSE);
            $tbAge = new Application_Model_Agendamento();
            foreach($agendamentos as $dados){
                foreach($dados as $dado){
                    // Validação de Horários
                    if ($dado[0][hora] != "0" && $dado[0][hora] != "" && $dado[0][hora] != "undefined") {
                        $dados_salvar = array("age_codigo"=>$dado[0][age_codigo],
                                       "esp_codigo"=>$dado[0][esp_codigo],
                                       "coni_codigo"=>$dado[0][coni_codigo],
                                       "med_codigo"=>$dado[0][med_codigo],
                                       "age_data"=>$dado[0][dia],
                                       "age_horario"=>$dado[0][hora]);
                        $tbAge->salvar($dados_salvar);
                    }
                }
            }
            return $this->render("dados",null,true);
         }
}

