<?php

class VacinaController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Vacina";
	}

	public function indexAction() {
	}
	
	public function carteirinhaAction(){
		$this->loadCarteirinha();
	}
	
	public function dadosEstoqueAction(){
		return $this->melhoresVacina();
	}
	
	/**
	 * Esta view retorna as vacinas que foram aplicadas (a/p/z) no paciente
	 */
	public function dadosAction(){
		$usu_codigo = $this->_getParam("id",FALSE);
		if(!$usu_codigo)
			return $this->_redirect("/vacina");
		
		$tbVac = new Application_Model_VacinaUsuario();
		$this->view->dados = $tbVac->getHistorico($usu_codigo)->toArray();
		return $this->render("dados", NULL, TRUE);
	}

	public function abrirDescartarFrascoAction(){	
		$acao = $this->_getParam("acao", FALSE);
		$pro_codigo = $this->_getParam("pro", FALSE);
		if(!$pro_codigo || !$acao){
			return false;
		}
		
		$tbPro = new Application_Model_Produto();
		if($acao == "abrir")
			$tbPro->fracionar($pro_codigo);
		
		elseif($acao == "descartar"){
			$motivo = $this->_getParam("motivo", NULL);
			$tbPro->descartar($pro_codigo, $motivo);
		}
		
		return $this->melhoresVacina();
	}

	/**
	 * Trata todas os tipo de ação (A/P/Z/C)
	 */
	public function salvarAction() {
		if ($this->_request->isPost()) {
			
			$dados = array(
				"usu_codigo" => $this->_getParam("usu",false),
				"pro_codigo" => $this->_getParam("pro",false),
				"vac_acao" => $this->_getParam("acao",false),
				"vac_data" => $this->_getParam("data",false), // Y-m-d
				"vac_dose" => $this->_getParam("dose",false)
			);			

			try {
				$tbVac = new Application_Model_VacinaUsuario();
				$tbVac->salvar($dados);
				$json = array("success"=>true);
			} catch (Zend_Exception $exc) {
				$json = array(
					"success" => false,
					"mensagem" => array(
						"titulo" => "Erro",
						"mensagem" => $exc->getMessage(),
						"x" => 300,
						"y" => 200
					)
				);				
			}
			$this->view->dados = $json;
			return $this->render("dados", NULL, TRUE);
		} else {
			$this->_redirect("/vacina");
		}
	}
	
	/**
	 * Para deletar uma vacina pelo vac_usu_codigo
	 */
	public function deletarAction(){
		$vac_usu_codigo = $this->_request->getPost("vac",FALSE);
		if(!$vac_usu_codigo){
			$this->view->dados = array("success"=>false,"mensagem"=>"Informe o parâmetro \"vac\"");
			return $this->render("dados", NULL, TRUE);
		}
		
		$tbVac = new Application_Model_VacinaUsuario();
		try {
			$tbVac->deletar($vac_usu_codigo);
		} catch (Exception $exc) {
			$this->view->dados = array("success"=>false,"mensagem"=>$exc->getMessage());
			return $this->render("dados", NULL, TRUE);
		}

		$this->view->dados = array("success"=>true);
		return $this->render("dados", NULL, TRUE);		
	}
	
	public function imprimirCarteirinhaAction(){
		$tbVac = new Application_Model_VacinaUsuario();
		$tbPrint = new Application_Model_ImpressoesVia();
		$usu_codigo = $this->_getParam("usu", FALSE);
		$ate_data = $this->_getParam("ate", FALSE);
		
		if(!$usu_codigo)
			return false;
		
		$this->loadCarteirinha();
		$tbVac->regImpVia($usu_codigo);
                //die("aaaaaaa");
                $this->view->dados = $tbVac->imprimirDados($usu_codigo);
		$this->view->ocultarEstoque = true;
		$this->view->usu_codigo = $usu_codigo;
		$this->view->imp = true;

		$this->view->via = $tbPrint->getVia($usu_codigo);
                
		return $this->render("carteirinha");
	}
	
	public function imprimirAtestadoAction(){
		$this->_helper->layout->setLayout("print");
		
		$usu_codigo = $this->_getParam("usu", FALSE);
		if(!$usu_codigo)
			return false;
		
		$ate_data = $this->_getParam("ate", FALSE);
		$tbVac = new Application_Model_VacinaUsuario();
		$this->view->dados = $tbVac->imprimir($usu_codigo, $ate_data);
	}
	
	private function melhoresVacina(){
		$tbPro = new Application_Model_Produto();
		$this->view->dados = $tbPro->selecionaMelhoresVacinas();
		return $this->render("dados", NULL, TRUE);
	}

	private function loadCarteirinha(){	
		$tbCar = new Application_Model_Carteirinha();
				
		try{
			$this->view->vacinas = $tbCar->carregarCarteirinha();	
		} catch (Zend_Validate_Exception $e){
			$this->view->erro = $e->getMessage();
			return $this->render("erro", NULL, TRUE);
		}		
	}

  
}

