<?php

class Materiais_EnvioDeMateriaisController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Envio de Materiais";
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/materiais/movimentacao.css','all');
    }
    public function indexAction() {
    // action body
        $tbReq = new Application_Model_RequisicaoMateriais();
        $this->view->itens = $tbReq->getRequisicoesPorEnvio(15);
        
        
    
    }
    
    public function formAction(){
        $tbSet = new Application_Model_Setor();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;
        
        $rem_codigo = $this->_getParam("id",FALSE);
        $this->view->rem_codigo = $rem_codigo;
        if($rem_codigo){
            $tbRem = new Application_Model_RequisicaoMateriais();
            $requisicao = $tbRem->getRequisicao($rem_codigo)->toArray();
            $this->view->itens = $requisicao;
            $tbRemi = $tbRemi = new Application_Model_RequisicaoMateriaisItens();
            $itens_requisicao = $tbRemi->getProdutosRequisicao($rem_codigo)->toArray();
            $this->view->itens_requisicao = $itens_requisicao;
        }
        
        $this->view->setor_origem =  $tbSet->selectTag(TRUE,"set_codigo_req",$requisicao[set_codigo_req]);
        $this->view->setores = $tbSet->selectTag(FALSE,"set_codigo_sol",$requisicao[set_codigo_sol]);
    }
    
    public function salvarAction(){
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
                    
        $requisicao = array("set_codigo_req"=>$this->_request->getPost("set_codigo_req",NULL),
                           "set_codigo_sol" => $this->_request->getPost("set_codigo_sol",NULL),
                           "rem_observacao" => ($this->_request->getPost("rem_observacao",NULL) == "0" ? "5003" : $this->_request->getPost("for_codigo",NULL)),
                           "rem_data" => 'NOW()',
                           "usr_codigo"=>$usr_codigo,
                           "rem_status"=>$this->_request->getPost("rem_status",NULL));
        
        /*EDITARRR*/
        /*if($this->_request->getPost("rem_codigo")){
            $movimento[mov_codigo] = $this->_request->getPost("rem_codigo",NULL);
        }*/
        
        
        $tbReq = new Application_Model_RequisicaoMateriais();
        try{
            $rem_codigo = $tbReq->salvar($requisicao);
            $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>$rem_codigo);
        }catch(Zend_Validate_Exception $exc){
            $this->view->dados = $exc->getMessage();
        }
        //$this->view->dados = $mov_codigo;
        return $this->render("dados",NULL,TRUE);
    }
   
    public function editarAction(){
        $rem_codigo = $this->_getParam("id",FALSE);
        return $this->_redirect("materiais/envio-de-materiais/form/id/$rem_codigo");
    }
    
    public function buscarAction(){
        if ($this->_request->isPost()) {
            $tbReq = new Application_Model_RequisicaoMateriais();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->rem_status = $this->_request->getPost("rem_status");
            $this->view->itens = $tbReq->getRequisicoesPorEnvio(NULL,$this->view->busca,$this->view->rem_status);
            $this->render("index");
        } else {
             $this->_redirect("/materiais/controle-movimentos/index");
        }
    }
    
    public function salvarItensAction(){
        
        $tbIteLo = new Application_Model_RequisicaoMateriaisItensLote();
        $tbIte = new Application_Model_RequisicaoMateriaisItens();
        $tbRem = new Application_Model_RequisicaoMateriais();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            $array_rem = array("rem_codigo" => $this->_request->getPost("rem_codigo"),
                               "rem_status" => "E");
            
            $tbRem->salvar($array_rem);
            if(count($this->_request->getPost("itens"))){
                foreach($this->_request->getPost("itens") as $itens){
                    $array_lotes = explode("-", $itens[remil_lote]);
                    
                    $array_itens_lote = array("remi_codigo"=>$itens[remi_codigo],
                                         "remil_quantidade" => $itens[remil_quantidade],
                                         "remil_lote" => $array_lotes[0],
                                         "remil_validade"=>$array_lotes[2]);
                    
                    $tbIteLo->salvar($array_itens_lote);
                    
                    $array_itens = array("remi_codigo"=>$itens[remi_codigo],
                                         "remi_status"=>"E");
                    $tbIte->salvar($array_itens);
                   
                    $this->geraSaidaAction($this->_request->getPost("rem_codigo"),$array_itens_lote);
                    
                    
                }
            }
            /*if(count($this->_request->getPost("itens_deletar_banco"))){
                
                foreach($this->_request->getPost("itens_deletar_banco") as $itens_remover){
                    $tbIteLo->deletar($itens_remover);
                }
            }*/
            $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>1);
            Zend_Db_Table::getDefaultAdapter()->commit();
        }catch (Exception $exc){
            $this->view->dados = $exc->getMessage();
            Zend_Db_Table::getDefaultAdapter()->rollBack();
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    public function geraSaidaAction($codRequisicao,$array_itens){
        
        $tbRem = new Application_Model_RequisicaoMateriais();
        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $tbMov = new Application_Model_Movimento();
        $tbIteMov = new Application_Model_ItensMovimento();
        $dadosRequisicao = $tbRem->getRequisicao($codRequisicao);
        $pro_codigo = $tbRemi->getItem($array_itens[remi_codigo])->pro_codigo;
       
        $dadosMov = array(
            "mov_tipo"=> "S",
            "mov_saida"=> "S-TR",
            "set_saida"=> $dadosRequisicao->set_codigo_sol,
            "mov_observacao" => $dadosRequisicao->rem_observacao,
            "mov_data" => 'NOW()',
            "mov_data_inclusao"=>'NOW()',
            "usr_codigo"=>$dadosRequisicao->usr_codigo
        );
        if(count($array_itens) > 0){
            try {
                $codMov = $tbMov->salvarMovimentacaoRequisicao($dadosMov);
                
                $dadosIteMov = array(
                    "pro_codigo"=> $pro_codigo,
                    "ite_quantidade" => $array_itens[remil_quantidade],
                    "ite_lote" => $array_itens[remil_lote],
                    "ite_validade" => $array_itens[remil_validade],
                    "mov_codigo" => $codMov
                );
                
                $dadosAtuIteReq = array(
                    "remi_codigo" => $array_itens->remi_codigo,
                    "remi_status" => "F"
                );

                $tbIteMov->salvarItensMovimentacaoRequisicao($dadosIteMov);
                $tbRemi->atualizaStatusItemRequisicao($dadosAtuIteReq);
                // Atualizando status da requisição para concluída
                $dadosAtuRequisicao = array(
                    "rem_codigo" => $codRequisicao,
                    "rem_status" => "E" 
                );
                $tbRem->atualizaStatusRequisicao($dadosAtuRequisicao);

                return true;
            } catch (Exception $exc) {

                throw new Zend_Validate_Exception($exc->getMessage());
                return false;
            }
        }else{
            throw new Zend_Validate_Exception("não encontrou itens");
        }
    }
    
    public function imprimirAction(){
        //Zend_Layout::getMvcInstance()->setLayout("print");
        $this->_helper->layout->setLayout("modelo-print");
        $tbUsr = new Application_Model_Usuarios();

        $this->view->title = "Requisição Materiais";

        $rem_codigo = $this->_getParam("rem_codigo",FALSE);
        $rem_status = $this->_getParam("rem_status",FALSE);

        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $tbRem = new Application_Model_RequisicaoMateriais();
        $this->view->dados_req = $tbRem->getRequisicao($rem_codigo);
        
        if($rem_status == "F" || $rem_status == "E"){
            $this->view->itens_lote = 1;
        }
        
        $this->view->itens = $tbRemi->getItensPorRequisicao($rem_codigo);
        $this->view->dados_gerais = "<b>Setor de Requisição: </b>".$this->view->dados_req->set_nome_req;
        $this->view->dados_gerais_sec = "<b>Setor de Solicitação: </b>".$this->view->dados_req->set_nome_sol;
        $this->view->dados_complementares = "<b>Solicitante: </b>".$this->view->dados_req->usr_nome;
        $this->view->dados_complementares_sec = "<b>Data da Requisição:</b>".$this->view->dados_req->rem_data;
        
    }
    
    public function cancelarItemAction(){
        $remi_codigo = $this->_getParam("remi_codigo",FALSE);
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        
        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $array_produto = array("remi_codigo"=>$remi_codigo,
                               "remi_status"=>"N");
        try{
            $tbRemi->cancelarItem($array_produto);
            $this->view->dados = $remi_codigo;
        }  catch (Exception $exe){
            
        }
        
        return $this->render("dados",NULL,TRUE);
    }
}



