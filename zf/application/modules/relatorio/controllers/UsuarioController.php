<?php

class Relatorio_UsuarioController extends Elotech_Controller_Action_Relatorio {

	private $tbUsu;

	public function init() {
		$this->view->title = "Pacientes";

		$this->tbUsu = new Application_Model_Usuario();
	}

	public function indexAction() {

	}

	public function prontuarioAction(){
		$this->view->title .= " - Prontuário";
		$this->tbUsu = new Application_Model_Usuario();
		//die("teste");
		$usu_codigo = $this->_request->getPost("usu_codigo", FALSE);
		if(!$usu_codigo)
			$usu_codigo = $this->_getParam ("usu_codigo",FALSE);

		if (!$usu_codigo) {
			$this->view->action = array("action" => "prontuario");
			return $this->render("prontuario", NULL, TRUE); // mostra action para pedir os dados
		}

		Zend_Layout::getMvcInstance()->setLayout("simples");

		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);
		$esp = $this->_request->getPost("especia",FALSE);
		//die($esp);
		$options = $this->_request->getPost("op", FALSE);
		$recebe = $this->tbUsu->relProntuario($usu_codigo,$data_inicial,$data_final,$options,$limit,$esp);
		//echo "<pre>";print_r($recebe);die();
		$this->view->dados = $recebe;
                //die($data_inicial);
		
                $this->view->data_inicial = $data_inicial;
                $this->view->data_final = $data_final;
                $tbUsr = new Application_Model_Usuarios();
                $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;
		$this->view->fantante = $this->tbUsu->getFaltas($usu_codigo, $data_inicial, $data_final);

		if(!$this->view->dados->usu){
			// Paciente não localizado, redirecionar
		}



	}

        public function guiaDiagnosticoAction(){
                $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio/usuario/prontuario.css','all');
		$this->view->title .= " - Guia de Diagnostico";
                $this->view->age_codigo = $this->_getParam ("age_codigo",FALSE);
                $tbUsr = new Application_Model_Usuarios();
                $this->view->med_nome = $tbUsr->getInfoUsr($this->_getParam ("med_codigo",FALSE))->usr_nome;

		$usu_codigo = $this->_request->getPost("usu_codigo", FALSE);
		if(!$usu_codigo)
			$usu_codigo = $this->_getParam ("usu_codigo",FALSE);

		if (!$usu_codigo) {
			$this->view->action = array("action" => "prontuario");
			return $this->render("prontuario", NULL, TRUE); // mostra action para pedir os dados
		}

		Zend_Layout::getMvcInstance()->setLayout("simples");

		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);
		//die("teste");


		//$options = $this->_request->getPost("op", FALSE);
                $options = array(0=>"atendimentos",1=>"pre-consulta",2=>"medicamentos"); //caso quiser colocar mais historicos add no array e descomente o codigo da view

                $tbConf = new Application_Model_Configuracao();
                if ($tbConf->getConfig("LIMIT_GUIA_DIAGNOSTICO") != NULL) {
                    $limit = $tbConf->getConfig("LIMIT_GUIA_DIAGNOSTICO");
                }else{
                    $limit = null;
                }

		$this->view->dados = $this->tbUsu->relProntuario($usu_codigo,$data_inicial,$data_final,$options,$limit);
                //die($data_inicial);
                $this->view->data_inicial = $data_inicial;
                $this->view->data_final = $data_final;
                $tbUsr = new Application_Model_Usuarios();
                $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;

		if(!$this->view->dados->usu){
			// Paciente não localizado, redirecionar
		}



	}



 public function guiaDiagnosticoSemHistoricoAction(){
    $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio/usuario/prontuario.css','all');
	$this->view->title .= " - Guia de Diagnostico";
    $this->view->age_codigo = $this->_getParam ("age_codigo",FALSE);
    $tbUsr = new Application_Model_Usuarios();
    $this->view->med_nome = $tbUsr->getInfoUsr($this->_getParam ("med_codigo",FALSE))->usr_nome;
    
    $recebeCrm = $tbUsr->recuperaCrm($this->_getParam("med_codigo",FALSE));

    $this->view->recebeCrm = $recebeCrm;


	$usu_codigo = $this->_request->getPost("usu_codigo", FALSE);
	if(!$usu_codigo)
		$usu_codigo = $this->_getParam ("usu_codigo",FALSE);

	if (!$usu_codigo) {
		$this->view->action = array("action" => "prontuario");
		return $this->render("prontuario", NULL, TRUE); // mostra action para pedir os dados
	}

	Zend_Layout::getMvcInstance()->setLayout("simples");

	$data_inicial = $this->_request->getPost("data_inicial", FALSE);
	$data_final = $this->_request->getPost("data_final", FALSE);

	//$options = $this->_request->getPost("op", FALSE);
            $options = array(0=>"atendimentos",1=>"pre-consulta",2=>"medicamentos"); //caso quiser colocar mais historicos add no array e descomente o codigo da view

            $tbConf = new Application_Model_Configuracao();
            if ($tbConf->getConfig("LIMIT_GUIA_DIAGNOSTICO") != NULL) {
                $limit = $tbConf->getConfig("LIMIT_GUIA_DIAGNOSTICO");
            }else{
                $limit = null;
            }

	$this->view->dados = $this->tbUsu->relProntuario($usu_codigo,$data_inicial,$data_final,$options,$limit);
            //die($data_inicial);
            $this->view->data_inicial = $data_inicial;
            $this->view->data_final = $data_final;
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;

		
	}


	public function relatorioDuplicadosAction(){
		Zend_Layout::getMvcInstance()->setLayout("modelo-print");
		$tbUsuario = new Application_Model_Usuario();
		$this->view->duplicados = $tbUsuario->getDuplicados();
	}


	public function estratificacaoRiscoAction(){
		$tpEstraUsu = new Application_Model_EstratificacaoUsu();
		$usu_codigo =$this->_getParam("usu_codigo", FALSE);
		
		$recebeScoreUsuario = $tpEstraUsu->recuperaScoreUsuario($usu_codigo);

		

		$this->view->score = $recebeScoreUsuario;
	}

}

