<?php

class Prontuario_EncaminhamentoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		//Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Encaminhamento";
	}
	
	public function indexAction(){
		$this->_helper->layout->setLayout("prontuario");
		$tbAte = new Application_Model_Atendimento();		
		$ate = $tbAte->temAtendimentoMedico();
		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->obs = $this->_getParam("obs", FALSE);		
		$this->view->io_codigo = $this->_getParam("io_codigo", FALSE);
       // die(($this->_getParam("ate_codigo", FALSE) == "" ? $ate->ate_codigo : $this->_getParam("ate_codigo", FALSE)));
		$this->view->ate_codigo = ($this->_getParam("ate_codigo", FALSE) == "" ? $ate->ate_codigo : $this->_getParam("ate_codigo", FALSE));
		$this->view->usu_codigo = $this->_getParam("usu_codigo", FALSE);		
		if($this->view->obs){
			$this->_helper->layout->disableLayout();
			//$this->render("itens");
		}	
                
		$tbEsp = new Application_Model_Especialidade();
                
		$this->view->especialidade = $tbEsp->selectTags();
		//die("asdfasdf	");
	}
	
	public function itensAction(){	
		$tbEnc = new Application_Model_Encaminhamento();
                $tbConf = new Application_Model_Configuracao();
                $this->view->tipo_impresso_simples = $tbConf->getConfig("ENCAMINHAMENTO_SIMPLES");
		$this->view->itens = $tbEnc->getItens();	
		$this->view->obs = $this->_getParam("obs",FALSE);
	}
	/**
	 * Mostra os encaminhamentos da internação atual
	 */
	public function itensInternacaoAction(){
		$io_codigo = $this->_getParam("io_codigo",FALSE);
		$ate_codigo = $this->_getParam("ate_codigo",FALSE);
		$usu_codigo = $this->_getParam("usu_codigo",FALSE);
		$imprimi = $this->_getParam("imprimi",FALSE);
                
		//die($io_codigo."-".$ate_codigo."-".$usu_codigo."-".$imprimi);
                $tbEnc = new Application_Model_Encaminhamento();
		$this->view->itens = $tbEnc->getItensInternacao($io_codigo);	
		$this->view->obs = $this->_getParam("obs",FALSE);
                $this->view->io_codigo = $io_codigo;
		$this->view->usu_codigo = $usu_codigo;
		$this->view->ate_codigo = $ate_codigo;
		$this->view->imprimi = $imprimi;
		$this->render("itens");
	}
	
	public function historicoAction(){		
		$ate_codigo = $this->_getParam("id", FALSE);
		if(!$ate_codigo)
			return $this->_redirect ("/prontuario");
				
		$tbEnc = new Application_Model_Encaminhamento();
		$this->view->itens = $tbEnc->getHistorico($ate_codigo);
	}
        
        public function salvarAction() {
			if ($this->_request->isPost()) {

				$obs = $this->_request->getPost("obs",FALSE);			
				$ate_codigo = $this->_request->getPost("ate_codigo",FALSE);
				$io_codigo = $this->_request->getPost("io_codigo",FALSE);	
				$dados = array(
					"ate_codigo" => $this->_request->getPost("ate_codigo", 0),
					"enc_codigo" => $this->_request->getPost("enc_codigo", 0),
					"esp_codigo" => $this->_request->getPost("esp_codigo", NULL),
					"enc_descricao" => $this->_request->getPost("enc_descricao", NULL), //se vazio não salvar retornar erro
					"enc_internacao" => $this->_request->getPost("enc_internacao", NULL),
					"enc_urgencia" => $this->_request->getPost("enc_urgencia", NULL)
				);
				if($dados[esp_codigo] == 1159 && $dados[enc_descricao] == ""){
					$this->_redirect("prontuario/encaminhamento/index?alert=error");
					return $this->_redirect("prontuario/encaminhamento#tabs-1");
				}

				try {

					$tbEnc = new Application_Model_Encaminhamento();
					$tbEnc->salvar($dados,$obs);
					$this->view->dialog = array("Confirmação","Encaminhamento registrado com sucesso!",300,140);
					$tbEsp = new Application_Model_Especialidade();
					$this->view->especialidade = $tbEsp->selectTags();

					if($obs == "S"){
							$this->_redirect("leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
					}else{
							$this->_helper->layout->setLayout("prontuario");
							$this->render("index");
					}
				} catch (Zend_Validate_Exception $exc) {
					$this->view->erro = $exc->getMessage();
					$this->view->dados = (object) $dados;
					$this->render("index");
				}
			} else {
				$this->_redirect("/prontuario/encaminhamento");
			}
		}
        
        public function excluirAction(){
		$id = (int) $this->_getParam("id",0);
                $ate_codigo = $this->_getParam("ate_codigo",FALSE);
		$io_codigo = $this->_getParam("cod",FALSE);
                //die($io_codigo."-".$ate_codigo);
		if(!$id)
			return $this->_redirect ("/prontuario/encaminhamento");
		
		$tbEnc = new Application_Model_Encaminhamento();
		$tbEnc->excluir($id);
		//die($io_codigo);
		
                if($io_codigo){
                   return $this->_redirect ("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
                }else{
                   return $this->_redirect ("/prontuario/encaminhamento");
                }
	}
	
	public function imprimirAction(){   
		Zend_Layout::getMvcInstance()->setLayout("paisagem-print");
		$tbUsr = new Application_Model_Usuarios();
        $tbConf = new Application_Model_Configuracao();
        $tbAte = new Application_Model_Atendimento();
        $tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
        $this->view->usr = $tbUsr->getUsrAtual();
        $this->view->secretaria  = $sec[nome_secretaria];
        $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
        $this->view->tipo_impressao = "ENCAMINHAMENTO";
		$this->view->title = "Imprimir Encaminhamento Médico";
                
		$selecionados = $this->_getParam("selecionados", FALSE);
        $io_codigo = $this->_getParam("cod",FALSE);
        $usu_codigo = $this->_getParam("usu_codigo",FALSE);
        $ate_codigo = $this->_getParam("ate_codigo",FALSE);
        $usu = $tbAte->getDadosCabecalho($ate_codigo)->usu_codigo;
          //  die("asdfasdf".$ate_codigo);
              
           //     die($selecionados."0".$ate_codigo."---".$usu);
//		$id = (int) $this->_getParam("id",0);
//		if(!$id)
//			return $this->_redirect ("/prontuario/encaminhamento");
		$tbRac = new Application_Model_Raca();
        $this->view->raca = $tbRac->fetchAll();
		$tbEnc = new Application_Model_Encaminhamento();
		if($selecionados=='null') {
			$dd = $tbEnc->getHistorico($ate_codigo);
		} else {			
			$dd = $tbEnc->getHistoricoItens($selecionados);
		}
		$this->view->dados = $tbEnc->imprimir($ate_codigo,$io_codigo,$usu,$selecionados);		
		$this->view->dd = $dd;
		$this->view->tipo = $tipo;
		$this->view->isMedico = $tbUsr->isMedico();
	}
        
        public function encaminhamentoExternoAction(){
            $this->view->title = "Encaminhamento Externo22";
        }
        
        public function encaminhamentoExternoItensAction(){
            $tbEncExt = new Application_Model_EncaminhamentoExterno();
            $tbAte = new Application_Model_Atendimento();
            $ateCodigo = $tbAte->temAtendimentoMedico()->ate_codigo;
            $this->view->itens = $tbEncExt->listaEncExterno($ateCodigo);
        }
        
        public function salvarEncaminhamentoExternoAction(){
            if($this->_request->isPost()){
                $tbEncExt = new Application_Model_EncaminhamentoExterno();
                $tbAte = new Application_Model_Atendimento();
                $ateCodigo = $tbAte->temAtendimentoMedico()->ate_codigo;
                
                $dados = array(
                    "ate_codigo" => $ateCodigo,
                    "usr_codigo" => Application_Model_Agendamento::usuEmAberto()->med_codigo,
                    "enc_ext_agendado_para" => $this->_request->getPost("enc_ext_agendado_para"),
                    "enc_ext_contato" => $this->_request->getPost("enc_ext_contato"),
                    "enc_ext_data" => ($this->_request->getPost("enc_ext_data") ? $this->_request->getPost("enc_ext_data") : NULL) ,
                    "enc_ext_hora" => $this->_request->getPost("enc_ext_hora"),
                    "enc_ext_internacao" => $this->_request->getPost("enc_internacao"),
                    "enc_ext_urgencia" => $this->_request->getPost("enc_urgencia"),
                    "enc_ext_descricao" => $this->_request->getPost("enc_descricao")
                );
                
                try {
                    $tbEncExt->salvar($dados);
                    $this->view->dialog = array("Confirmação","Encaminhamento registrado com sucesso!",300,140);
                    $this->_helper->layout->setLayout("prontuario");
                    $this->_redirect("prontuario/encaminhamento/index/#tabs2-2");
                    //$this->render("index");
                } catch (Exception $exc) {
                    $this->view->erro = $exc->getMessage();
                    $this->view->dados = (object) $dados;
                    $this->render("index");
                }
                
            } else {
                $this->_redirect("prontuario/encaminhamento");
            }
        }
        
        public function excluirItemEncaminhamentoExternoAction(){
            if($this->_getParam("id")== ""){
                $this->_redirect("prontuario/encaminhamento/index/#tabs2-2");
            } else {
                $tbEncExt = new Application_Model_EncaminhamentoExterno();
                $encExtCod = $this->_getParam("id");
                $tbEncExt->excluir($encExtCod);
                $this->_redirect("prontuario/encaminhamento/index/#tabs2-2");
            }
        }
        
        public function encaminhamentoExternoImprimirAction(){
            Zend_Layout::getMvcInstance()->setLayout("print");
        	$ate_codigo = $this->_getParam("ate_codigo",FALSE);
			$this->view->title = "Imprimir Encaminhamento Médico Externo";
            $tbEncExt = new Application_Model_EncaminhamentoExterno();
            $tbSec = new Application_Model_Secretaria();
			$sec = $tbSec->fetchRow();
            $dados = $tbEncExt->getDadosImpEncaminhamentoExterno($ate_codigo);
            $idade = $tbEncExt->calculaIdade($dados[0][usu_codigo]);
            $this->view->dados = $dados;
            $this->view->idade = $idade[0][date_part];
            $this->view->secretaria = $sec;
        }
        
         public function contraReferenciaAction(){
            $io_codigo = $this->_getParam("cod",FALSE);
            $usu_codigo = $this->_getParam("usu_codigo",FALSE);
            //die($usu_codigo."0".$io_codigo);
            $id = (int) $this->_getParam("id",0);
            $tbSec = new Application_Model_Secretaria();
            $this->view->sec = $tbSec->getDadosSec()->toArray();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->_helper->layout->disableLayout();
            $this->view->dados_usr = $tbUsr->getUsrAtual();
            $tbEnc = new Application_Model_Encaminhamento();
            $this->view->dados = $tbEnc->imprimir($id,$io_codigo,$usu_codigo);
            
        }

}

