<?php

class RaioxController extends Zend_Controller_Action {
	
	public function init() {
		$this->view->title = "Raio-x Solicitados";
                
                
	}
	public function indexAction(){
	   $tbAte = new Application_Model_Atendimento();
           $tbReq = new Application_Model_RequisicaoExame();
           $this->view->itensUsuario = $tbReq->getListaSolicitacoesManuais();
           
	}
	
	public function uploadAction(){
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/default/raiox.js');
            $usu_codigo = $this->_getParam("usu_codigo",NULL);
            $ate_codigo = $this->_getParam("ate_codigo",NULL);
            $dt_requisicao = $this->_getParam("dt_requisicao",NULL);
            $this->view->usu_codigo = $usu_codigo;
            $this->view->ate_codigo = $ate_codigo;
            $this->view->dt_requisicao = $dt_requisicao;
            $tbReq = new Application_Model_RequisicaoExame();
            if(empty($ate_codigo)){
                $this->view->procedimentos = $tbReq->getRaioxPedidos(NULL,$usu_codigo,$dt_requisicao);
            }else if($ate_codigo){
                $this->view->procedimentos = $tbReq->getRaioxPedidos($ate_codigo);
            }
            return $this->render("iframe", NULL, TRUE);
	}
        
        public function novoAction(){
            
            $usu_codigo = $this->_getParam("id",NULL);
            $interno = $this->_getParam("int",NULL);
            $med_codigo = $this->_getParam("med",NULL);
            
            if($usu_codigo){
                $tbUsu = new Application_Model_Usuario();
                $usu_info = $tbUsu->getInfo($usu_codigo);
                //echo "<pre>".print_r($usu_info,1);die();
                $this->view->usu_codigo = $usu_codigo;
                $this->view->usu_nome = $usu_info->usu_nome;
            }
            
            if($med_codigo){
                
                if($interno == 1){
                    $tbUsr = new Application_Model_Usuarios();
                    $usr_info = $tbUsr->getInfoUsr($med_codigo);
                    $this->view->usr_codigo = $usr_info->usr_codigo;
                    $this->view->usr_nome = $usr_info->usr_nome;
                }else{
                    
                    $tbMed = new Application_Model_Medico();
                    $med_info = $tbMed->getInfoMedico($med_codigo);
                    $this->view->usr_codigo = $med_info->med_codigo;
                    $this->view->usr_nome = $med_info->med_nome;
                }
                $this->view->interno = $interno;
            }
            
        }
        
        public function salvarAction() {
            if ($this->_request->isPost()) {
                    $interno = $this->_request->getPost("interno", NULL);
                    $med_codigo = $this->_request->getPost("usr_codigo_solicitante", NULL);
                    $dados = array(
                            "usu_codigo" => $this->_request->getPost("usu_codigo", NULL),
                            "proc_codigo" => $this->_request->getPost("proc_codigo", NULL),
                            "req_observacao" => $this->_request->getPost("req_observacao", NULL),
                            "req_encaminhamento"=>"t",
                            ($interno == 1 ? "usr_codigo_solicitante":"med_codigo_solicitante")=>$med_codigo
                    );
                    
                   // echo "<pre>".print_r($dados,1);die();
                    try {
                            $tbReq = new Application_Model_RequisicaoExame();
                            $rec_codigo = $tbReq->salvar($dados);
                            $this->view->dialog = array("Confirmação","Solicitação de exame registrado com sucesso!",300,140);
                            $tbProc = new Application_Model_Procedimento();
                            $this->view->procedimento = $tbProc->selectTag();
                            $usu_codigo = $this->_request->getPost("usu_codigo", NULL);
                            $this->view->dados->usu_nome = $this->_request->getPost("usu_nome", NULL);
                            $this->_redirect("default/raiox/novo/id/$usu_codigo/med/$med_codigo/int/$interno");
                            
                            
                    } catch (Zend_Validate_Exception $exc) {
                            $this->view->erro = $exc->getMessage();
                            $this->view->dados = (object) $dados;				
                            $this->render("novo");
                    }
            } else {
                    $this->_redirect("/default/raiox");
            }
	}
        
        public function itensAction(){ 
                $usu_codigo = $this->_getParam("usu_codigo",NULL); 
                if($usu_codigo){
                    $tbReq = new Application_Model_RequisicaoExame();
                    $this->view->itens = $tbReq->getItensPorUsuario($usu_codigo);
                }
	}
        
        public function excluirItemAction(){
		$id = (int) $this->_getParam("id",0);
                $usu_codigo = (int) $this->_getParam("usu_codigo",0);
		if(!$id)
			return $this->_redirect ("/default/raiox/novo/id/$usu_codigo");
		
		$tbReq = new Application_Model_RequisicaoExame();
		$tbReq->excluir($id);
                return $this->_redirect ("/default/raiox/novo/id/$usu_codigo");
	}
        
        public function pesquisaAction() {
		if ($this->_request->isPost()) {
			$this->view->busca = $this->_request->getPost("busca");
			$tbRec = new Application_Model_RequisicaoExame();
			$this->view->itensUsuario = $tbRec->pesquisar($this->view->busca);
			$this->render("index");
		} else {
			$this->_redirect("raiox/index");
		}
	}
        
        public function verAction(){
            Zend_Layout::getMvcInstance()->disableLayout();
            $req_codigo = $this->_getParam("id",NULL);
            $diretorios = array("thumbnail","small");
            $tbUpl = new Application_Model_UploadArquivo();
            $tbUpl->limpaDir($diretorios);
            $arquivos = $tbUpl->extrairArquivosBanco($req_codigo);
            $tbUpl->geraThumbs($arquivos,100,67,"thumbnail");
            $tbUpl->geraThumbs($arquivos,411,274,"small");
            $lista = $tbUpl->getArquivosPorRequisicao($req_codigo);
            //echo "<pre>".print_r($lista,1)."<br/>";die();
            $this->view->arquivos = $lista;
        }
        
        
        
}
?>
