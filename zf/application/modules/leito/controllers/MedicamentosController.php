<?php

class Leito_MedicamentosController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Dispensação de Medicamentos";
	}

	/**
	 * Lista as grades que precisam ser dispensadas.
	 * Quais são os proximos leitos a receber medicamentos.
	 */
	public function indexAction() {
		$tbLGra = new Application_Model_LeitoGrade();
		$this->view->itens = $tbLGra->getProximos();
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/leito/medicamentos/index.js');
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/public/css/leito/medicamentos/index.css', 'all');
	}

	/**
	 * Lista as grades criadas para este leito/paciente
	 */
	public function verAction() {
		$io_codigo = $this->_getParam("io_codigo", FALSE);
		$prontuario = $this->_getParam("prontuario", FALSE);
                $impresso = $this->_getParam("impresso", FALSE);
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/leito/medicamentos/ver.js');
		if (!$io_codigo)
			return $this->_redirect("/leito");

		if (!empty($prontuario)) {
			$this->view->prontuario = $prontuario;
		}
                
                if (!empty($impresso)) {
			$this->view->impresso = $impresso;
		}

		$tbLGra = new Application_Model_LeitoGrade();
		$this->view->grades = $tbLGra->buscarGrades($io_codigo);
		$this->view->io_codigo = $io_codigo;
		$tbIo = new Application_Model_InternacaoObservacao();
		$io_observacao = $tbIo->buscar($io_codigo);
		//die('asdfasfda');
		$this->view->io_observacao = $io_observacao[io_observacao];
	}

	public function haReservasAction() {
		$lgra_codigo = $this->_getParam("lgra", FALSE);
		;
		if (!$lgra_codigo)
			$this->view->dados = array("reservas" => FALSE);

		else {

			$tbLGra = new Application_Model_LeitoGrade();
			$reservas = $tbLGra->buscarRerservas($lgra_codigo);
			$this->view->dados = array("reservas" => (bool) $reservas->count());
		}

		return $this->render("dados", NULL, TRUE);
	}

	public function historicoAction() {
		$this->_helper->layout->disableLayout();
		$lgra_codigo = $this->_getParam("lgra", FALSE);
		if (!$lgra_codigo)
			return FALSE;

		$tbLGD = new Application_Model_LeitoDispensacao();
		$this->view->itens = $tbLGD->getHistorico($lgra_codigo);
	}

	/**
	 * Lista os produtos que serão usados no leito
	 * Procura se há revervas, ou então procura 
	 * @return type 
	 */
	public function listarProdutosAction() {
		$lgra_codigo = $this->_getParam("lgra", FALSE);
		$tbUsr = new Application_Model_Usuarios();
		//$usu_codigo = $this->_getParam("usu_codigo", FALSE);

		//$this->view->dadosUsr = $tbUsr->find($usu_codigo)->current();


		if (!$lgra_codigo)
			return $this->_redirect("/leito/medicamentos");

		$this->_helper->layout->disableLayout();
		$tbLGra = new Application_Model_LeitoGrade();
		$this->view->lgra = $tbLGra->find($lgra_codigo)->current();

		if ($this->view->lgra->lgra_status != 1) {
			return FALSE; // páre por aqui
		}

		$this->view->lgra_codigo = $lgra_codigo;

		$this->view->itens = $tbLGra->buscarRerservas($lgra_codigo);
		$this->view->tipo = "1"; // reserva=>dispensar
		if (!$this->view->itens->count()) {
			$this->view->itens = $tbLGra->getMelhoresLotes($lgra_codigo);
			$this->view->tipo = "2"; // saldo/fracionado => reservar
		} else {
			$tbCFR = new Application_Model_ControleFracionadoReserva();
			$this->view->itens = $tbCFR->reservasToTable($this->view->itens);
		}
	}

	public function dispensarDaReservaAction() {
		//echo "<pre>".print_r($_REQUEST,1);exit;
		$reservas = $this->_request->getPost("cfr", FALSE);
		$lgra_codigo = $this->_request->getPost("lgra_codigo", FALSE);
		$usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
		$num = $this->_request->getPost("num", FALSE); // 1ª dispensacao, 2ª dispensação...
		$tbLDis = new Application_Model_LeitoDispensacao();
		try {
			$tbLDis->dispensar($lgra_codigo, $reservas, $num, $usr_codigo);
			$this->view->dados = array("success" => TRUE, "reload" => TRUE);
		} catch (Exception $exc) {
			$this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
			if ($exc->getCode() == 999) {
				$this->view->dados['reload'] = TRUE;
			}
		}

		return $this->render("index", NULL, TRUE);
	}

	/**
	 * View para criar a grade para dispensação
	 */
	public function dispensarAction() {
		$io_codigo = $this->_getParam("io_codigo", FALSE);
		$ate_codigo = $this->_getParam("ate_codigo", FALSE);
		$prontuario = $this->_getParam("prontuario", FALSE);
		//die($prontuario);
		$this->view->prontuario = $prontuario;
		$this->_helper->layout->setLayout("simples"); // abas manuais
		$this->view->headMeta()->setName('viewport', 'width=device-width, user-scalable=no');

		$this->view->io_codigo = $io_codigo;
		$this->view->ate_codigo = $ate_codigo;
                //Pega todos as administrações e as informa a view
                $tbAdmProd = new Application_Model_TbAdministracaoProduto();
		$this->view->administracoes = $tbAdmProd->getTodasAdministracoes();
	}

	public function modeloAction() {
		$this->_helper->layout->disableLayout();
	}

	public function salvarAction() {

		$tbLGra = new Application_Model_LeitoGrade();
		try {
			$tbLGra->salvarFromArray($_POST);
			$io_codigo = $this->_request->getPost("io_codigo", FALSE);
			$ate_codigo = $this->_request->getPost("ate_codigo", FALSE);
			$prontuario = $this->_request->getPost("prontuario", FALSE);
			$this->_redirect("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
		} catch (Exception $exc) {
			echo $exc->getMessage();
			exit;
		}
	}

	public function cancelarGradeAction() {
		$lgra_codigo = $this->_request->getPost("lgra_codigo", FALSE);
		if (!$lgra_codigo)
			$this->view->dados = array("error" => TRUE, "mensagem" => "Informe o lgra_codigo!");

		else {
			$tbLGra = new Application_Model_LeitoGrade();
			$tbLGra->cancelar($lgra_codigo);
			$this->view->dados = array("success" => TRUE);
		}
		$this->render("dados", NULL, TRUE);
	}

	public function validacaoAction() {
		$this->_helper->layout->disableLayout();
		$this->view->url = "/WebSocialSaude/biometria/retornaUsuario.php";
		$this->view->height = 200;
		$this->render("iframe", NULL, TRUE);
	}

}

