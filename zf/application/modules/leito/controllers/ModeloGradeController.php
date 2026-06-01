<?php

class Leito_ModeloGradeController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Modelos para Dispensação de Medicamentos";
	}

	public function indexAction() {
            $this->_redirect("/leito/modelo-grade/categorias");
	}

	public function categoriasAction() {
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/public/css/ui.jqgrid.4.3.1.css', 'all');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/i18n/grid.locale-pt-br.4.3.1.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.jqGrid.min.4.3.1.js');

		$tbLGM = new Application_Model_LeitoGradeModelo();
		$this->view->itens = $tbLGM->getCategorias();
	}
        
        public function cadastrarAction(){
                $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/public/css/ui.jqgrid.4.3.1.css', 'all');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/i18n/grid.locale-pt-br.4.3.1.js');
		$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.jqGrid.min.4.3.1.js');

		$tbLGM = new Application_Model_LeitoGradeModelo();
		$this->view->itens = $tbLGM->getCategorias();
        }

	/**
	 * Recebe o ID da categoria e retorna os modelos dessa categoria
	 * @return type 
	 */
	public function modelosAction() {
		$this->_helper->layout->disableLayout();
		$lgc_codigo = $this->_getParam("categoria", FALSE);
		if (!$lgc_codigo)
			return false;

		$tbLGM = new Application_Model_LeitoGradeModelo();
		$this->view->categoria = $tbLGM->getNomeCategoria($lgc_codigo);
		$this->view->itens = $tbLGM->getModelos($lgc_codigo);
	}

	/**
	 * Recebe o id do modelo e retorna um json com os produtos/qtd
	 */
	public function modeloAction() {
		$lgm_codigo = $this->_getParam("lgm", FALSE);
		if (!$lgm_codigo)
			return false;

		$tbLGM = new Application_Model_LeitoGradeModelo();
		$this->view->dados = $tbLGM->getModelo($lgm_codigo);
		return $this->render("dados", NULL, TRUE);
	}

	public function jqgridAction() {
		$categoria = $this->_getParam("categoria", FALSE);
		$modelo = $this->_getParam("modelo", FALSE);

		$page = $this->_getParam("page", 1);
		$limit = $this->_getParam("rows");
		$sidx = $this->_getParam("sidx", "id");
		$sord = $this->_getParam("sord", "ASC");

		if ($categoria) {
			$tbLGM = new Application_Model_LeitoGradeModelo();
			$tbLGM->setFields(array("lgm_codigo", "lgm_descricao", "lgm_intervalo", "lgm_repeticoes"));
			$this->view->dados = $tbLGM->getGridResource($page, $limit, $sidx, $sord, "lgc_codigo=" . $categoria);
		} else {
			$tbLGMI = new Application_Model_LeitoGradeModeloItens();
			$this->view->dados = $tbLGMI->getGridResource($page, $limit, $sidx, $sord, $modelo);
		}
		return $this->render("dados", NULL, TRUE);
	}

	public function salvarCategoriaAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$oper = $this->_request->getPost("oper", FALSE);

		// Código da categoria: GET
		$lgc_codigo = $this->_getParam("id", FALSE);
                
		if (!$lgc_codigo || !$oper) {
			return FALSE;
		}

		$tbLGM = new Application_Model_LeitoGradeModelo();
		$dados = array(
			"lgm_codigo" => $this->_request->getPost("id", FALSE), // Código do modelo: POST
			"lgm_descricao" => $this->_request->getPost("lgm_descricao", FALSE),
			"lgm_intervalo" => $this->_request->getPost("lgm_intervalo", FALSE),
			"lgm_repeticoes" => $this->_request->getPost("lgm_repeticoes", FALSE),
			"lgc_codigo" => $lgc_codigo,
		);
		try {
			$tbLGM->salvar($dados);
		} catch (Exception $exc) {
			$this->getResponse()->setHttpResponseCode(500);
			$this->view->dados = array("error" => $exc->getMessage());
			$this->render("dados", NULL, TRUE);
		}
	}
        

	public function salvarModeloAction() {
		$tbLGMI = new Application_Model_LeitoGradeModeloItens();
		
		$ligm_codigo = $this->_request->getPost("ligm_codigo", FALSE);
		
		$acao = $this->_request->getPost("acao", FALSE);
		if($acao && $acao == "excluir" && $ligm_codigo){
			$tbLGMI->delete("ligm_codigo=".$ligm_codigo);
			return $this->render("dados", NULL, TRUE);
		}
		
		$dados = array(
			"ligm_codigo" => $ligm_codigo,
			"lgm_codigo" => $this->_request->getPost("lgm_codigo", FALSE),
			"pro_codigo" => $this->_request->getPost("pro_codigo", FALSE),
			"ligm_quantidade" => $this->_request->getPost("ligm_quantidade", FALSE)
		);

		try {
			$tbLGMI->salvar($dados);
			$this->view->dados = array();
		} catch (Exception $exc) {
			$this->view->dados = array("erro" => $exc->getMessage());
		}
		$this->render("dados", NULL, TRUE);
	}

}

