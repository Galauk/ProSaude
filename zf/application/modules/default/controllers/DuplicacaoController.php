<?php

class DuplicacaoController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Controle de Duplicações";
	}

	public function indexAction() {
		// lista as duplicações suportadas
	}
	
	public function ruaAction(){
            $this->view->title .= " - Logradouro";
            if ($this->_request->isPost()) {
                $correto = $this->_request->getPost("rua_codigo", FALSE);
                $duplicados = $this->_request->getPost("rua_codigo_duplicado", FALSE);
                if($correto == "" && count($duplicados)==0){
                    return $this->view->dialog = array("Falha!", "Selecione a rua correta e pelo menos uma rua duplicada.", 300, 140);
                } else {
                    $tbRua = new Application_Model_Rua();
                    list($up,$del) = $tbRua->removerDuplicacoes($correto, $duplicados);
                    $this->view->dialog = array("Duplicações removidas com sucesso", "$up domicilio(s) atualizados.<br />$del rua(s) removidas.", 300, 140);
                }
            }	
	}
        

	
	public function pacienteAction(){
		$this->view->title .= " - Pacientes";
		
		if ($this->_request->isPost()) {
			$correto = $this->_request->getPost("usu_codigo", FALSE);
			$duplicados = $this->_request->getPost("usu_codigo_duplicado", FALSE);
			
			if(!$correto || !$duplicados){
				return $this->view->dialog = array("Falha!", "Selecione o paciente correto e pelo menos um um paciente duplicado.", 300, 140);
                        }			
			$tbUsu = new Application_Model_Usuario();
			$resultado = $tbUsu->removerDuplicacoes($correto, $duplicados);
			
			if(!$resultado){
                            

				$this->view->dialog = array("Falha", "Não foi possível remover as duplicações entre os pacientes selecionados", 300, 140);
				return;
			} 
			
			list($up,$del) = $resultado;
			$this->view->dialog = array("Duplicações removidas com sucesso", "$up registros(s) atualizados.<br />$del pacientes(s) removidos.", 300, 140);
		}		
	}
	public function produtoAction($horus=FALSE){
                $tbPro = new Application_Model_Produto();
		$this->view->title .= " - Produto";
                $this->view->produtos = $tbPro->buscarMedicamentosComMovimentacoes();
		if ($this->_request->isPost()) {
                    //if($horus){
                        $horus = $this->_request->getPost("horus", FALSE);
                        
                    //}
			$correto = $this->_request->getPost("pro_codigo", FALSE);
			$duplicados = $this->_request->getPost("pro_codigo_duplicado", FALSE);			
			if(!$correto || !$duplicados)
				return $this->view->dialog = array("Falha!", "Selecione o produto correto e pelo menos um um produto duplicado.", 300, 140);
			
			
			$resultado = $tbPro->removerDuplicacoes($correto, $duplicados);
			if(!$resultado){
				$this->view->dialog = array("Falha", "Não foi possível remover as duplicações entre os produtos selecionados", 300, 140);
				return;
			} 
			
			list($up,$del) = $resultado;
			$this->view->dialog = array("Duplicações removidas com sucesso", "$up registros(s) atualizados.<br />$del Produto(s) removidos.", 300, 140);
                        if($horus == "t"){
                            $this->_redirect('/default/duplicacao/horus');
                        }
		}
                
	}
        
        public function horusAction(){
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/default/duplicacao/produto.js');
            $this->produtoAction();
		
	}
        
        public function imprimirHorusAction(){
            
            Zend_Layout::getMvcInstance()->setLayout("print");
            $this->view->title = "Imprimir Produtos Horus";
            $tbPro = new Application_Model_Produto();
            $this->view->dados = $tbPro->buscarMedicamentosHorus();		
        }
	
	
}

