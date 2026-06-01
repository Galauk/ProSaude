<?php

class Prontuario_ExameController extends Zend_Controller_Action {

	public function init() {
            $this->_helper->acl->copiarPermissao("zf/prontuario/index");
            $this->view->title = "Exames";     
	}
	
	public function indexAction(){
            $tbAte = new Application_Model_Atendimento();
            $obs = $this->_getParam("obs", FALSE);
            $this->_helper->layout->setLayout("prontuario");
            $tbConfig = new Application_Model_Configuracao();
            $this->view->config = $tbConfig->getConfig("ENCAMINHAMENTO_RAIOX");
            $this->view->obs = $this->_getParam("obs", FALSE);
            $this->view->ate_codigo = $this->_getParam("ate_codigo", FALSE);
            $this->view->io_codigo = $this->_getParam("io_codigo", FALSE);
            $this->view->cod = $this->_getParam("cod", FALSE);
            $this->view->imprimi = $this->_getParam("imprimi", FALSE);
            $this->view->usu_codigo =  $this->_getParam("usu_codigo", FALSE);

            if($this->view->obs){
                    $this->_helper->layout->disableLayout();
            }
            if(empty($this->view->ate_codigo)){
                $this->view->ate_codigo = $tbAte->temAtendimento($_SESSION[prontuario][age]->age_codigo)->ate_codigo;
            }

            $tbGP = new Application_Model_GrupoProcedimento();
      //  die("asdfasdf");
            $this->view->g_select = $tbGP->getGrupo();
	}
	
	/**
	 * Mostra os exames do atendimento atual
	 * Atenção: exame solicitado pelo médico, mas não quer dizer que foi realizado, nem mesmo agendado
	 */
	public function itensAction(){                	
            $obs = $this->_getParam('obs',NULL);
            $tbReq = new Application_Model_RequisicaoExame();
            $this->view->itens = $tbReq->getItens();
            $this->view->obs = $obs;
	}
	/**
	 * Mostra os exames da internação atual
	 * Atenção: exame solicitado pelo médico, mas não quer dizer que foi realizado, nem mesmo agendado
	 */
	
	public function itensInternacaoAction(){
            $obs = $this->_getParam('obs',NULL);
            $io_codigo = $this->_getParam("io_codigo",false);
            $ate_codigo = $this->_getParam("ate_codigo",false);
            $usu_codigo = $this->_getParam("usu_codigo",false);
            $imprimi = $this->_getParam("imprimi",false);

            $tbReq = new Application_Model_RequisicaoExame();
            $this->view->itens = $tbReq->getItensInternacao($io_codigo);
            $this->view->obs = $obs;
            $this->view->ate_codigo = $ate_codigo;
            $this->view->usu_codigo = $usu_codigo;
            $this->view->imprimi = $imprimi;
            $this->render("itens");
	}

    public function grupoProcedimentoAction(){
        $gp_codigo = $this->_request->getPost("gp_codigo", FALSE);
        $obs = $this->_getParam("obs",FALSE);
        $ate_codigo = $this->_getParam("ate_codigo",FALSE);
        $io_codigo = $this->_getParam("io_codigo",FALSE);
        $tbGP = new Application_Model_GrupoProcedimento();
        $procs = $tbGP->getGrupoProcedimento($gp_codigo);
        $grupo = $tbGP->getGrupo($gp_codigo);
        foreach ($procs as $key => $proc) {
            $dados = array(
                "ate_codigo" => $ate_codigo,
                "proc_codigo" => $proc->proc_codigo,
                "req_observacao" => ("Exame pertencente ao grupo " . $grupo[0]->gp_descricao)
            );
            if($this->_request->getPost("usu_codigo")){
                    $dados[usu_codigo] = $this->_request->getPost("usu_codigo");
                }

            try {
                $tbReq = new Application_Model_RequisicaoExame();
                if($dados[req_encaminhamento] == ""){
                    $dados[req_encaminhamento] = 'f';
                }
                $tbReq->salvar($dados);
            } catch (Zend_Validate_Exception $exc) {
                $this->view->erro = $exc->getMessage();
                $this->view->dados = (object) $dados;
                if($obs == "S"){
                    $this->render("index");
                }else{
                    die($exc->getMessage());
                }
            }
        }

        $this->view->dialog = array("Confirmação","Solicitação de exame registrado com sucesso!",300,140);
        $tbProc = new Application_Model_Procedimento();
        $this->view->procedimento = $tbProc->selectTag();
        if($obs != "S"){
            $this->_redirect("prontuario/exame");
        }else{
            $this->_redirect("/prontuario/exame/index/obs/S/cod/$io_codigo/io_codigo/".$io_codigo."/ate_codigo/".$ate_codigo."/usu_codigo/".$this->_request->getPost("usu_codigo")."/imprimir/S");
        }


    }
	/**
	 * Mostra uma lista com o histórico de todos os exames solicitados pelo(s) médico(s)
	 * Se for informado o id será filtrado por atendimento
	 * Atenção: exame solicitado pelo médico, mas não quer dizer que foi realizado, nem mesmo agendado
	 */
	public function historicoAction(){
            $ate_codigo = $this->_getParam("id", NULL);

            $tbReq = new Application_Model_RequisicaoExame();
            $this->view->itens = $tbReq->getHistorico($ate_codigo);	
	}

	/**
	 * Mostra uma lista de exame (da tabela cadastroDoExame) que foram coletados/concluidos
	 */
	public function coletadosAction(){
            $tbCad = new Application_Model_CadastroDoExame();
            $this->view->itens = $tbCad->getListaColetados();
	}
	
	/**
	 * Mostra o resultado do exame (itx_codigo)
	 */
	public function verAction(){
            Zend_Layout::getMvcInstance()->disableLayout();

            $agei_codigo = $this->_getParam("id",FALSE);
            if(!$agei_codigo)
                return FALSE;	

            $tbRes = new Application_Model_ResultadoExame();		
            $this->view->itens = $tbRes->getResultados($agei_codigo);
            $ate_codigo = $this->_getParam("ate_codigo",FALSE);
            $io_codigo = $this->_getParam("io_codigo",FALSE);
	}

	public function salvarAction() {
            $obs = $this->_getParam("obs",FALSE);
            $ate_codigo = $this->_getParam("ate_codigo",FALSE);
            $io_codigo = $this->_getParam("io_codigo",FALSE);
            if ($this->_request->isPost()) {			
                $dados = array(
                    "ate_codigo" => ($ate_codigo ? $ate_codigo : $this->_request->getPost("ate_codigo", NULL)),
                    "proc_codigo" => $this->_request->getPost("proc_codigo", NULL),
                    "req_observacao" => $this->_request->getPost("req_observacao", NULL),
                    "req_encaminhamento"=>$this->_request->getPost("req_encaminhamento",NULL),
                    "proc_solicitado"=>$this->_request->getPost("proc_solicitado",NULL),
                    "proc_avaliado"=>$this->_request->getPost("proc_avaliado",NULL)
                );

                if($this->_request->getPost("usu_codigo")){
                    $dados[usu_codigo] = $this->_request->getPost("usu_codigo");
                }

                try {
                    $tbReq = new Application_Model_RequisicaoExame();
                    if($dados[req_encaminhamento] == ""){
                        $dados[req_encaminhamento] = 'f';
                    }
                    $tbReq->salvar($dados);

                    $this->view->dialog = array("Confirmação","Solicitação de exame registrado com sucesso!",300,140);
                    $tbProc = new Application_Model_Procedimento();
                    $this->view->procedimento = $tbProc->selectTag();
                    if($obs != "S"){
                        $this->_redirect("prontuario/exame");
                    }else{
                        $this->_redirect("/prontuario/exame/index/obs/S/cod/$io_codigo/io_codigo/".$io_codigo."/ate_codigo/".$ate_codigo."/usu_codigo/".$this->_request->getPost("usu_codigo")."/imprimir/S");
                    }
                } catch (Zend_Validate_Exception $exc) {
                    $this->view->erro = $exc->getMessage();
                    $this->view->dados = (object) $dados;
                    if($obs == "S"){
                        $this->render("index");
                    }else{
                        die($exc->getMessage());
                    }
                }
            } else {
                $this->_redirect("/prontuario/exame");
            }
	}

	
	public function excluirAction(){
            $id = (int) $this->_getParam("id",0);
            $ate_codigo = $this->_getParam("ate_codigo",FALSE);
            $io_codigo = $this->_getParam("cod",FALSE);
            if(!$id){
                return $this->_redirect ("/prontuario/exame");
            }

            $tbReq = new Application_Model_RequisicaoExame();
            $tbReq->excluir($id);
            
            if($io_codigo){
               return $this->_redirect ("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
            }else{
              return $this->_redirect ("/prontuario/exame");
            }	
	}
	
	public function imprimirAction(){
            Zend_Layout::getMvcInstance()->setLayout("simples");
            $this->view->title = "Imprimir Requisição de Exame";

            $io_codigo = $this->_getParam("cod",FALSE);
            $usu_codigo = $this->_getParam("usu_codigo",FALSE);
            $ate_codigo = $this->_getParam("ate_codigo",FALSE);
            $selecionados = $this->_getParam("selecionados",FALSE);
            $seg = $this->_getParam("seg",FALSE);
            $this->view->seg = $seg;

            if($selecionados)
                $selecionados = explode(",",$selecionados);
            
            if(empty($usu_codigo))
                $usu_codigo = $_SESSION[prontuario][age]->usu_codigo;

            $tbSec = new Application_Model_Secretaria();
            $tbReq = new Application_Model_RequisicaoExame();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $tbUsu = new Application_Model_Usuario();
            $tbRac = new Application_Model_Raca();
            $this->view->raca = $tbRac->fetchAll();
            $this->view->dados_usr = $tbUsr->getUsrAtual();
            $this->view->pessoa = $tbUsu->listaDadosUsuario($usu_codigo);
            $this->view->sec  = $tbSec->getDadosSec()->toArray();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "PEDIDO DE EXAMES";
            $this->view->dados = $tbReq->imprimir($selecionados,$io_codigo,$usu_codigo,$ate_codigo);

	}

	public function imprimirSelecionadosAction(){
            $this->_helper->layout->setLayout("prontuario");
            $tbUsr = new Application_Model_Usuarios();
            $this->view->selecionados = $this->_getParam("imprimir",array());
            $this->view->isMedico = $tbUsr->isMedico();
            return $this->render("index");
	}
}

