<?php

class Prontuario_AtestadoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Atestado";
	}

	public function indexAction() {
            $tbAte = new Application_Model_Atendimento();
            $tbAtest = new Application_Model_Atestado();
            $tbConf = new Application_Model_Configuracao();
            
            $dadosAtest = $tbAtest->buscar();
            // Pegando o CID inserido no atestado
            $cidCodAtest = $dadosAtest->cid_codigo;
            // Encaminhando validações de impressão e CID para a view
            if ($tbConf->getConfig("CID_OBRIGATORIO")== "1") {  $this->view->cid_obrigatorio = 1; } else { $this->view->cid_obrigatorio = 0; }
            if (count($dadosAtest)>0) { $this->view->imprimir = 1;  } else { $this->view->imprimir = 0; }
            // Pegando Dados do cid e mandando pra view
            if ($cidCodAtest) {
                $tbCid = new Application_Model_Cid();
                $this->view->cid = $tbCid->find($cidCodAtest)->current();
            }
            
            $this->view->dados = $dadosAtest;
            
            
            // Só faz a verificação se CID estiver como obrigatório
            /*if ($tbConf->getConfig("CID_OBRIGATORIO")== "1") {
               if(!$cidCodAtest){
                    $this->view->erro = "É preciso informar um CID no atendimento para emitir um atestado, caso já informou recarregue-o no icone abaixo. <a href=\"".$this->view->url(array("controller"=>"atendimento"))."\">Ir para Atendimento</a>.";
                    $this->view->imprimir = 0;
               } else {
                   $this->view->imprimir = 1;
               } 
            } else {
                if (count($this->view->dados)>0) 
                    $this->view->imprimir = 1;
            }*/ 
            
	}
        
	public function salvarAction() {
		if ($this->_request->isPost()) {
                       $padraoMotivos = array(
                            "consulta_medica" => 'S',
                            "acompanhando_filho" => 'N',
                            "retorno_trabalho" => 'N',
                            "repouso_hs" => 'N',
                            "repouso_hoje" => 'N',
                            "repouso_dia" => 'N'
			);
			$motivos = $this->_request->getPost("motivo",$padraoMotivos);
			$dados = array(
                            "acompanhando" => $this->_request->getPost("acompanhando", NULL),
                            "retornoaotrabalho" => $this->_request->getPost("retornoaotrabalho", NULL),
                            "repousohs_ini" => $this->_request->getPost("repousohs_ini", NULL),
                            "repousohs_final" => $this->_request->getPost("repousohs_final", NULL),
                            "repousodias" => $this->_request->getPost("repousodias", 0),
                            "obs" => $this->_request->getPost("obs", NULL),
                            "cid_codigo" => ($this->_request->getPost("ate_cd10_codigo") ? $this->_request->getPost("ate_cd10_codigo") : NULL)
                        );
			if ($this->_request->getPost("atest_codigo")) {
                            $dados["atest_codigo"] = $this->_request->getPost("atest_codigo");
                        }
                        $dados = array_merge($padraoMotivos,$dados,$motivos);
                        try {
				Zend_Registry::get("logger")->log($motivos, Zend_Log::INFO);
				Zend_Registry::get("logger")->log($padraoMotivos, Zend_Log::INFO);
				Zend_Registry::get("logger")->log($dados, Zend_Log::INFO);
				$tbAtest = new Application_Model_Atestado();
				try{
                                   $tbAtest->salvar($dados);
                                } catch(Exception $exc) {
                                    die($exc->getMessage());
                                }
                                $this->_redirect("/prontuario/atestado/index");
                                /*if ($cid) {
                                    $this->_redirect("/prontuario/atestado/index/imprimir/1/cid/'".$cid."'");
                                } else {
                                    $this->_redirect("/prontuario/atestado/index/imprimir/1");
                                }*/
			} catch (Zend_Validate_Exception $exc) {
				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->render("index");
			}
		} else {
			$this->_redirect("/prontuario/atestado");
		}
	}

	public function imprimirAction(){
            Zend_Layout::getMvcInstance()->setLayout("paisagem-print");
            $tbUsr = new Application_Model_Usuarios();
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "ATESTADO MÉDICO";
            $this->view->title = "Imprimir Atestado Médico";
            $this->view->atest_codigo = $this->_getParam("atest",false);
            $tbAtest = new Application_Model_Atestado();
            $this->view->dados = $tbAtest->imprimir($this->view->atest_codigo);
            //echo "<pre>".print_r($this->view->dados);die();
            $this->view->isMedico = $tbUsr->isMedico();
	}
}
