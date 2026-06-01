<?php

class AgendamentoAnteriorController extends Zend_Controller_Action {

	public function init() {
        $this->view->title = "Agendamento Anterior";
		$this->_helper->acl->allow(NULL);
	}

	public function indexAction() {
		$this->render("index");
		// action body
	}

	public function recuperaAgendamentoPorPeriodoAction (){

		$objetoAgendaMigrate = new Application_Model_AgendamentoAnterior();

		$recebeDataInicial = $this->_request->getPost("recebeDataInicial");
		$recebeDataFinal = $this->_request->getPost("recebeDataFinal");
		$recebeIdDoUsuario = $this->_request->getPost("recebeIdDoUsuario");

		$recebeResultadoDaBusca = $objetoAgendaMigrate->recuperaAgendamentoPorPeriodo(
			$recebeDataInicial, $recebeDataFinal, $recebeIdDoUsuario
		);

		for ($i = 0 ; $i < count ($recebeResultadoDaBusca) ; $i++ ) {
			$datas = explode('-', explode(' ', $recebeResultadoDaBusca[$i][data] ) [0] );
			$data = $datas[2].'/'.$datas[1].'/'.$datas[0];
			$recebeResultadoDaBusca[$i][data] = $data;
			// echo "<pre>";print_r($recebeResultadoDaBusca);die();
		}

		// print_r($recebeResultadoDaBusca);die();
		// return false;
		echo json_encode($recebeResultadoDaBusca);
		exit();
	}

	public function recuperaAgendamentoPorPeriodoPacienteAction (){

		$objetoAgendaMigrate = new Application_Model_AgendamentoAnterior();

		$recebeDataInicial = $this->_request->getPost("recebeDataInicial");
		$recebeDataFinal = $this->_request->getPost("recebeDataFinal");
		$recebeIdDoUsuario = $this->_request->getPost("recebeIdDoUsuario");

		$recebeResultadoDaBusca = $objetoAgendaMigrate->recuperaAgendamentoPorPeriodoPaciente(
			$recebeDataInicial, $recebeDataFinal, $recebeIdDoUsuario
		);

		for ($i = 0 ; $i < count ($recebeResultadoDaBusca) ; $i++ ) {
			$datas = explode('-', explode(' ', $recebeResultadoDaBusca[$i][data] ) [0] );
			$data = $datas[2].'/'.$datas[1].'/'.$datas[0];
			$recebeResultadoDaBusca[$i][data] = $data;
			// echo "<pre>";print_r($recebeResultadoDaBusca);die();
		}

		// print_r($recebeResultadoDaBusca);die();
		// return false;
		echo json_encode($recebeResultadoDaBusca);
		exit();
	}

}

