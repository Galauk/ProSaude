<?php

class Relatorio_Pma2Controller extends Elotech_Controller_Action_Relatorio {
	
	public function init() {
		$this->view->title = "PMA2";		
	}

	public function indexAction() {
		$tbArea = new Application_Model_Area();
		$tbPMA = new Application_Model_PMA();
		$tbUni = new Application_Model_Unidade();
		
		if($this->_request->isPost()){
			$buscar = (bool) $this->_request->getPost("buscar", TRUE);
			$area_codigo = $this->_request->getPost("area_codigo", 0);
			$uni_codigo = $this->_request->getPost("uni_codigo", 0);
			$mes_ano = $this->_request->getPost("mes_ano", FALSE);

			if(!$buscar){
				$pma_arr = $tbPMA->criar($mes_ano,$uni_codigo, $area_codigo);

				if(count($pma_arr) == 1){
					return $this->_redirect("/relatorio/pma2/editar/pma/".$pma_arr[0]);
				} 
			}
			$this->view->itens = $tbPMA->filtrar(FALSE, $mes_ano, $uni_codigo, $area_codigo);
		} else {
			$this->view->itens = $tbPMA->filtrar(); // 15 ultimos
		}
		
		$this->view->areas = $tbArea->selectTag($this->_request->getPost("area_codigo", NULL), array(0,"Todas"));
		$this->view->unidades = $tbUni->selectTag($this->_request->getPost("uni_codigo", NULL), array(0,"Todas"));
		$this->view->mes_ano = $this->_request->getPost("mes_ano", date("m/Y"));
	}

	public function pma2Action() {
		$this->view->title = "PMA2";
		
		// Coloca um layout limpo
		$this->_helper->layout->setLayout("simples");
		
		$pma_codigo = $this->_getParam("pma", FALSE);
		if(!$pma_codigo)
			return $this->_redirect ("relatorio/pma2");
		
		// Dados
		$tbPMA = new Application_Model_PMA();
		$this->view->dados = $tbPMA->carregarPma($pma_codigo);
	}
	
	public function editarAction(){		
		$this->view->pma_codigo = $this->_getParam("pma", FALSE);
		if(!$this->view->pma_codigo)
			return $this->_redirect ("/relatorio/pma2");
		
		$tbPMA = new Application_Model_PMA();
		list($this->view->dados, $this->view->calc, $this->view->info) = $tbPMA->carregarPma($this->view->pma_codigo, TRUE);
	}

	public function apagarAction(){	
			$pma_codigo = $this->view->pma_codigo = $this->_getParam("pma", FALSE);
			$tbPMARel = new Application_Model_PMARelacao();
			$tbPMA = new Application_Model_PMA();
			
			$tbPMARel->delPmaRel($pma_codigo);
			
			$tbPMA->delPma($pma_codigo);
			return $this->_redirect ("/relatorio/pma2");

	}
	
	public function salvarAction(){
		if($this->_request->isPost()){
			
			$pma_codigo = $this->_request->getPost("pma", FALSE);
			$digitado = $this->_request->getPost("d", FALSE);
			$original = $this->_request->getPost("o", FALSE);
			
			$tbPMA = new Application_Model_PMA();
			$tbPMA->editar($pma_codigo, $digitado, $original);
			
			$this->_redirect("/relatorio/pma2/pma2/pma/$pma_codigo"); // url lol
			
		} else 
			return $this->_redirect ("/relatorio/pma2");
	}
}