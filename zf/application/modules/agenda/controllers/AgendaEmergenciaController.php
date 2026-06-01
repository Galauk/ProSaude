<?php

class Agenda_AgendaEmergenciaController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Fazer Agendamento";
	}

	public function indexAction() {
		// action body
	}

	public function selecionarDataAction(){
		$this->_helper->layout->disableLayout();

		$coni_codigos = $this->_getParam("procs", FALSE);
		$this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));


		if(!$coni_codigos)
			return $this->_helper->viewRenderer->setNoRender(true);

		$coni_codigos = explode(",", $coni_codigos);

		$tbAge = new Application_Model_Agenda();
		$tbConI = new Application_Model_ConvenioItens();
                $this->view->data_final = $tbAge->calculaDataFinal($this->view->data_inicial);

		$this->view->vagas = $tbAge->getVagas($coni_codigos, $this->view->data_inicial, $this->view->data_final);

		$this->view->nomeProcs = $tbConI->getNomeProcedimentos($coni_codigos);
	}

        /**
	 * Salvar
	 * Acessar por post/ajax
	 * @return json
	 */
	public function salvarAction(){

	 	if ($this->_request->isPost()) {
			$dados = array(
				"usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
				"usr_codigo_medico" => $this->_request->getPost("usr_codigo_medico", FALSE),
				"ate_codigo" => $this->_request->getPost("ate_codigo", FALSE),
				"interno" => $this->_request->getPost("interno", FALSE),
				"itens" => $this->_request->getPost("coni", array())
			);


			try {
				$tbAge = new Application_Model_Agenda();
				$age_codigo = $tbAge->salvar($dados);
				$this->view->dados = array("success"=>TRUE,"age_codigo"=>$age_codigo);

			} catch (Zend_Validate_Exception $exc) { // Exceção de validação
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());

			} catch (Zend_Exception $exc) { // Exceção de login
				$this->view->dados = array("success"=>FALSE, "titulo"=>"Faça login", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
			}

			return $this->render("dados", NULL, TRUE);
		} else {
			$this->_redirect("/agenda/agenda-emergencia");
		}
	}

	public function imprimirAction(){
		$age_codigo = $this->_getParam("age", FALSE);
                $coletados = $this->_getParam("coletados", FALSE);
		$this->_helper->layout->setLayout("modelo-print");

		$tbAge = new Application_Model_Agenda();
                $tbUsr = new Application_Model_Usuarios();
		$age = $tbAge->getAgendamento($age_codigo,$coletados);

                $this->view->emissor = $tbUsr->getUsrAtual()->usr_nome;
		$this->view->codigo = $age_codigo;
		$this->view->usu_codigo = $age->current()->usu_codigo;
		$this->view->age_codigo = $age->current()->age_codigo;
		$this->view->usu_nome = $age->current()->usu_nome;
		$this->view->age = $age;
                $this->view->medico = ($age->current()->usr_nome ? $age->current()->usr_nome: $age->current()->medico_e);
		$this->view->orientacoes = $tbAge->getOrientacoes($age_codigo);
                $this->view->coletados =$coletados;
	}

	/**
	 * Histórico de agendamento de exames por paciente
	 */
	public function historicoAction(){
		$this->_helper->layout->disableLayout();

		$usu_codigo = $this->_getParam("usu", FALSE);
		if(!$usu_codigo)
			return $this->_redirect ("/agenda/agenda-emergencia");

		$tbAge = new Application_Model_Agenda();
		$this->view->itens = $tbAge->getHistoricoDeExames($usu_codigo);
	}

	public function excluirAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$agei_codigo = $this->_request->getPost("agei_codigo", FALSE);
		if(!$agei_codigo)
			return $this->_redirect ("/agenda/agenda-emergencia");

		$tbAgei = new Application_Model_AgendaItens();
		$tbAgei->excluir($agei_codigo);
	}

	public function coletaAction(){

		$array_cache = json_decode(stripslashes($_POST['array_cache']));
		$data = $_POST['data'];
		$age_codigo = $_POST['age_codigo'];
		$datasql = $_POST['datasql'];

		$key = array_keys($array_cache, $data);
		$key_size = sizeof($key);

		$cAgei = new Application_Model_AgendaItens();
		//$cAge = new Application_Model_Agenda();
		$cIns = new Application_Model_Coleta();

		for($i=0;$i<$key_size;$i++){
				$agei_codigo = $cAgei->getAgeiColeta($key[$i], $age_codigo);
				$inserir = $cIns->insertColeta($agei_codigo->agei_codigo, $datasql);
			}

		for($i=0;$i<$key_size;$i++){
				$agei_codigo = $cAgei->getAgeiColeta($key[$i], $age_codigo);
				$status = $cAgei->updateStatusColeta($key[$i], $agei_codigo->agei_codigo);
			}
	}

	public function redirecionarAction(){
			// $usu_codigo = $_POST['usu_codigo'];
		  // $age_codigo = $_POST['age_codigo'];

			$usu_codigo = $this->_getParam("usu", FALSE);
			$age_codigo = $this->_getParam("age", FALSE);
			//die($age_codigo);
			// var_dump($_SESSION[linkroot]);
			// var_dump($_SESSION[comum]);
			return $this->_redirect($_SESSION[linkroot]."WebSocialSaude/exa_digitacaoresultado2.php?age_codigo=". $age_codigo ."&id_login=649&usu_codigo=". $usu_codigo);
			//return ("".$_SESSION[linkroot]."WebSocialSaude/exa_digitacaoresultado2.php?age_codigo=". $age_codigo ."&id_login=649&usu_codigo=". $usu_codigo."");
			//echo $age_codigo;
			//header("Location: ".$_SESSION[linkroot]."WebSocialSaude/exa_digitacaoresultado2.php?age_codigo=". $age_codigo ."&id_login=649&usu_codigo=". $usu_codigo ."");

			// echo '<script type="text/javascript">
      //      window.location.href = "".$_SESSION[linkroot]."WebSocialSaude/exa_digitacaoresultado2.php?age_codigo=". $age_codigo ."&id_login=649&usu_codigo=". $usu_codigo."";
      // </script>';
	}
}
