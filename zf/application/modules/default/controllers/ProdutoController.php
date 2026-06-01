<?php

class ProdutoController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
	}

	public function indexAction() {
		// action body
	}
	
	/**
	 * Busca os todos produtos (medicamento ou não)
	 * Retorna um json, para ser usado com o plugin jquery.buscar.js
	 */
	public function buscarAction(){
		$term = $this->_getParam("term",FALSE);
                $horus = $this->_getParam("horus",FALSE);
                
		if(!$term)
			return false;
		
		$tbPro = new Application_Model_Produto();
		$limite = $this->_getParam("limite",FALSE);
		if($horus){
                    $this->view->dados = $tbPro->buscarMedicamentosHorus($term, $limite);
                }else{
                    $this->view->dados = $tbPro->buscarMedicamentos($term, $limite);
                }
		
		return $this->render("dados", NULL, TRUE);		
	}
	
	/**
	 * Busca os todos Medicamentos (controlados ou não)
	 * Retorna um json, para ser usado com o plugin jquery.buscar.js
	 */
	public function medicamentoAction(){		
		$term = $this->_getParam("term",FALSE);
		if(!$term)
			return false;
		
		$tbPro = new Application_Model_Produto();
		$limite = $this->_getParam("limite",FALSE);
		
		$this->view->dados = $tbPro->buscarMedicamentos($term, NULL, $limite);
		
		return $this->render("dados", NULL, TRUE);
	}
	
	/**
	 * Busca os Medicamentos do Posto (não-controlados)
	 * Retorna um json, para ser usado com o plugin jquery.buscar.js
	 */
	public function medicamentoPostoAction(){		
		$term = $this->_getParam("term",FALSE);
		if(!$term)
			return false;
		
		$tbPro = new Application_Model_Produto();
		$this->view->dados = $tbPro->buscarMedicamentosPosto($term);
		
		return $this->render("dados", NULL, TRUE);
	}
        
    public function buscarProdutosAction(){        
        $term = $this->_getParam("term",FALSE);
        $set_codigo = $this->_getParam("setor",FALSE);
        
        if(!$term){
            return false;
        }

        $tbPro = new Application_Model_Produto();
        $limite = $this->_getParam("limite",FALSE);
        $this->view->dados = $tbPro->buscarProdutos($term, $limite,$set_codigo);

        return $this->render("dados", NULL, TRUE);
    }
        
    public function buscarProdutosComEstoqueAction(){
        $term = $this->_getParam("term",FALSE);

        $set_codigo = $this->_getParam("setor",FALSE);
        $tipo = $this->_getParam("tipo",FALSE);

        if(!$term)
            return false;

        $tbPro = new Application_Model_Produto();
        $limite = $this->_getParam("l",FALSE);
        $this->view->dados = $tbPro->buscarProdutosComEstoque($term, $limite,$set_codigo,$tipo);

        return $this->render("dados", NULL, TRUE);
    }
	
	/**
	 * Busca os Medicamentos Controlados
	 * Retorna um json, para ser usado com o plugin jquery.buscar.js
	 */
	public function medicamentoControladosAction(){		
		$term = $this->_getParam("term",FALSE);
		if(!$term)
			return false;
		
		$tbPro = new Application_Model_Produto();
		$this->view->dados = $tbPro->buscarMedicamentosControlado($term);
		
		return $this->render("dados", NULL, TRUE);
    }

    public function buscaRemedioCodigoAction(){
        
        $cod = $this->_getParam('term', FALSE);

        if(!$cod){
            return false;
        }
        
        $tbPro = new Application_Model_Produto();
        $retorno = $tbPro->buscarMedicamentosControladoCodigo($cod);

        //echo "<pre>"; print_r($retorno); die();

        $this->view->dados = $retorno->toArray();
        return $this->render("dados", null, true);
    }
	
	public function reservarAction(){
                
		$tipo = $this->_request->getPost("tipo", FALSE);
		$codigo = $this->_request->getPost("codigo", FALSE);
		//$usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
                //$
		$saldo = $this->_request->getPost("saldo", array());
		$cont = $this->_request->getPost("cont", array());
		
		$tbPro = new Application_Model_Produto();
		$tbCFR = new Application_Model_ControleFracionadoReserva();

		try {
			$novos = $tbPro->fracionarVarios($saldo);			
			
			// PHP Bug: array_merge não preserva a chave se ela for int
			// http://br2.php.net/manual/pt_BR/function.array-merge.php#107590
			if($novos){
				foreach($novos as $k => $v)
					$cont[$k] = $v;
			}
			$tbCFR->addParaReserva($tipo, $codigo, $cont, NULL);
			$this->view->dados = array("success"=>true);
		} catch (Exception $exc) {
			$this->view->dados = array("error"=>true,"mensagem"=>$exc->getMessage());
		}
		
		return $this->render("dados", NULL, TRUE);
	}

	/**
	 * Usado para cancelar a reserva
	 */
	public function retirarReservaAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$tipo = $this->_request->getPost("tipo", FALSE);
		$codigo = $this->_request->getPost("codigo", FALSE);
		
		if(!$tipo || !$codigo)
			return FALSE;
		
		$tbCFR = new Application_Model_ControleFracionadoReserva();
		$tbCFR->devolver($tipo,$codigo);		
	}
        
    public function vincularProdutoSetorAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        
        $tbProset = new Application_Model_ProdutoSetor();
        $data = array("set_codigo"=>$set_codigo,"pro_codigo"=>$pro_codigo);
        try{
            $tbProset->salvar($data);
            $this->view->dados = 1;
        }  catch (Exception $exc){
            $this->view->dados = $exc->getMessage();
        }
        
        return $this->render("dados",NULL,TRUE);
        
    }
        
    public function getLotesAction(){
        // die("dasdasdas");
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        $enviados = $this->_getParam("enviados",FALSE); // esse parametro irá definir se ele vai buscar a quanTIDADE - as quantidades enviadas na requisicao
        $tipo = $this->_getParam("tipo",FALSE);
        // echo '<pre>';var_dump($tipo);
        if($tipo == "S"){
            $somenteVencidos = FALSE;
        }else{
            $somenteVencidos = TRUE;
        }

        $tbSal = new Application_Model_Saldo();
        $tbIte = new Application_Model_Movimento();
        $resultSaldo = $tbSal->getLotes($pro_codigo, $set_codigo,$somenteVencidos,$enviados)->toArray();
        $this->view->dados = $resultSaldo;

        return $this->render("dados",NULL,TRUE);
    }
    
    public function verificaProdutoSetorAction(){ 
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        $tbProSet = new Application_Model_ProdutoSetor();
        $verifica_vinculo_setor = $tbProSet->verificaVinculoProdutoSetor($pro_codigo,$set_codigo);
        $this->view->dados =  $verifica_vinculo_setor;
        return $this->render("dados",NULL,TRUE);
        
    }
        
    public function getProdutoAction(){
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $tbPro = new Application_Model_Produto();
        $dados = $tbPro->getProduto($pro_codigo);
        if($dados)
            $this->view->dados = $dados->toArray();
        
        return $this->render("dados",NULL,TRUE);
    }
        
        
        
    public function getProdutoComEstoqueAction(){
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        
        $tbPro = new Application_Model_Produto();
        $dados = $tbPro->getProdutoComEstoque($pro_codigo,$set_codigo);
        if($dados){
            $this->view->dados = $dados->toArray();
        }
        return $this->render("dados",NULL,TRUE);
    }
        
    public function getLoteAutomaticoAction(){
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        $sal_quantidade = $this->_getParam("quantidade",FALSE);
        $tbSal = new Application_Model_Saldo();
        $lotes = $tbSal->getLotes($pro_codigo, $set_codigo,true,1)->toArray();
        
        $lotes_dispensar = array();
        $pegar = 0;
        $faltam = $sal_quantidade;
        foreach($lotes as $lote){
            
            if($lote[sal_qtde] > $faltam){
                $pegar = $faltam;
                
            } else {
                $pegar = $lote[sal_qtde];
            }
            $faltam -= $pegar;
            
            $lotes_dispensar[$lote[sal_lote]] = $pegar."|".$lote[sal_validade];
            
            if($faltam == 0){
                break;
            }
        }
        
        if($faltam > 0){
            $lotes_dispensar["faltam"] = $faltam;
        }
        $this->view->dados = $lotes_dispensar;
        return $this->render("dados",null,true);
        
    }
    
    public function verificaSeDispensouNoDiaAction(){
        $pro_codigo = $this->_getParam("pro_codigo",FALSE);
        $usu_codigo = $this->_getParam("usu_codigo",FALSE);
        $params = array("mov_data"=>  date("d/m/Y"),"usu_codigo"=>$usu_codigo,"pro_codigo"=>$pro_codigo);
        $tbIte = new Application_Model_ItensMovimento();
        if($tbIte->verificaSeJaDispensou($params)){
            $this->view->dados = 0;
        }else{
            $this->view->dados = 1;
        }
        
        return $this->render("dados",null,true);
    }

    public function getFracionamentoAction() {
        
        $pro_codigo = $this->_getParam("pro_codigo", FALSE);
        
        $tbPro = new Application_Model_Produto();
        $tbIteMov = new Application_Model_ItensMovimento();

        $qtdIteMov = $tbIteMov->getFracionamentoMinimo($pro_codigo)->toArray();
        
        if(count($qtdIteMov) > 0 && $qtdIteMov->pro_frmmin > 0){
            $this->view->dados = $qtdIteMov;
            return $this->render("dados", null, true);
        } else {   
            $this->view->dados = $tbPro->getFracionamentoMinimo($pro_codigo)->toArray();
            return $this->render("dados", null, true);
        }
    }

    public function retornaEstoqueCentroDestinoAction(){
        $recebeCodigoItensMovimento = $this->_getParam("recebeCodigoItensMovimento", FALSE);
        $setCodigoDestino = $this->_getParam("setCodigoDestino", FALSE);
        $pro_codigo = $this->_getParam("pro_codigo", FALSE);

        $tbIteMov = new Application_Model_ItensMovimento();

        $retornaEstoqueCentroDestino = $tbIteMov->retornaEstoqueCentroDestino($recebeCodigoItensMovimento, $setCodigoDestino, $pro_codigo);

        echo json_encode($retornaEstoqueCentroDestino);

        exit();

    }

}

