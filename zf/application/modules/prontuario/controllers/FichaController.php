<?php

class Prontuario_FichaController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("simples");
	}

	/**
	 * Deve carregar todo o histórico do paciente
	 */
	public function indexAction() {
                $_p = new Zend_Session_Namespace("prontuario");
		$tbInt = new Application_Model_AtendimentoInternacao();
		$_p->age = (object) $tbInt->getInternacaoEAgendamento($this->_getParam("age", FALSE))->current()->toArray();
                
                
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio/usuario/prontuario.css"),"all");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio.css"),"all");
		$this->view->age_codigo = $this->_getParam("age", FALSE);
                $usu_codigo = $this->getUsuCodigoAgendamentoAction($this->view->age_codigo);
                $this->view->usu_codigo = $usu_codigo;
                $usr_codigo = $this->getUsrCodigoAgendamentoAction($this->view->age_codigo);
                $this->view->usr_codigo = $usr_codigo;
                $this->view->esp_codigo = $_p->age->esp_codigo;
                $this->view->age_data = $_p->age->age_data;
                
		
		$tbUsu = new Application_Model_Usuario();
		$opcoes = array("alertas","pre-consulta","atendimentos","procedimentos","medicamentos",
						"pre-consulta" => array(
							"incluirSemObservacao" => TRUE
						));
		
		
		// já existe atendimento?
		$tbAte = new Application_Model_Atendimento();	
		$this->view->dadosAte = $tbAte->buscar($this->view->age_codigo);

		// procedimentos disponiveis para este CBO		
		$tbProc = new Application_Model_Procedimento();
		$this->view->procedimentos = $tbProc->selectTag();	
		
		// receita médica
		$tbIRec = new Application_Model_ReceitaItens();
		$this->view->medicamentos = $tbIRec->receitasPorUsuario($usu_codigo);
		$this->view->dados = $tbUsu->relProntuario($usu_codigo, FALSE, FALSE, $opcoes);
                //die("e agora");
	}
	
	public function index2Action(){
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio/usuario/prontuario.css"),"all");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio.css"),"all");
		
		$this->view->usu_codigo = $this->_getParam("usu", FALSE);
		
		$tbUsu = new Application_Model_Usuario();
		$this->view->dados = $tbUsu->relProntuarioFicha($this->view->usu_codigo);
		//echo "<pre>" . print_r($this->view->dados, 1) . "</pre>";
		//exit;
	}

	/**
	 * Imprime o atendimento feito pelo médico
	 * Deve ser carimbado e assinado pelo médico,
	 * e depois guardado junto a ficha (física) do paciente
	 */
	public function atendimentoAction(){
                $ate_codigo = $this->_getParam("ate", FALSE);
		if(!$ate_codigo)
			return $this->_redirect ("/prontuario");
		
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio/usuario/prontuario.css"),"all");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/css/relatorio.css"),"all");
		
		$tbUsu = new Application_Model_Usuario();
		$tbAte = new Application_Model_Atendimento();
                
		
		$ate = $tbAte->buscar($ate_codigo);
		
                $this->view->uni_desc = $ate->uni_desc; 
		$this->view->uni_endereco = $ate->uni_endereco; 
		$this->view->uni_numero = $ate->uni_numero; 
		$this->view->usu_codigo = $ate->usu_codigo;
		$this->view->usr_nome = $ate->usr_nome;
		$this->view->usr_num_conselho = $ate->usr_num_conselho;
                
		
		$this->view->dados = $tbUsu->relProntuarioFicha($this->view->usu_codigo, FALSE, FALSE, $ate->usr_codigo, 1);
		
                //echo "<pre>" . print_r($this->view->dados, 1);
                //die();
                
	}
	
	public function internacaoAction(){
            $this->view->title = "Ficha de Internação";
            // Chamando o Model de Internação
            $tbInt = new Application_Model_Internacao();
            $ate_codigo = $this->_getParam("id", FALSE);
            // Criando array de dados pra impressão da ficha
            $dados = $this->view->ficha = $tbInt->getFichaInternacao($ate_codigo)->toArray();
            // Se tiver observação interna, busca a grade
            if($dados["io_codigo"]) { 
                $dados_grade = $this->view->dados_grade = $tbInt->getGradeMedicacaoFicha($dados["io_codigo"])->toArray(); 
            }
            // Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts  
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
            // Chamando a ficha para exibição dos dados
            $this->render("internacao");
	}
        
        public function internacaoHospitalarAction(){
            $this->_helper->layout->disableLayout();
            $tbAge = new Application_Model_Agendamento();
            $ageCodigo = $tbAge->usuEmAberto()->age_codigo;
            $this->view->dados = $tbAge->getDadosAgendamentoUsuario($ageCodigo);
        }
        
        public function getUsuCodigoAgendamentoAction($age_codigo){
            $tbAge = new Application_Model_Agendamento();
            $usu_codigo = $tbAge->getAgendamento($age_codigo)->usu_codigo;
            return $usu_codigo;
        }
        
        public function getUsrCodigoAgendamentoAction($age_codigo){
            $tbAge = new Application_Model_Agendamento();
            $usr_codigo = $tbAge->getAgendamento($age_codigo)->med_codigo;
            return $usr_codigo;
        }
        
        public function finalizarAction(){
            $tbAge = new Application_Model_Agendamento();
            $age_codigo = $this->_getParam("age_codigo",FALSE);
            $tbAge->alteraSituacao("A",$age_codigo);
            return $this->render("dados", NULL, TRUE);
         }
}

