<?php

class Prontuario_InternacaoObservacaoController extends Zend_Controller_Action {

	public function init() {
		//$this->_helper->acl->copiarPermissao("zf/prontuario/index");
   
		Zend_Layout::getMvcInstance()->setLayout("prontuario");// essa linha fala que vou utilizar o layout do prontuário(zf/aplication/layout)
		$this->view->title = "Internação / Observação";
	}
	
	/**
	 * Responsavel por gerar a view index da enfermagem
	 */
	public function indexAction(){
		$tbUsr = new Application_Model_Usuarios();
		$tbIO = new Application_Model_InternacaoObservacao(); 
                $ate_codigo =  $tbIO->buscarAtual();
		$this->view->ate_codigo = $ate_codigo;
                
                $io_codigo = $this->_getParam("id", FALSE);
		if($io_codigo)
                        $this->view->dados = $tbIO->buscar($io_codigo);
                
               // $teste = $tbIO->getAtual($ate_codigo);
                $this->view->dados = $tbIO->getAtual($ate_codigo);
               // echo "<pre>".print_r($teste,1);die();
  
	}
	

	
	public function verAction(){
		$io_codigo = $this->_getParam("id", FALSE);
		if(!$io_codigo)
			return $this->_redirect ("/prontuario/internacao-observacao/index");
		
                
		$tbIo = new Application_Model_InternacaoObservacao();
                $tbUsr = new Application_Model_Usuarios();
        
		$this->view->io_codigo = $io_codigo_codigo;
		//$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
		$this->view->dados = $tbIo->buscar($io_codigo);
                //die("vish");
	}

	
	public function salvarAction() {
                //die("alo");
		if ($this->_request->isPost()) {
                        $data_cadastro = date("d/m/Y H:m:s");
                        $tbUsr = new Application_Model_Usuarios();
                        $dados = array(
				"io_observacao" => $this->_request->getPost("io_observacao", FALSE),
				"io_codigo" => $this->_request->getPost("io_codigo",FALSE),
                                "io_status" => $this->_request->getPost("io_status",FALSE),
                                "io_data_cadastro" => "$data_cadastro",
                                "usr_codigo" => $tbUsr->getUsrAtual()->usr_codigo,
                                "io_situacao_internacao" => "1"
			);
                        $condicao = $this->_request->getPost("io_codigo",FALSE);
                        $tbAtin = new Application_Model_AtendimentoInternacao();
			try {
				// Salvando os dados de internação 
                                $tbIO = new Application_Model_InternacaoObservacao();
                                $tbAte = new Application_Model_Atendimento();
                                $ateCodigo = $tbAte->temAtendimento()->ate_codigo;
				$this->view->dados = (object) $dados;
				$io_codigo = $tbIO->salvar($dados);
                                // Encaminhando código pra view
                                $this->view->dados->io_codigo = $io_codigo;
                                $dados2 = array("io_codigo"=>$io_codigo,
                                                "ate_codigo"=>$ateCodigo);
                                
                                if(!$condicao){
                                    $atin_codigo = $tbAtin->salvar($dados2);
                                }
                                $this->view->dialog = array("Confirmação", "Encaminhado com sucesso!", 300, 140);
				//$this->render("index"); // faz com que na ação salvar ele tras o código pronto 
                                return $this->_redirect("/prontuario/internacao-observacao/index/id/$io_codigo");
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->render("index");
			}
		} else {
			$this->_redirect("/prontuario/internacao-observacao");
		}
	}
	
	public function salvarPostoAction(){
		if ($this->_request->isPost()) {

			$dados = array(
				"pe_codigo" => $this->_request->getPost("pe_codigo",FALSE),
				"proc_codigo" => $this->_request->getPost("proc_codigo",FALSE),
				"pe_observacao" => $this->_request->getPost("pe_observacao",FALSE),
				"pe_descricao" => $this->_request->getPost("pe_descricao",FALSE)
			);
                        
			try {
                           
				$tbPE = new Application_Model_PostoEnfermagem();
				$tbPE->salvar($dados);
                                 
				$this->view->dialog = array("Confirmação", "Salvo com sucesso!", 300, 140);
				$this->view->dados = (object) $dados;
				$tbUsr = new Application_Model_Usuarios();
                                
				$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
				$this->render("ver"); // faz com que na ação salvar ele tras o código pronto 
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$tbUsr = new Application_Model_Usuarios();
				$this->view->esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
				$this->render("ver");
			}
		} else {
			$this->_redirect("/prontuario/enfermagem");
		}
	}
	
	public function itensAction(){
		$pe_codigo = $this->_getParam("cod", FALSE);
		
		if(empty($pe_codigo))
			return $this->_redirect("/prontuario/enfermagem");
			
		$tbPat = new Application_Model_ProcedimentoAtendimento();
		$this->view->itens = $tbPat->getHistorico($pe_codigo,Application_Model_ProcedimentoAtendimento::POSTO_ENFERMAGEM);
	}

	public function excluirAction(){
		$pe_codigo = $this->_getParam("cod"); // cod = pe_codigo	
		$id = (int) $this->_getParam("id",0); // id = pat_codigo
		if(!$id)
			return $this->_redirect ("/prontuario/enfermagem");
		
		$tbPA = new Application_Model_ProcedimentoAtendimento();
		$tbPA->excluir($id);
			
		return $this->_redirect("/prontuario/enfermagem/ver/cod/$pe_codigo");
	}
	
	public function finalizarAction(){
		$pe_codigo = $this->_getParam("id"); // cod = pe_codigo
		if(!$pe_codigo)
			return $this->_redirect ("/prontuario/enfermagem");
		
		$tbPe = new Application_Model_PostoEnfermagem();
		$tbPe->finalizar($pe_codigo);
		return $this->_redirect ("/prontuario/enfermagem");
	}
        
	public function quantidadeAction(){
                $tbUsr = new Application_Model_Usuarios();
                $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;
		$tbPosto = new Application_Model_PostoEnfermagem();
		$this->view->quantidade = $tbPosto->getLista($uni_codigo);
	}
	
}

