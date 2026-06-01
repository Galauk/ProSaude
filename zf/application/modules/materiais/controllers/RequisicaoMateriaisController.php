<?php

class Materiais_RequisicaoMateriaisController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Requisição de Materiais";
    }
    public function indexAction() {
        $tbReq = new Application_Model_RequisicaoMateriais();
        $this->view->itens = $tbReq->getRequisicoes(15)->toArray();
        
    }
    
    public function confirmAction(){
        $tbSet = new Application_Model_Setor();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;
        
        $rem_codigo = $this->_getParam("id",FALSE);
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
    
    public function listaLotesPorRequisicaoAction(){
        $codRequisicaoItens = $this->_request->getPost("codRequisicaoItens");
        $tbRemil = new Application_Model_RequisicaoMateriaisItensLote();
        $this->view->dados = $tbRemil->listaLotesPorRequisicao($codRequisicaoItens)->toArray();
        $this->render("dados",NULL,TRUE);
    }
    
    public function atualizaStatusItemRequisicaoAction(){
        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $codRequisicaoItens = $this->_request->getPost("codRequisicaoItens");
        // Busca Status Atual para inverter 
        $statusAtualReq = $tbRemi->getStatusItemRequisicao($codRequisicaoItens)->remi_status;
        // Alterando status quando clica na transferência
        if ($statusAtualReq == "E") { $statusReq = "C"; } else { $statusReq = "E"; }
        $dados = array(
          "remi_codigo" => $codRequisicaoItens,
          "remi_status" => $statusReq  
        );
        $tbRemi->atualizaStatusItemRequisicao($dados);
        $this->view->dados = $statusReq;
        $this->render("dados",NULL,TRUE);
    }
    
    public function confirmaRequisicaoAction(){
        $tbRem = new Application_Model_RequisicaoMateriais();
        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $tbMov = new Application_Model_Movimento();
        $tbIteMov = new Application_Model_ItensMovimento();
        // Buscando dados da requisição
        $codRequisicao = $this->_getParam("id",FALSE);
        $dadosRequisicao = $tbRem->getRequisicao($codRequisicao);
        $dadosIteRequisicao = $tbRem->getDadosRequisicao($codRequisicao);
        if (count($dadosIteRequisicao)>0) {
            // Buscando dados dos itens da requisição para inserção na itens
            // Salva a movimentação
            $dadosMov = array(
                "mov_tipo"=> "E",
                "set_entrada"=> $dadosRequisicao->set_codigo_req,
                "set_saida"=> $dadosRequisicao->set_codigo_sol,
                "mov_observacao" => $dadosRequisicao->rem_observacao,
                "mov_data" => 'NOW()',
                "mov_data_inclusao"=>'NOW()',
                "usr_codigo"=>$dadosRequisicao->usr_codigo
            );
            // Executando controle de transação
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try {
                // Salvando movimentação
                $codMov = $tbMov->salvarMovimentacaoRequisicao($dadosMov);
                // Salvando itens movimento
                foreach($dadosIteRequisicao as $iteMov){
                    $dadosIteMov = array(
                        "pro_codigo"=> $iteMov->pro_codigo,
                        "ite_quantidade" => $iteMov->remil_quantidade,
                        "ite_lote" => $iteMov->remil_lote,
                        "ite_validade" => $iteMov->sal_validade,
                        "mov_codigo" => $codMov,
                        "ite_dose" => $iteMov->sal_dose_lote
                    );
                    $dadosAtuIteReq = array(
                        "remi_codigo" => $iteMov->remi_codigo,
                        "remi_status" => "F"
                    );
                    $tbIteMov->salvarItensMovimentacaoRequisicao($dadosIteMov);
                    $tbRemi->atualizaStatusItemRequisicao($dadosAtuIteReq);
                }
                // Atualizando status da requisição para concluída
                $dadosAtuRequisicao = array(
                    "rem_codigo" => $codRequisicao,
                    "rem_status" => "F" 
                );
                $tbRem->atualizaStatusRequisicao($dadosAtuRequisicao);
                // Realizando a inserção dos de dados, se não deu nenhum problema
                Zend_Db_Table::getDefaultAdapter()->commit();
                return $this->_redirect("materiais/requisicao-materiais/index/id/$codRequisicao");
            } catch (Exception $exc) {
                Zend_Db_Table::getDefaultAdapter()->roolBack();
                $this->view->dados = $exc->getMessage();
            }
        } else {
            // Atualizando status da requisição para concluída
            $dadosAtuRequisicao = array(
                "rem_codigo" => $codRequisicao,
                "rem_status" => "F" 
            );
            $tbRem->atualizaStatusRequisicao($dadosAtuRequisicao);
            return $this->_redirect("materiais/requisicao-materiais/index/id/$codRequisicao");
        }
        
    }
    
    public function formAction(){
        $tbSet = new Application_Model_Setor();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr_nome = $tbUsr->getUsrAtual()->usr_nome;
        
        $rem_codigo = $this->_getParam("id",FALSE);
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
        return $this->_redirect("materiais/requisicao-materiais/form/id/$rem_codigo");
    }
    
    public function buscarAction(){
        if ($this->_request->isPost()) {
            $tbReq = new Application_Model_RequisicaoMateriais();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->rem_status = $this->_request->getPost("rem_status");
            $this->view->itens = $tbReq->getRequisicoes(NULL,$this->view->busca,$this->view->rem_status);
            $this->render("index");
        } else {
             $this->_redirect("/materiais/controle-movimentos/index");
        }
    }
    
    public function salvarItensAction(){
        
        $tbIte = new Application_Model_RequisicaoMateriaisItens();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            if(count($this->_request->getPost("itens"))){
                foreach($this->_request->getPost("itens") as $itens){
                    $array_itens = array("pro_codigo"=>$itens[pro_codigo],
                                         "remi_quantidade" => $itens[remi_quantidade],
                                         "rem_codigo" => $itens[rem_codigo],
                                         "remi_status" => $itens[remi_status]);
                    if($itens[remi_codigo] != ""){
                        $array_itens[remi_codigo] = $itens[remi_codigo]; // se for editar
                    }
                    $remi_codigo = $tbIte->salvar($array_itens);
                }
            }
            
            if(count($this->_request->getPost("itens_deletar_banco"))){
                
                foreach($this->_request->getPost("itens_deletar_banco") as $itens_remover){
                    $tbIte->deletar($itens_remover);
                }
            }
            
            $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>1);
            Zend_Db_Table::getDefaultAdapter()->commit();
        }catch (Exception $exc){
            $this->view->dados = $exc->getMessage();
            Zend_Db_Table::getDefaultAdapter()->rollBack();
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    public function imprimirAction(){
        //Zend_Layout::getMvcInstance()->setLayout("print");
        $this->_helper->layout->setLayout("modelo-print");
        $tbUsr = new Application_Model_Usuarios();

        $this->view->title = "Requisição Materiais";

        $rem_codigo = $this->_getParam("rem_codigo",FALSE);

        $tbRemi = new Application_Model_RequisicaoMateriaisItens();
        $tbRem = new Application_Model_RequisicaoMateriais();
        $this->view->dados_req = $tbRem->getRequisicao($rem_codigo);
        $this->view->itens = $tbRemi->getItensPorRequisicao($rem_codigo);
        $this->view->dados_gerais = "<b>Setor de Requisição: </b>".$this->view->dados_req->set_nome_req;
        $this->view->dados_gerais_sec = "<b>Setor de Solicitação: </b>".$this->view->dados_req->set_nome_sol;
        $this->view->dados_complementares = "<b>Solicitante: </b>".$this->view->dados_req->usr_nome;
        $this->view->dados_complementares_sec = "<b>Data da Requisição:</b>".$this->view->dados_req->rem_data;
        
    }
    
}



