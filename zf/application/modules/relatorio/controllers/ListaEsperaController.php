<?php

class Relatorio_ListaEsperaController extends Elotech_Controller_Action_Relatorio {

	public function init() {

	}

	public function indexAction(){
    $this->view->title = "Relatório Lista de Espera";
	}

  public function imprimirAction(){
    $tipo = $this->_getParam("tipo", FALSE);
    $tbConf = new Application_Model_Configuracao();
    $tbSec = new Application_Model_ListaEspera();
    $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
    $this->view->dados = $tbSec->getDadosLista($tipo);
    $this->view->tipo = $tipo;

  }
}

?>
