<?php

class Prontuario_PreConsultaController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Pré-Consulta";
	}

	public function indexAction() {
//		if ($this->isMedicoSemAtendimento())
//			$this->_redirect("/prontuario/pre-consulta/ultima");
		$gambi = $this->_getParam("gambi", false);
		// die($gambi);

		$this->_redirect("/prontuario/pre-consulta/ultima/gambi/$gambi");
		$tbPC = new Application_Model_PreConsulta();
		$this->view->historico = $tbPC->getHistorico();
	}

	public function ultimaAction() {

            $gambi = $this->_getParam("gambi", false);
            $this->view->gambi = $gambi;
            $tbPC = new Application_Model_PreConsulta();
            $tbLocal = new Application_Model_TbLocalAtend();
            $this->view->selectLocais = $tbLocal->selectTag();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->uni_tipo = $tbUsr->getUsrAtual()->uni_tipo;

            $ultima = $tbPC->getUltima();
            if (!$ultima) {
                    return $this->render("index");
            }
            $this->_redirect("/prontuario/pre-consulta/ver/id/" . $tbPC->getUltima()->pc_codigo . "/gambi/$gambi");
	}

	public function verAction() {
		$gambi = $this->_getParam("gambi", false);
		$this->view->gambi = $gambi;
		$noGambi = $this->_getParam("noGambi", false);

		// bloqueia botões do form se form médico e não tiver feito atendimento
		$this->view->bloquear = $this->isMedicoSemAtendimento();
               // echo "<pre>".print_r($_SESSION,1);die();
		$id = $this->_getParam("id", FALSE);
		if (!$id)
                    return $this->_redirect("/prontuario/pre-consulta");

		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->semLayout = $this->_getParam("sem-layout", FALSE);

		if ($this->view->semLayout)
			$this->_helper->layout->disableLayout();

		$tbPC = new Application_Model_PreConsulta();
		$pc = $tbPC->find($id);

                $tbLocal = new Application_Model_TbLocalAtend();
                $tbUsr = new Application_Model_Usuarios();
                $this->view->selectLocais = $tbLocal->selectTag($pc->co_local_atend);
                $this->view->uni_tipo = $tbUsr->getUsrAtual()->uni_tipo;
		if (!$pc) {
			return $this->_redirect("/prontuario/pre-consulta");
		}
		$pc = $pc->current();

		// Essa PC faz parte desse Agendamento?
		$age = Application_Model_Agendamento::usuEmAberto();

		$this->view->historico =$tbPC->find($id);
                //die($id. "!=". $pc->age_codigo);
                if($id != $pc->age_codigo && empty($id))
                    $this->view->vizualizar = 1;


		$this->view->historico =$tbPC->find($id);

		$tbAte = new Application_Model_Atendimento();
		//$ate_origem = $tbAte->buscaRetornoOrigem();
		$tbUsr = new Application_Model_Usuarios();
		if ($ate_origem->age_atendido == "E" && $tbUsr->isMedico()) { //if aplicado apenas pra retorno por isso nao editava pre
			$this->view->dados = $pc;
		} else if ($ate_origem->ate_encaminhamento == "S") {
			$tbRet = new Application_Model_Retorno();
			$pc = $tbRet->getDadosPre($pc->age_codigo, $pc->pc_codigo, $gambi, $noGambi);
			$this->view->dados = $pc;
		} else {
			$this->view->dados = $pc;
		}

		$this->render("index");
	}


        public function editarAction() {

		$id = $this->_getParam("id", FALSE);
		if (!$id)
			return $this->_redirect("/prontuario/pre-consulta");

		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->semLayout = $this->_getParam("sem-layout", FALSE);

		if ($this->view->semLayout)
			$this->_helper->layout->disableLayout();

		$tbPC = new Application_Model_PreConsulta();
		$pc = $tbPC->find($id);

                $tbLocal = new Application_Model_TbLocalAtend();
                $this->view->selectLocais = $tbLocal->selectTag($pc->co_local_atend);

		if (!$pc) {
			return $this->_redirect("/prontuario/pre-consulta");
		}
		$pc = $pc->current();

		$age = Application_Model_Agendamento::usuEmAberto();

		$this->view->historico =$tbPC->find($id);

		$tbAte = new Application_Model_Atendimento();
		//$ate_origem = $tbAte->buscaRetornoOrigem();
		$tbUsr = new Application_Model_Usuarios();
		if ($ate_origem->age_atendido == "E" && $tbUsr->isMedico()) { //if aplicado apenas pra retorno por isso nao editava pre
			$this->view->dados = $pc;
		} else if ($ate_origem->ate_encaminhamento == "S") {
			$tbRet = new Application_Model_Retorno();
			$pc = $tbRet->getDadosPre($pc->age_codigo, $pc->pc_codigo, $gambi, $noGambi);
			$this->view->dados = $pc;
		} else {
			$this->view->dados = $pc;
		}

		$this->render("index");
	}

	public function salvarAction() {
		if ($this->_request->isPost()) {
			// die($this->_request->getPost("pc_clas_risco")."as");
			$json = $this->_request->getPost("json", FALSE);
                        $tbUsr = new Application_Model_Usuarios();
			// Resgatando os dados do banco
			$dados = array(
                            "co_local_atend" => $this->_request->getPost("co_local_atend", FALSE),
                            "pc_codigo" => $this->_request->getPost("pc_codigo", FALSE),
                            "pc_temperatura" => $this->_request->getPost("temperatura", NULL),
                            "pc_peso" => $this->_request->getPost("peso", NULL),
                            "pc_altura" => $this->_request->getPost("altura", NULL),
                            "pc_pressao_sistolica" => $this->_request->getPost("pressao_sistolica", NULL),
                            "pc_pressao_diastolica" => $this->_request->getPost("pressao_diastolica", NULL),
                            "pc_freq_cardiaca" => $this->_request->getPost("freq_cardiaca", NULL),
                            "pc_freq_respiratoria" => $this->_request->getPost("freq_respiratoria", NULL),
                            "pc_perimetro_cefalico" => $this->_request->getPost("p_cefalico", NULL),
                            "pc_glicose" => $this->_request->getPost("glicose", NULL),
                            "pc_dados" => $this->_request->getPost("obs", NULL),
                			"turno" => ($this->_request->getPost("turno", false)),
                            "pc_clas_risco" => $this->_request->getPost("pc_clas_risco", NULL),
                            "pc_saturacao" => $this->_request->getPost("pc_saturacao", NULL),
                            "age_codigo" => ($this->_request->getPost("age_codigo") != NULL ? $this->_request->getPost('age_codigo', NULL) : ""),
                            "usr_codigo" => ($this->_request->getPost("usr_codigo") != NULL ? $this->_request->getPost('usr_codigo', NULL) : $tbUsr->getUsrAtual()->usr_codigo),
                            "pc_data" => ($this->_request->getPost("pc_data") != NULL ? $this->_request->getPost('pc_data', NULL) : "NOW()"),
							"pc_alt_uterina" => ($this->_request->getPost("pc_alt_uterina") != NULL ? $this->_request->getPost('pc_alt_uterina') : 0),
							"pc_idade_gest" => ($this->_request->getPost("pc_idade_gest") != NULL ? $this->_request->getPost('pc_idade_gest') : 0),
							"pc_bat_fetal" => ($this->_request->getPost("pc_bat_fetal") != NULL ? $this->_request->getPost('pc_bat_fetal') : 0),
							"pc_perim_torac" => ($this->_request->getPost("pc_perim_torac") != NULL ? $this->_request->getPost('pc_perim_torac') : 0)
			);
			try {
				$tbPC = new Application_Model_PreConsulta();
				$id = $tbPC->salvar($dados,$json);

				$tbAte = new Application_Model_Atendimento();
				$ate_origem = $tbAte->buscaRetornoOrigem();
				$data_retorno = array("ate_codigo_origem" => $ate_origem->ate_codigo,
					"pc_codigo" => $id);

				$tbRet = new Application_Model_Retorno();
				//AQUI QUE PAROU /
				///FALTA PEGAR O AGE_CODIGO E ALTERAR O STATUS PARA I DO AGENDAMENTO COM O METODO ALTERA SITUAÇÃO
				// O GRANDE PROBLEMA EH A SESSAO QUE QUANDO ABRE O METODO ELE DESTROI A MESMA
				$tbRet->salvar($data_retorno);

				$tbAge = new Application_Model_Agendamento();
				$age_codigo = $tbRet->getDadosPre(null, $id);
				//echo "<pre>".print_r($age_codigo->age_codigo,1);exit;
				// die($age_codigo->age_codigo);
				$tbAge->alteraSituacao("I", $age_codigo->age_codigo, FALSE);
				if ($json){
                                        return $this->json($id);
                                }else{
					return $this->_redirect("/prontuario/pre-consulta/editar/id/$id");
                                }
			} catch (Zend_Validate_Exception $exc) {

				if ($json) {
					$this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
					$this->render("dados", NULL, TRUE);
				} else {
					$this->view->erro = $exc->getMessage();
					$this->view->dados = (object) $dados;
					$this->render("index");
				}
			}
		} else {
			$this->_redirect("/prontuario/pre-consulta");
		}
	}

	public function historicoAction() {
		$tbPC = new Application_Model_PreConsulta();
		$this->view->historicoPreConsulta = $tbPC->getHistorico()->toArray();
		$this->view->ultimaPreConsulta = array_pop($this->view->historicoPreConsulta); // colocar imagens na index
	}

	private function json($id) {
		$tbPC = new Application_Model_PreConsulta();
		$pc = $tbPC->getPC($id);
                if (!$pc)
                    return $this->_redirect("/prontuario/pre-consulta");

		$this->view->dados = $pc->toArray();
		$this->render("dados", NULL, TRUE);
	}

	private function isMedicoSemAtendimento() {
		$tbUsr = new Application_Model_Usuarios();
		if ($tbUsr->isMedico()) {
			// verifica se há um atendimento feito pelo médico
			$tbAte = new Application_Model_Atendimento();
			if ($tbAte->temAtendimentoMedico()){
                            return FALSE;
                        }else{

                            return TRUE;
                        }
		}
		return FALSE;
	}

        public function excluirAction(){
                $id = (int) $this->_getParam("id", 0);

		if (!$id)
			return $this->_redirect("/prontuario/pre-consulta");

		$tbPc = new Application_Model_PreConsulta();
		$tbPc->excluir($id);

		if ($this->_getParam("json", FALSE)) {
			$this->view->dados = array("success" => TRUE);
			return $this->render("dados", NULL, TRUE);
		}

		return $this->_redirect("/prontuario/pre-consulta");
        }

        public function historicoDePreConsultaAction() {
            $tbAte = new Application_Model_PreConsulta();
            $this->view->term = $this->_getParam("term", FALSE);
            $usu_codigo = $this->_getParam("cod", FALSE);
            $this->view->itens = $tbAte->getHistorico($usu_codigo,NULL,NULL,NULL,NULL,$this->view->term);
        }




	public function salvarDoPrenatalAction() {
		$tbUsr = new Application_Model_Usuarios();
		$reavaliacao = $this->_request->getPost("reavaliacao", FALSE);
		$dados = array(
			"co_local_atend" => $this->_request->getPost("co_local_atend", FALSE),
			"pc_temperatura" => $this->_request->getPost("temperatura", NULL),
			"pc_peso" => $this->_request->getPost("peso", NULL),
			"pc_altura" => $this->_request->getPost("altura", NULL),
			"pc_pressao_sistolica" => $this->_request->getPost("pressao_sistolica", NULL),
			"pc_pressao_diastolica" => $this->_request->getPost("pressao_diastolica", NULL),
			"pc_freq_cardiaca" => $this->_request->getPost("freq_cardiaca", NULL),
			"pc_freq_respiratoria" => $this->_request->getPost("freq_respiratoria", NULL),
			"pc_perimetro_cefalico" => $this->_request->getPost("p_cefalico", NULL),
			"pc_glicose" => $this->_request->getPost("glicose", NULL),
			"pc_momento_coleta" => $this->_request->getPost("pc_momento_coleta", NULL),
			"pc_dados" => $this->_request->getPost("obs", NULL),
			"pc_clas_risco" => $this->_request->getPost("pc_clas_risco", NULL),
			"pc_saturacao" => $this->_request->getPost("pc_saturacao", NULL),
			"age_codigo" => ($this->_request->getPost("age_codigo") != NULL ? $this->_request->getPost('age_codigo', NULL) : ""),
			"usr_codigo" => ($this->_request->getPost("usr_codigo") != NULL ? $this->_request->getPost('usr_codigo', NULL) : $tbUsr->getUsrAtual()->usr_codigo),
			"esp_codigo" => ($this->_request->getPost("esp_codigo") != NULL ? $this->_request->getPost('esp_codigo', NULL) : $tbUsr->getUsrAtual()->esp_codigo),
			"pc_data" => ($this->_request->getPost("pc_data") != NULL ? $this->_request->getPost('pc_data', NULL) : "NOW()")
		);
		$pc_codigo = $this->_request->getPost("pc_codigo", FALSE);
		if($pc_codigo && $reavaliacao == 1){
			$dados['pc_codigo'] = $pc_codigo;
		}
		try {
			$tbPC = new Application_Model_PreConsulta();
			$id = $tbPC->salvar($dados);
			$this->view->dados = array("success" => TRUE, "pc_codigo" => $id);

		}catch (Zend_Validate_Exception $exc) {
			$this->view->dados = array("success" => FALSE, "mensagem" => $exc->getMessage());
		}
		return $this->render("dados", NULL, TRUE);
	}

}
