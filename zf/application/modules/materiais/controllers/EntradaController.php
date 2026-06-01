<?php

class Materiais_EntradaController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Entrada";
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/materiais/movimentacao.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/materiais/movimentacao.css','all');
    }
    public function indexAction() {
        // action body
        $aux1 = $_SESSION[logon];
        $aux2 = $aux1[usr];
        // die(var_dump($aux2->set_codigo));
        $tbFor = new Application_Model_Fornecedor();
        $tbSet = new Application_Model_Setor();
        $tbConf = new Application_Model_Configuracao();
        
        $mov_codigo = $this->_getParam("id",FALSE);
        if($mov_codigo){
            $tbMov = new Application_Model_Movimento();
            $movimento = $tbMov->getMovimento($mov_codigo)->toArray();
            $this->view->itens = $movimento;
            $tbIte = $tbIte = new Application_Model_ItensMovimento();
            $itens_movimento = $tbIte->getProdutosPorMovimento($mov_codigo)->toArray();
            $this->view->itens_movimento = $itens_movimento;
        }

        $this->view->config_imp = $tbConf->getDadosConfigPelaChave
                ("IMPRIMIR_COMPROVANTES_MOVIMENTACAO")->conf_valor_bool;
        $this->view->fornecedores = $tbFor->selectTag(NULL,$movimento[for_codigo]);
        //$this->view->setores = $tbSet->selectTag(TRUE,NULL,$movimento[set_entrada]);
        //die(var_dump($tbSet->getInfoSetor($aux2->set_codigo)));
        if($aux2->set_codigo != null && $aux2->set_codigo != ""){
            $this->view->setor = $tbSet->getInfoSetor($aux2->set_codigo);
        } else {
            $dados = array();
            $dados[set_codigo] = 0;
            $dados[set_nome] = "Unidade sem setor vinculado.";
            $this->view->setor = $dados;
        }
        
        
    }
    
    
}

