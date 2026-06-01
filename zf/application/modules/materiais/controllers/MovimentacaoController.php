<?php

class Materiais_MovimentacaoController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Movimentação";
        set_time_limit(100000000000);
        ini_set('memory_limit', '-1');
    }
    
    public function indexAction() {
        // action body
        // die("teste");
    }
    
    public function zeraMovimentacaoAction(){
        $this->view->title = "Remove Estoque de Produtos";
        $tbSet = new Application_Model_Setor();
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->setores = $tbSet->buscaSetorPorUsuario($usr_codigo);
        //$this->view->setores = $tbSet->selectTag(TRUE,NULL,NULL);
    }
    
    public function trataSetorAction($setor){
        if ($setor) {
            $setores = "";
            foreach($setor as $value){
                $setores .= $value.",";
            }
            return substr($setores,0,-1);
        }
    }
    
    public function zeraMovimentacaoEnviaAction(){
        $this->view->title = "Remove Estoque de Produtos";
        $setores = $this->trataSetorAction($this->_request->getPost("setor"));
        $data = $this->_request->getPost("data");
        // Inicio da transação
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            //Trazendo dados view
            $tbSet = new Application_Model_Setor();
            $tbUsr = new Application_Model_Usuarios();
            $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
            $this->view->setores = $tbSet->buscaSetorPorUsuario($usr_codigo);
            // Tabelas Utilizadas
            $tbIteMov = new Application_Model_ItensMovimento();
            $tbMov = new Application_Model_Movimento();
            $tbSal = new Application_Model_Saldo();
            $tbMvb = new Application_Model_MovimentoBkp();
            $tbIteBkp = new Application_Model_ItensMovimentoBkp();
            //Realizando backup dos dados de Movimentação
            $tbMvb->salvarPorSetor($setores,$data);
            // Realizando backups dos Itens de Movimentação
            $tbIteBkp->salvarItensPorSetor($setores,$data);
            // Desabilitando triggers para remoção dos dados
            $this->desabilitaTriggersAction();
            // Deletando os dados da tabela saldo
            $tbSal->excluiSaldoPorSetor($setores);
            // Deletando os dados da tabela de itens_movimento
            $tbIteMov->excluiItensMovimentacoesPorSetor($setores,$data);    
            // Deletando os dados da tabela de movimento
            $tbMov->excluiMovimentacaoPorSetor($setores,$data);
            // Ativando as triggers da tabela de itens_movimento
            $this->habilitaTriggersAction();
            // Executa operações realizadas
            Zend_Db_Table::getDefaultAdapter()->commit();
            $this->view->dialog = array("Confirmação","Dados removido com sucesso!",300,140);
        } catch (Exception $exc) {
            $this->view->erro = $exc->getMessage();
            Zend_Db_Table::getDefaultAdapter()->rollBack();
        }
        return $this->render("movimentacao/zera-movimentacao",NULL,TRUE);
    }
    
    public function desabilitaTriggersAction(){
        $tbIteMov = new Application_Model_ItensMovimento();
        $tbIteMov->desabilitaTrigger01();
        $tbIteMov->desabilitaTrigger02();
        $tbIteMov->desabilitaTrigger03();
        $tbIteMov->desabilitaTrigger04();
    }
    
    public function habilitaTriggersAction(){
        $tbIteMov = new Application_Model_ItensMovimento();
        $tbIteMov->habilitaTrigger01();
        $tbIteMov->habilitaTrigger02();
        $tbIteMov->habilitaTrigger03();
        $tbIteMov->habilitaTrigger04();
    }
    
    public function salvarAction(){
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
                    
        $movimento = array("mov_tipo"=>$this->_request->getPost("mov_tipo",NULL),
                           "mov_entrada" => $this->_request->getPost("mov_entrada",NULL),
                           "mov_saida" => $this->_request->getPost("mov_saida",NULL),
                           "for_codigo" => ($this->_request->getPost("for_codigo",NULL) == "0" ? "5003" : $this->_request->getPost("for_codigo",NULL)),
                           ($this->_request->getPost("mov_tipo",NULL) == "E" ? "set_entrada" : "set_saida") => $this->_request->getPost("set_codigo",NULL),
                           "mov_observacao" => $this->_request->getPost("mov_observacao",NULL),
                           "saidatrigger" => $this->_request->getPost("saida_trigger",NULL),
                           "mov_data" => 'NOW()',
                           "mov_data_inclusao"=>'NOW()',
                           "usr_codigo"=>$usr_codigo,
                           "mov_nr_nota"=>($this->_request->getPost("mov_nr_nota",NULL) == "" ? "" : $this->_request->getPost("mov_nr_nota",NULL)));
        
        if($this->_request->getPost("mov_codigo")){
            $movimento[mov_codigo] = $this->_request->getPost("mov_codigo",NULL);
        }
        
        if($this->_request->getPost("mov_tipo",NULL) == "T"){
            $movimento["set_entrada"] = $this->_request->getPost("set_codigo_destino",NULL);
            $movimento["set_saida"] = $this->_request->getPost("set_codigo",NULL);
        }

        if($this->_request->getPost("mov_tipo",NULL) == "S"){
            $movimento["set_entrada"] = $this->_request->getPost("set_codigo_destino",NULL);
            $movimento["set_saida"] = $this->_request->getPost("set_codigo",NULL);
        }

        // echo "<pre>";print_r($movimento);die();

        $tbMov = new Application_Model_Movimento();
        try{
            $mov_codigo = $tbMov->salvar($movimento);
            $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>$mov_codigo);
        }catch(Zend_Validate_Exception $exc){
            $this->view->dados = $exc->getMessage();
        }
        //$this->view->dados = $mov_codigo;
        return $this->render("dados",NULL,TRUE);
    }
    
    public function salvarItensAction(){

        $itens = $this->_request->getPost("itens");

        $tbMov = new Application_Model_Movimento();
        $tbIte = new Application_Model_ItensMovimento();
        $tbPro = new Application_Model_Produto();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();

        try{
            if(count($this->_request->getPost("itens"))){
                foreach($this->_request->getPost("itens") as $itens){
                    if(!$tbMov->ValidaData($itens[ite_validade]) && $itens[ite_validade] != ""){
                        $pro = $tbPro->getProduto($itens[pro_codigo]);
                        throw new Exception($pro->pro_nome." com validade inválida!");
                    }

                    $array_valores = $tbIte->getValorPorProdutoLote($itens[pro_codigo], $itens[ite_lote]);
                    
                    $vlr_unitario = $tbIte->recuperaVlr_unitario($itens[pro_codigo]);
                    // echo "<pre>";var_dump($vlr_unitario[0][vlr_unitario]);die();
                    $custo_medio = $this->getCustoMedio($array_valores);

                    if($custo_medio == "" || $custo_medio == NULL){
                        $custo_medio = 0;
                    }
                    
                    $array_itens = array("pro_codigo"=>$itens[pro_codigo],
                                         "ite_quantidade" => $itens[ite_quantidade],
                                         "ite_lote" => ($itens[ite_lote] == "" ? "SEM_LOTE" : $itens[ite_lote]),
                                         "ite_validade" => ($itens[ite_validade] == "" ? "" : $itens[ite_validade]),
                                         "ite_vlrunit" => ($itens[ite_vlrunid] == "" ? $vlr_unitario[0][vlr_unitario] : $itens[ite_vlrunid]),
                                         "mov_codigo" => $itens[mov_codigo],
                                         "saidatrigger" => $itens[saida_trigger],
                                         "ite_dose" => ($itens[ite_doses] == "" ? "" : $itens[ite_doses]),
                                         "pro_frmmin" => ($itens[pro_frmmin] == "undefined" ? "0" : $itens[pro_frmmin]),
                                         "ite_vlrtotal" => ($itens[ite_vlrtotal] == "" ? "0.0" : $itens[ite_vlrtotal]),
                                         "ite_custo_medio" => ($itens[ite_vlrunid] == "" || $itens[ite_vlrunid] == NULL ? $custo_medio : "0"));

                    if($itens[ite_codigo] != ""){
                        $array_itens[ite_codigo] = $itens[ite_codigo]; // se for editar
                    }

                    if($itens[fab_codigo] != "" || $itens[fab_codigo] != NULL){
                        $array_itens[fab_codigo] = $itens[fab_codigo];
                    }
                    // echo "<pre>";print_r($array_itens);die();
                    $ite_codigo = $tbIte->salvar($array_itens);
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
            $this->view->dados = array("msg"=>$exc->getMessage());
            Zend_Db_Table::getDefaultAdapter()->rollBack();
        }
        return $this->render("dados",NULL,TRUE);
    }
    
     public function getCustoMedio($array_valores){
            $valor_total_entrada = 0;
            $qtde_total_entrada = 0;
            $valor_total_saida = 0;
            $qtde_total_saida = 0;
            foreach($array_valores as $valor_geral){
                foreach($valor_geral as $valor_entrada){
                    $valor_total_entrada += $valor_entrada[t_entrada];
                    $qtde_total_entrada += $valor_entrada[qtde_entrada];
                    $valor_total_saida += $valor_entrada[t_saida];
                    $qtde_total_saida += $valor_entrada[qtde_saida];
                }
            }
            //echo $valor_total_entrada."-".$valor_total_saida. "/".$qtde_total_entrada. "-". $qtde_total_saida;
            
            $valor =  number_format($valor_total_entrada,16) - number_format($valor_total_saida,16);
            $qtde = $qtde_total_entrada - $qtde_total_saida;
            $custo_medio = $valor / $qtde;
            //die($valor. "/ ".$qtde);
            return number_format($custo_medio,16);
        }

    
    public function verificaSeMovimentouAction(){
        $ite_codigo = $this->_getParam("ite_codigo",FALSE);
        $ite_lote = $this->_getParam("ite_lote",FALSE);
        $tbIte = new Application_Model_ItensMovimento();
        $this->view->dados = $tbIte->verificaSeMovimentou($ite_lote,$ite_codigo)->movs;
        return $this->render("dados",NULL,TRUE);
    }
    
    public function imprimeEntradasAction(){
        $this->_helper->layout->setLayout("simples");
        $this->view->headLink()->setStylesheet($this->view->baseUrl().
                '/public/css/materiais/movimentacao-impressoes.css','all');
        $mov_codigo = $this->_request->getParam("mov_codigo",NULL); 
        $tbIteMov = new Application_Model_ItensMovimento();
        $tbSec = new Application_Model_Secretaria();
        $tbConf = new Application_Model_Configuracao();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr = $tbUsr->getUsrAtual();
        $this->view->secretaria  = $tbSec->getDadosSec();
        $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
        $this->view->dados = $this->dadosCabecalhoComprovante($mov_codigo,"E");
        $this->view->itens =  $tbIteMov->getProdutosPorMovimento($mov_codigo);
    }
    
    public function imprimeSaidasAction(){
        $this->_helper->layout->setLayout("simples");
        $this->view->headLink()->setStylesheet($this->view->baseUrl().
                '/public/css/materiais/movimentacao-impressoes.css','all');
        $mov_codigo = $this->_request->getParam("mov_codigo",NULL); 
        $tbIteMov = new Application_Model_ItensMovimento();
        $tbSec = new Application_Model_Secretaria();
        $tbConf = new Application_Model_Configuracao();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr = $tbUsr->getUsrAtual();
        $this->view->secretaria  = $tbSec->getDadosSec();
        $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
        $this->view->dados = $this->dadosCabecalhoComprovante($mov_codigo,"S");
        $this->view->itens =  $tbIteMov->getProdutosPorMovimento($mov_codigo);
    } 
    
    public function imprimeTransferenciasAction(){
        $this->_helper->layout->setLayout("simples");
        $this->view->headLink()->setStylesheet($this->view->baseUrl().
                '/public/css/materiais/movimentacao-impressoes.css','all');
        $mov_codigo = $this->_request->getParam("mov_codigo",NULL); 
        $tbIteMov = new Application_Model_ItensMovimento();
        $tbSec = new Application_Model_Secretaria();
        $tbConf = new Application_Model_Configuracao();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->usr = $tbUsr->getUsrAtual();
        $this->view->secretaria  = $tbSec->getDadosSec();
        $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
        $this->view->dados = $this->dadosCabecalhoComprovante($mov_codigo,"T");
        $this->view->itens =  $tbIteMov->getProdutosPorMovimento($mov_codigo);
    }

    public function dadosCabecalhoComprovante($mov_codigo,$mov_tipo){
        $tbMov = new Application_Model_Movimento();
        $dados_mov = $tbMov->getDadosMovimento($mov_codigo);
        $this->view->dados_mov_tipo = $mov_tipo;
        $this->view->dados_mov = $dados_mov->mov_codigo;
        $this->view->dados_mov_data = $dados_mov->mov_data;
        $this->view->dados_usu_nome = $dados_mov->usu_nome;
        $this->view->dados_mov_nota = 
                ($dados_mov->mov_nr_nota == "" ? "-" : $dados_mov->mov_nr_nota);
        $this->view->dados_mov_forn = 
                ($dados_mov->for_nome == "" ? "-" : $dados_mov->for_nome);

        $this->view->dados_mov_observacao = $dados_mov->mov_observacao;
        // Validações Centro Estocadores
        if ($mov_tipo=="E") {
            //die($dados_mov->setor_entrada);
            $this->view->tipo_impressao = "COMPROVANTE DE ENTRADA";
            $this->view->dados_mov_centroo = $dados_mov->setor_entrada;
        }
        if ($mov_tipo=="S") { 
            $this->view->tipo_impressao = "COMPROVANTE DE SAÍDA";
            $this->view->dados_mov_centroo = $dados_mov->setor_saida;
        }
        if ($mov_tipo=="T") { 
            $this->view->tipo_impressao = "COMPROVANTE DE TRANSFERÊNCIA";
            $this->view->dados_mov_centroo = $dados_mov->setor_saida;
            $this->view->dados_mov_centros = 
                ($dados_mov->setor_entrada != "" ? 
                    $dados_mov->setor_entrada : "-");
        }
    }
}
    
