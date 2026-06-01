<?php
    class Leito_PrescricaoEnfermagemController extends Zend_Controller_Action {


	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
	
		$this->view->title = "Prescrição de enfermagem";
	}
	
	public function indexAction(){
		$obs = $this->_getParam("obs", FALSE);
		
		$this->view->obs = $this->_getParam("obs", FALSE);
		$this->view->ate_codigo = $this->_getParam("ate_codigo", FALSE);
		$this->view->io_codigo = $this->_getParam("io_codigo", FALSE);
		$this->view->cod = $this->_getParam("cod", FALSE);
	
		
	}


	public function salvarAction() {
            if ($this->_request->isPost()) {			
                    $dados = array(
                            "pres_higiene_corporal" => $this->_request->getPost("pres_higiene_corporal", NULL),
                            "pres_higiene_oral" => $this->_request->getPost("pres_higiene_oral", NULL),
                            "pres_ingesta_hidrica" => $this->_request->getPost("pres_ingesta_hidrica", NULL),
                            "pres_ingesta_alimentar" => $this->_request->getPost("pres_ingesta_alimentar", NULL),
                            "pres_realizar_curativo" => $this->_request->getPost("pres_realizar_curativo", NULL),
                            "pres_realizar_decubito" => $this->_request->getPost("pres_ingesta_hidrica", NULL),
                            "pres_observar_padrao_respiratorio" => $this->_request->getPost("pres_observar_padrao_respiratorio", NULL),
                            "pres_observar_perfuracao_pereferica" => $this->_request->getPost("pres_observar_perfuracao_pereferica", NULL),
                            "pres_observar_eliminacoes" => $this->_request->getPost("pres_observar_eliminacoes", NULL),
                            "pres_observar_queixas" => $this->_request->getPost("pres_observar_queixas", NULL),
                            "pres_observar_nivel_consciencia" => $this->_request->getPost("pres_observar_nivel_consciencia", NULL)
                    );

                    try {
                            $tbReq = new Application_Model_RequisicaoExame();
                            $tbReq->salvar($dados);
                            $this->view->dialog = array("Confirmação","Solicitação de exame registrado com sucesso!",300,140);
                            $tbProc = new Application_Model_Procedimento();
                            $this->view->procedimento = $tbProc->selectTag();
                            if($obs != "S"){
                                    //$this->render("index");
                              //  die("akooka");
                                    $this->_redirect("prontuario/exame");
                            }else{
                                    $this->_redirect("/prontuario/exame/index/obs/S/io_codigo/".$io_codigo."/ate_codigo/".$ate_codigo);
                            }
                    } catch (Zend_Validate_Exception $exc) {
                            $this->view->erro = $exc->getMessage();
                            $this->view->dados = (object) $dados;				
                            $this->render("index");

                    }
            } else {
                    $this->_redirect("/prontuario/exame");
            }
	}

	
    }