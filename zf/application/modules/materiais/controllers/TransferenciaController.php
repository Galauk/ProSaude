<?php

class Materiais_TransferenciaController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Transferência";
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/materiais/movimentacao.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/materiais/movimentacao.css','all');
    }
    public function indexAction() {
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
        $this->view->setor_origem =  $tbSet->selectTag(TRUE,NULL,$movimento[set_saida],"T");
        $this->view->setores = $tbSet->selectTag(FALSE,"set_codigo_destino",$movimento[set_entrada],"T");
    }
    
}

