<?php
	class Leito_AtendimentoController extends Zend_Controller_Action {
		public function init(){
			$this->view->title = "Atendimento da internação";
			$this->_helper->layout->setLayout("simples");
		}		
		public function indexAction(){
		
                    $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.buscar.js');
                    $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/prontuario/receita-medica.js');
                    $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/prontuario/atendimento.js');
                    $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/prontuario/exame.js');


                    $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/prontuario.css','all');
                    //echo "<pre>".print_r($_REQUEST,1);
                    $tbUsr = new Application_Model_Usuarios();
                    $tbAte = new Application_Model_Atendimento();
                    $tbPC  = new Application_Model_PreConsulta();
                    $tbInt = new Application_Model_AtendimentoInternacao();
                    $tbIo = new Application_Model_InternacaoObservacao();

                    $io_codigo = $this->_getParam("cod",FALSE);
                    $ate_codigo = $this->_getParam("ate_codigo",FALSE);
                    $ultimo  = (object) $tbInt->getInternacao($io_codigo)->current()->toArray();
                    $usu_codigo = $ultimo->usu_codigo;
                    $age = $ultimo->age_codigo;

                    if($io_codigo){
                            $this->view->historico = $tbInt->getHistoricoInternacao($io_codigo);
                           // echo "<pre>".print_r($this->view->historico,1);die();
                            $this->view->classi = $tbPC->getHistorico($usu_codigo);
                            $this->view->age = $ultimo;
                            $this->view->temAtendimento = $tbAte->temAtendimentoMedicoNaInternacao($ate_codigo);
                            $this->view->temPreConsulta = $tbPC->temPreConsulta($ultimo->age_codigo);
                            //echo "<pre>".print_r($tbPC->temPreConsulta($ultimo->age_codigo)->toArray(),1);
                            $this->view->ate_codigo = $ate_codigo;
                            $observacao_internacao = $tbIo->buscar($io_codigo)->toArray();
                            $this->view->observacao = $observacao_internacao[io_observacao];
                            $this->view->usu_codigo = $usu_codigo;
                    }else{
                            return $this->_redirect ("/leito/internacao");
                    }
	
		}
		public function timeLineAction(){
			$io_codigo = $this->_getParam("io_codigo",FALSE);
            $this->view->url = "/WebSocialSaude/timeline/examples/example_json.php?io_codigo=$io_codigo";
            $this->view->height = 189;
            return $this->render("iframe", NULL, TRUE);

		}
		public function  altaAction(){
			$usu_codigo = $this->_getParam("usu_codigo",FALSE);
			
			//$this->_helper->layout->disableLayout();
			$this->view->title = "Alta da Internação";
			$tbAlt = new Application_Model_Alta();
			$this->view->dados = $tbAlt->fetchAll();
			$this->view->usu_codigo = $usu_codigo;
			//$this->view->dados  = $tbAlt->getItens();
		}
		public function  salvaraltaAction(){
			//echo "<pre>".print_r($_REQUEST,1);exit;
			$io_codigo = $this->_getParam("cod",FALSE);
			$ate_codigo = $this->_getParam("ate_codigo",FALSE);
			$usr = new Application_Model_Usuarios();
			$usr_codigo = $usr->getUsrAtual()->usr_codigo;
			$data_alta = date("d/m/Y H:m:s");
			
			$alta = $this->_request->getPost("alta", FALSE);
			if ($this->_request->isPost()) {					
                            $dados = array(
                                    "io_codigo" => $io_codigo,
                                    "io_situacao_internacao" => 3,
                                    "alt_codigo" => $this->_request->getPost("alt_codigo", FALSE),
                                    "io_observacao_alta" => $this->_request->getPost("alt_observacao", FALSE),
                                    "io_data_alta" => $data_alta,
                                    "usr_codigo_alta" => $usr_codigo);
                            
                            $tbLei = new Application_Model_Leito();
                            $tbPl = new Application_Model_PacienteLeito();
                            $lei_codigo = $tbPl->getLeitoInternado($io_codigo)->lei_codigo;
                            $atualiza_leito = array("lei_codigo"=>$lei_codigo,
                                                    "lei_ocupado"=>"f");
                            try {			
                                    $tbInt = new Application_Model_InternacaoObservacao();
                                    $tbUsu = new Application_Model_Usuario();
                                    $dadosUsuario = array(
                                            "usu_codigo" => $this->_getParam("usu_codigo",FALSE),
                                            "usu_obito" => "S",
                                            "usu_dt_obito" => date("d/m/Y")
                                    );

                            //	echo "<pre>".print_r($dadosUsuario,1);exit;
                                    $tbLei->salvar($atualiza_leito);
                                    $tbInt->salvar($dados);
                                    $tbUsu->salvar($dadosUsuario);
                                    //$tbPac->excluir($io_codigo);
                                    $this->_redirect("leito/internacao");

                            } catch (Zend_Validate_Exception $exc) {

                                    $this->view->erro = $exc->getMessage();
                                    $this->view->dados = $dados;

                                    $this->render("index");
                            }
			} else {
				$this->_redirect("/leito/internacao");
			}
                        
                        
		}

		public function abasatendimentoAction(){
			$io_codigo = $this->_getParam("io_codigo",FALSE);
			$ate_codigo = $this->_getParam("ate_codigo",FALSE);
			$obs = $this->_getParam("obs",FALSE);
           // $this->view->url = "/WebSocialSaude/timeline/examples/example_json.php?io_codigo=$io_codigo";
            $this->view->obs = $obs;
            $this->view->io_codigo = $io_codigo;
            $this->view->ate_codigo = $ate_codigo;
            //return $this->render("iframe", NULL, TRUE);
		}
		public function cancelarAction(){
			$this->_redirect("leito/internacao");
		}
		public function finalizarAction(){
                    
		   $tbAte = new Application_Model_Atendimento();
		   $data = array(
			   "ate_codigo" => $this->_getParam("ate_codigo",FALSE),
			   "ate_atendido" => "S"
		   );
                   $_COOKIE[ate_reclamacao] = "";
                  
		  // echo "<pre>".print_r($data,1);exit;
		   $tbAte->atualizaStatus($data);
                   $obs = $this->_getParam("obs",FALSE);
                   return $this->render("dados",null,true);
//                   if(!$obs)
//                       $this->_redirect("leito/internacao");
		}
                public function retornoAction(){
                    
                    $this->_helper->layout->disableLayout();
                    $ate_codigo = $this->_getParam("ate_codigo",FALSE);
                    $retorno = $this->_getParam("retorno",FALSE);
                    $io_codigo = $this->_getParam("io_codigo",FALSE);
                    $data_alta = date("d/m/Y H:m:s");
                    $tbInt = new Application_Model_InternacaoObservacao();
                    $tbAte = new Application_Model_Atendimento();
                    $tbAtin = new Application_Model_AtendimentoInternacao();
                    $dados = array("io_codigo"=>$io_codigo,
                                   "io_situacao_internacao"=>"3",
                                   "io_observacao"=>"akokakoa",
                                   "alt_codigo"=>"3",
                                   "io_data_alta"=>"$data_alta");
                    
                    
                    $ate_origem = $tbAtin->getAtendimentoDeOrigem($io_codigo);
                   // die($ate_origem->ate_codigo);
                    $dadosAte = array("ate_codigo"=>$ate_origem->ate_codigo,
                                      "ate_encaminhamento"=>"S");
                    $tbAte->salvar($dadosAte);
                    $tbInt->salvar($dados);
                    
                   
                    return $this->render("dados", null, true);
                    
                }
		



		
	}
?>
