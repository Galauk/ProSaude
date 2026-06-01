<?php

class Prontuario_ProcedimentoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		//Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Procedimento";
	}

	public function indexAction() {
		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
        ini_set('memory_limit','5048M');
       $tbUsr = new Application_Model_Usuarios();
        $this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
		$this->view->obs = $this->_getParam("obs", FALSE);
		$this->view->io_codigo = $this->_getParam("io_codigo", FALSE);
        $this->view->imprimi = $this->_getParam("imprimi", FALSE);
		if(!$this->_getParam("obs", FALSE)){
            $this->_helper->layout->setLayout("prontuario");
			//$this->_helper->layout->disableLayout();
			//$this->render("itens");
		}
       $tbProc = new Application_Model_Procedimento();
		// $this->view->procedimentos = $tbProc->selectTag();//comentado 13/12/2016 parece que nao serve pra nada
       $tbAte = new Application_Model_Atendimento();

		if($this->_getParam("obs", FALSE) == "S"){
			 $this->view->ate_codigo = $this->_getParam("ate_codigo", FALSE);
		 }else{
			$ate = $tbAte->temAtendimento();
			if ($ate)
				$this->view->ate_codigo = $ate->ate_codigo;
		}

		if(!$this->_getParam("obs", FALSE) == "S"){
			$this->view->bloquear = $this->isMedicoSemAtendimento();
		}
	}

	public function itensAction() {
		$obs = $this->_getParam("obs",FALSE);
		$tbPat = new Application_Model_ProcedimentoAtendimento();
			$this->view->itens = $tbPat->getHistoricoGeral();
			$this->view->obs = $obs;
	}
	public function itensInternacaoAction() {
		$obs = $this->_getParam("obs",FALSE);
		$io_codigo = $this->_getParam("io_codigo",FALSE);
		$limit = $this->_getParam("limit",FALSE);
		//die("limit".$limit);
		$tbPat = new Application_Model_ProcedimentoAtendimento();
		$this->view->itens = $tbPat->getHistoricoInternacao($io_codigo,$limit);
		$this->view->obs = $obs;
		$this->render("itens");
	}

	public function historicoAction() {
		$ate_codigo = $this->_getParam("id", FALSE);
                $age_codigo = $this->_getParam("age_codigo",FALSE);
		if (!$ate_codigo)
			return $this->_redirect("/prontuario");

		$tbPat = new Application_Model_ProcedimentoAtendimento();
                //die($ate_codigo);
		$this->view->itens = $tbPat->getHistorico($age_codigo);
	}

	public function salvarAction() {
		// die("éaqui");
		if ($this->_request->isPost()) {
			$tbProc = new Application_Model_Procedimento();
			// $this->view->procedimentos = $tbProc->selectTag(); comentado 13/12/2016 estava dando tela azul ao salvar procedimento
			// Procedimentos Realizados não podem ser editados;
		//	echo "<pre>".print_r($_POST,1); exit;
			$json = $this->_request->getPost("json", FALSE);
			$obs = $this->_request->getPost("obs",FALSE);
			$ate_codigo = $this->_request->getPost("ate_codigo",FALSE);
			$io_codigo = $this->_request->getPost("io_codigo",FALSE);

            if($io_codigo){
                $this->view->io_codigo = $io_codigo;
            }

			//die($this->_request->getPost("obs"));
			$dados = array(
				"ate_codigo" => $this->_request->getPost("ate_codigo", 0),
				"proc_codigo" => $this->_request->getPost("proc_codigo", NULL),
				"cd10_codigo" => $this->_request->getPost("cd10_codigo", NULL)
			);

			try {
				$obs = $this->_request->getPost("obs",FALSE);

				$tpPA = new Application_Model_ProcedimentoAtendimento();
				$pat_codigo = $tpPA->salvar($dados,$obs,$json);

				if ($json){
					$tbPat = new Application_Model_ProcedimentoAtendimento();
					$this->view->dados = $tbPat->buscar($pat_codigo)->toArray();
					return $this->render("dados", NULL, TRUE);
				}

				$this->view->dialog = array("Confirmação", "Procedimento registrado com sucesso!", 300, 140);
                                //die($obs);
					if($obs == "S"){
						$this->_redirect("/prontuario/procedimento/index/obs/S/io_codigo/$io_codigo/ate_codigo/$ate_codigo");
					}else{
						$this->_helper->layout->setLayout("prontuario");
						$this->render("index");
					}

			} catch (Zend_Validate_Exception $exc) {
				if ($json) {
					$this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
					return $this->render("dados", NULL, TRUE);
				}
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->render("index");
			}
		} else {
			$this->_redirect("/prontuario/procedimento");
		}
	}

	public function excluirAction() {
		$id = (int) $this->_getParam("id", 0);
                $ate_codigo = $this->_getParam("ate_codigo",FALSE);
		$io_codigo = $this->_getParam("cod",FALSE);
		if (!$id)
			return $this->_redirect("/prontuario/procedimento");

		$tbPA = new Application_Model_ProcedimentoAtendimento();
		$tbPA->excluir($id);

		$json = $this->_request->getPost("json", FALSE);
		if($json){
			return $this->render("dados",NULL, TRUE);
		}
                if($io_codigo){
                   return $this->_redirect ("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
                }else{
                    return $this->_redirect ("/prontuario/procedimento");
                }

	}

	private function isMedicoSemAtendimento() {
		// se for médico,
		$tbUsr = new Application_Model_Usuarios();
		if ($tbUsr->isMedico()) {
			// verifica se há um atendimento feito pelo médico
			$tbAte = new Application_Model_Atendimento();
			if ($tbAte->temAtendimentoMedico())
				return FALSE;
			else
				return TRUE;
		}
		return FALSE;
	}

}

