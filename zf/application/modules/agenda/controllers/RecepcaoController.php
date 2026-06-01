<?php

class Agenda_RecepcaoController extends Zend_Controller_Action {

	public function init() {
           // $this->view->title = "Recepção de pacientes";
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/agenda/distribuicao.js');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.sortable.js');
        }

	public function indexAction() {
		$this->view->title = "Recepção de pacientes";
        	//$tbConv = new Application_Model_ConvenioItens();
		//$this->view->convenio = $tbConv->selectTag();
		//$this->view->itens = $tbConv->getNomeProfissionaisPorUnidade(570019);
	}

	public function buscarProfissionaisAction() {
		$this->_helper->layout->disableLayout();
		$uni_codigo = $this->_getParam("uni_codigo", false);
		$tbConi = new Application_Model_ConvenioItens();
                $this->view->dados = $tbConi->getNomeProfissionaisPorUnidadeConveniado($uni_codigo)->toArray();
                $this->render("dados", NULL, TRUE);
	}

	public function carregaEspecialidadeAction() {
            $this->_helper->layout->disableLayout();
            $uni_codigo = $this->_getParam("uni_codigo", false);
            $usr_codigo = $this->_getParam("usr_codigo", false);
            $tbConi = new Application_Model_ConvenioItens();
            $this->view->dados = $tbConi->getEspecialidadeMedicoPorConvenio($uni_codigo, $usr_codigo)->toArray();
            $this->render("dados", NULL, TRUE);
	}

	public function carregaPacientesAgendadosAction() {
            
                $this->_helper->layout->disableLayout();
		$uni_codigo = $this->_getParam("uni_codigo", false);
		$usr_codigo = $this->_getParam("usr_codigo", false);
		$esp_codigo = $this->_getParam("esp_codigo", false);
		$age_data = $this->_getParam("age_data", false);
                $data_inicial = $this->_getParam("data_inicial", false);
                $conv_codigo = $this->_getParam("conv_codigo", false);
                $coni_codigo = $this->_getParam("coni_codigo", false);
		$tbAge = new Application_Model_Agendamento();
                //$this->view->dados = $tbAge->getPacientesAgendados($uni_codigo, $usr_codigo, $esp_codigo, $age_data)->toarray();
                $dadosPac = $tbAge->getPacientesAgendados($uni_codigo, $usr_codigo, $esp_codigo, $age_data)->toarray();
                $i = 0;
                // Lendo dados do paciente
                foreach ($dadosPac as $ind=>$pac) {
                    if($this->confereHorarioAction($pac["age_horario"],$coni_codigo,$conv_codigo,$data_inicial)=="false") {
                        $dadosPac[$i]["status"] = "R";
                        $dadosPac[$i]["age_atendido"] = "Transferir Agendamento";
                        $dadosPac[$i]["cor"] = "#FF3300";
                    }
                    $i++;    
                }
                $this->view->dados = $dadosPac;
                $this->render("dados", NULL, TRUE);
	}
        
        public function confereHorarioAction($horario,$coni_codigo,$conv_codigo,$data_inicial){
            $this->_helper->layout->disableLayout();
            // Passa por get código do convênio, código do convênio item e data inicial
            $horarioGet = explode(":",$horario);
            $horario = $horarioGet[0].":".$horarioGet[1];
            $tbConvAge = new Application_Model_ConvenioDiasSemanaAgendamento();
            $tbFun = new Application_Model_Funcoes();
            $tbGrap = new Application_Model_GradePeriodo();
            $atendeQueDia = $tbFun->diaSemana($data_inicial);
            $condiAgeCod = $tbConvAge->getDadosDia($coni_codigo,$atendeQueDia)->condi_age_codigo;
            $horarios = $tbGrap->getHorariosDia($coni_codigo, $data_inicial, $condiAgeCod);
            // Calcula quantidade de intervalo dos horários e joga em um array
            $quantidades = $tbFun->calculaQuantidadePorIntervalo($horarios,$coni_codigo,$data_inicial,$condiAgeCod);
            // Realiza um arry de distribuição de horários pela quantidade, horarios e etc ..
            $dadosHorario = $tbFun->montaArrayDeHorarios($quantidades,$horarios,$coni_codigo,$data_inicial,$condiAgeCod);
            if(in_array($horario, $dadosHorario)) {
                return "true";
            } else {
                return "false";
            }
        }
	
	public function imprimePacientesAgendadosAction() {
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $tbAge = new Application_Model_Agendamento();
            $agendamentos = $this->_getParam("agendamentos", false);
            $usr_codigo = $this->_getParam("usr", false);
            $age_data = $this->_getParam("age", false);
            $usr_nome = $tbUsr->getInfoUsr($usr_codigo);
            $uni_codigo = $this->_getParam("uni", false);
            $esp_codigo = $this->_getParam("esp", false);
            /* ---------------------------------------------------------------
             *  Essa configuração foi criada, porque Paraiso do Norte, usa o
             *  relatório do else, portanto não alterar
             * -------------------------------------------------------------*/
            if($tbConf->getConfig("IMPRIMIR_PACIENTES_TELEFONE")==1) {
                $this->_redirect("agenda/recepcao/imprime-dados-pacientes-agendado-sem-diagnostico/agendamentos/$agendamentos/uni/$uni_codigo/usr/$usr_codigo/esp/$esp_codigo/age/$age_data");
            } else {
                $this->view->params = serialize(array("titulo"=>"Atendente","dados"=>"$usr_nome->usr_nome  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Data:</strong> $age_data"));
                $pac = 1;
                $this->view->dados_pac = $tbAge->imprimePacientesAgendados($agendamentos,$uni_codigo, $usr_codigo, $esp_codigo, $age_data)->toArray();
            }
	}
        
        public function imprimeDadosPacientesAgendadoSemDiagnosticoAction(){
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
            $tbUsr = new Application_Model_Usuarios();
            $tbAge = new Application_Model_Agendamento();
            $tbConf = new Application_Model_Configuracao();
            $agendamentos = $this->_getParam("agendamentos", false);
            $usr_codigo = $this->_getParam("usr", false);
            $age_data = $this->_getParam("age", false);
            $uni_codigo = $this->_getParam("uni", false);
            $esp_codigo = $this->_getParam("esp", false);
            $usr_nome = $tbUsr->getInfoUsr($usr_codigo);
            // Títulos do relatório
            $this->view->params = serialize(array("titulo"=>"Atendente","dados"=>"$usr_nome->usr_nome  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Data:</strong> $age_data"));
            $pac = 1;
            $this->view->dados_pac = $tbAge->imprimePacientesAgendados($agendamentos,$uni_codigo, $usr_codigo, $esp_codigo, $age_data)->toArray();
        }

	public function cancelarOuFaltaAgendamentoAction() {
		$this->_helper->layout->disableLayout();
		$tbAge = new Application_Model_Agendamento();
		$age_codigos = $this->_getParam("age_codigos", false);
		$motivo = $this->_getParam("motivo", false);
		//die($age_codigos . "-".$motivo);
		if ($motivo != 'C') {
			foreach ($age_codigos as $age_codigo) {
				$dados = array("age_codigo" => $age_codigo,
					"age_atendido" => $motivo);
				$tbAge->salvar($dados);
			}
		} else {
			foreach ($age_codigos as $age_codigo) {
				// echo "age:".$age_codigo;
				$tbAge->excluir($age_codigo);
			}
		}
		//  die('apara');
		// $this->view->dados = $tbAge->getPacientesAgendados($uni_codigo, $usr_codigo, $esp_codigo, $age_data)->toarray();
		$this->view->dados = $motivo;
		$this->render("dados", NULL, TRUE);
	}

	public function alteraSituacaoAction() {
		$this->_helper->layout->disableLayout();
		$age_codigo = $this->_getParam("age_codigo", false);
		$tbAge = new Application_Model_Agendamento();
		$situacao = $tbAge->getAgendamento($age_codigo)->age_atendido;
		if ($situacao == "S") {
			$altera = "N";
		} else {
			$altera = "S";
		}
		$tbAge->alteraSituacao($altera, $age_codigo);
		$this->view->dados = $altera;

		$this->render("dados", NULL, TRUE);
	}
}

