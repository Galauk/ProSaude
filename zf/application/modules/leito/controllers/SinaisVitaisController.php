<?php

class Leito_SinaisVitaisController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");		
		$this->view->title = "Sinais vitais";
		
	}

	public function indexAction() {
	//	if ($this->isMedicoSemAtendimento())
	//		$this->_redirect("/prontuario/pre-consulta/ultima");
		$io_codigo = $this->_getParam("cod", FALSE);
		$ate_codigo = $this->_getParam("ate_codigo", FALSE);
		$tbSi = new Application_Model_SinaisVitais();
		$this->view->historico = $tbSi->getHistorico($io_codigo);
			
	}


	public function verAction() {
		$this->_helper->layout->disableLayout();
		$ate_codigo = $this->_getParam("ate_codigo", FALSE);
		//$io_codigo =  $this->_getParam("cod", FALSE);
		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->semLayout = $this->_getParam("sem-layout", FALSE);
		$this->view->readonly = $this->_getParam("readonly", FALSE);
		
		if($this->view->semLayout)
			$this->_helper->layout->disableLayout();

		$tbSi = new Application_Model_SinaisVitais();
		if ($ate_codigo){
			$si = $tbSi->getHistorico(FALSE,FALSE,FALSE, $ate_codigo);
			//die("pelo menos chegou");
			$si = $si->current();

		}
		//echo "<pre>".print_r($si,1);exit;
		/*if ($io_codigo){
			$si = $tbSi->getHistorico($io_codigo,FALSE,FALSE, FALSE);
			$si = $si->current();
		}*/
		
		/*if (!$si)
			return $this->_redirect("/leito/atendimento");*/

		$this->view->dados = $si;
		$this->render("index");
	}
	public function verPorInternacaoAction() {
		//$ate_codigo = $this->_getParam("ate_codigo", FALSE);
		$io_codigo =  $this->_getParam("cod", FALSE);
		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->semLayout = $this->_getParam("sem-layout", FALSE);
		$this->view->readonly = $this->_getParam("readonly", FALSE);
		
		if($this->view->semLayout)
			$this->_helper->layout->disableLayout();

		$tbSi = new Application_Model_SinaisVitais();
	
		if ($io_codigo){
			$si = $tbSi->getHistorico($io_codigo,FALSE,FALSE, FALSE);
			$si = $si->current();
		}
		
		/*if (!$si)
			return $this->_redirect("/leito/atendimento");*/

		$this->view->dados = $si;
		$this->render("index");
	}

	public function salvarAction() {
		if ($this->_request->isPost()) {
			$ate_codigo = $this->_getParam("ate_codigo",FALSE);
			$io_codigo = $this->_getParam("cod",FALSE);
             
			
			$json = $this->_request->getPost("json", FALSE);
                        
                      
                    
			$dados = array(
				"si_codigo" => $this->_request->getPost("si_codigo", FALSE),
				"si_temperatura" => $this->_request->getPost("temperatura", NULL),
				"si_peso" => $this->_request->getPost("peso", NULL),
				"si_altura" => $this->_request->getPost("altura", NULL),
				"si_pressao_sistolica" => $this->_request->getPost("pressao_sistolica", NULL),
				"si_pressao_diastolica" => $this->_request->getPost("pressao_diastolica", NULL),
				"si_freq_cardiaca" => $this->_request->getPost("freq_cardiaca", NULL),
				"si_freq_respiratoria" => $this->_request->getPost("freq_respiratoria", NULL),
				"si_perimetro_cefalico" => $this->_request->getPost("p_cefalico", NULL),
                                "si_glicose" => $this->_request->getPost("glicose", NULL),
				"si_dados" => $this->_request->getPost("obs", NULL),
				"ate_codigo" => $ate_codigo
                               
			);
                       //echo "<pre>".print_r($dados,1);exit;
			try {
				$tbSi = new Application_Model_SinaisVitais();
				$id = $tbSi->salvar($dados);
				if ($json)
					return $this->json($id);
				else
					return $this->_redirect("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");

			} catch (Zend_Validate_Exception $exc) {

				if ($json) {
					$this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
					$this->render("dados",NULL, TRUE);
				} else {
					$this->view->erro = $exc->getMessage();
					$this->view->dados = (object) $dados;
					$this->render("index");
				}
			}
		} else {
			$this->_redirect("/leito/sinais-vitais");
		}
	}

	public function historicoAction(){

		$io_codigo = $this->_getParam("cod",FALSE);
		$ate_codigo = $this->_getParam("ate_codigo",FALSE);
		
		$this->_helper->layout->disableLayout();
		$tbSi = new Application_Model_SinaisVitais();
                
		$this->view->ate_codigo = $ate_codigo;
		$this->view->historicoSinais = (object) $tbSi->getHistorico($io_codigo)->toArray();
		$this->view->historicoPreConsulta = $tbSi->getHistorico($io_codigo)->toArray();
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

