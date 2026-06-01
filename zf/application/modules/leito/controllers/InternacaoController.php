<?php
class Leito_InternacaoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->layout->setLayout("simples"); // abas manuais
		//$this->view->title = "Controle de Internação";
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.tinyscrollbar.min.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.core.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.widget.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.mouse.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.draggable.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.ui.droppable.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.contextMenu.js');
		$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/jquery.contextMenu.css','all');
                        
	}

	public function indexAction() {
		// Chamando o model de Observações da internação
		$tbIO = new Application_Model_InternacaoObservacao();
		// Retorna os dados de controle pra internação
		$this->view->dados =  $tbIO->getLista();
		// Retorna Pacientes Internados
		$this->view->inter = $tbIO->getInternados();
		// Retorna Pacientes com alta
		$this->view->alta = $tbIO->getPacAlta();
		// Chamando o método de Internação
		$tbIn = new Application_Model_Internacao();
		// Busca os quartos que possui pacientes	
		$this->view->itens = $tbIn->buscaQuartos();

	}      
        
	public function verAction(){

	}
        
        public function quartosAction(){
            $usu_codigo = $this->_getParam("usu_codigo",0);
            $io_codigo = $this->_getParam("io_codigo",0);
            $acao = $this->_getParam("acao",0);
            
            $this->_helper->layout->disableLayout();
            
            $tbIn = new Application_Model_Internacao();
            if($acao == "nul" || $acao == "D"){
                $acao = "D";
            }
            $this->view->dados = $tbIn->buscaQuartos($acao)->toArray();
            
            //die($usu_codigo);
           
            $this->render("dados", null, true);
        }
        
        
        public function leitosAction(){
            $qua_codigo = $this->_getParam("id");
            $this->_helper->layout->disableLayout();
            $tbIn = new Application_Model_Internacao();
            $leitos = $tbIn->buscaLeitos($qua_codigo);
            $this->view->dados = $leitos;
            $this->render("dados", null, true);
            
        }
        
        public function cancelaAction(){
            $this->_helper->layout->disableLayout();
            $io_codigo = $this->_getParam("io_codigo");
            
            $tbAi = new Application_Model_AtendimentoInternacao();
            $qtde = $tbAi->verificaAtendimentosPorInternacao($io_codigo);
            //die($qtde[qtde]);
            if($qtde[qtde] > 1){
                 $this->view->dados = "E";
                return $this->render("dados", null, true);
            }else{

                $dados1 = array("io_codigo" => $io_codigo,
                                "io_situacao_internacao" => "1");
                $tbIO = new Application_Model_InternacaoObservacao();
                $tbIO->salvar($dados1);
                $tbPl = new Application_Model_PacienteLeito();
                $tbPl->excluir($io_codigo);

                $this->view->dados = "C";
                return $this->render("dados", null, true);
            }
            
        }
        
        public function internaAction(){
			
            $this->_helper->layout->disableLayout();
            //$usu_codigo = $this->_getParam("usu_codigo",0);
            $qua_codigo = $this->_getParam("qua_codigo",0);
            $io_codigo = $this->_getParam("io_codigo",0);
            $data_cadastro = date("d/m/Y H:m:s");
            $dados1 = array("io_codigo" => $io_codigo,
                            "io_situacao_internacao" => "2");
            $usu_codigo = $this->_getParam("usu_codigo",0);
            
            $tbIO = new Application_Model_InternacaoObservacao();
            $internado = $tbIO->buscaInternamentos($usu_codigo);
            if(!$internado[io_codigo]){
                $tbLei = new Application_Model_Leito();
                $leito = $tbLei->buscarLeitoLivre($qua_codigo);
                
                if($leito[lei_codigo] == "" || $leito[lei_codigo] == null){
                    $this->view->dados = "C";
                    return $this->render("dados", null, true);
                }else{
                    $tbIO->salvar($dados1);
                    $atualiza_leito = array("lei_codigo" => $leito["lei_codigo"],
                                            "lei_ocupado" => 't');
                    $tbLei->salvar($atualiza_leito);
                    $tbUsr = new Application_Model_Usuarios();
                    $dados2 = array("io_codigo"=>$io_codigo,
                                    "lei_codigo"=>$leito[lei_codigo],
                                    "pac_dtentrada_leito"=>$data_cadastro,
                                    "usr_codigo"=>$tbUsr->getUsrAtual()->usr_codigo);

                    $tbPL = new Application_Model_PacienteLeito();
                    $tbPL->salvar($dados2);
                    
                    $this->view->dados = "S";
                    
                    return $this->render("dados", null, true);
                    
                }
            }else{
               $this->view->dados = "E";   
               return $this->render("dados", null, true);
            }
        }
        
        public function liberaPacienteAction(){
            $this->_helper->layout->disableLayout();
            //$usu_codigo = $this->_getParam("usu_codigo",0);
             $io_codigo = $this->_getParam("io_codigo",0);
             $array_alta = array("io_codigo" => $io_codigo,
                                 "io_situacao_internacao" => "3");
             $tbIO = new Application_Model_InternacaoObservacao();
             try{
                 $tbIO->salvar($array_alta);
                 $this->view->dados = array("id"=>1);
             } catch (Zend_Validate_Exception $exc) {
                 $this->view->dados = array("msg" => $exc->getMessage());
             }
             
             return $this->render("dados",null,true);
        }
        
       
}

