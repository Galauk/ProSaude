<?php

class Relatorio_MateriaisController extends Elotech_Controller_Action_Relatorio {

	private $tbPro;
	
	public function init() {
            set_time_limit(100000000000);
            $this->view->title = "Materiais";
            $this->tbPro = new Application_Model_Produto();
	}
       
	public function indexAction() {
		
	}
	
	public function estoquePsicotropicosAction(){

		$this->view->title .= " - Psicotrópicos";
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);


		if (!$set_codigo) {
			$this->view->action = array("action" => "estoque-psicotropicos");
			return $this->render("setor", NULL, TRUE); // mostra action para pedir os dados		
		}
       

		$where = $this->tbPro->relEstoquePsicotropico($this->view, $set_codigo);
		$this->relatorio($where);
	}
		

	public function produtosAVencerAction() {
		$this->view->title .= " - Produtos a vencer";
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);

		if (!$set_codigo) {
			$this->view->action = array("action" => "produtos-a-vencer");
			return $this->render("setor-data", NULL, TRUE); // mostra action para pedir os dados		
		}

		$where = $this->tbPro->relMedicamentosPorValidade($this->view, $set_codigo, $data_inicial, $data_final);
		$this->relatorio($where);
	}
	public function formEntradaPsicotropicosAction() {
        $this->view->title = "Entrada Psicotrópicos";
        $this->view->portarias = $this->portariaPsicotropicos();    
    }
    public function relEntradaPsicotropicosAction() {       

        $set_codigo = $this->_request->getPost("set_codigo", FALSE);

        $set_nome = $this->_request->getPost("set_nome", FALSE);    

        $portarias = $this->_request->getPost("psico_codigo", FALSE);
                $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);

        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
                //die($set_codigo."-".$data_inicial."-".$data_final);
        if (!$set_codigo) {
            $this->view->action = array("action" => "form-entrada-psicotropicos");
            $this->view->portarias =  $this->portariaPsicotropicos();    
            return $this->render("form-entrada-psicotropicos"); // mostra action para pedir os dados
        }
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $where = $this->tbPro->relEntradaPsicotropicos($this->view, $set_codigo, $data_inicial, $data_final, $portaria);
        $this->view->where = $where;
        $tbUsr = new Application_Model_Usuarios();
         $array = array('uni_desc'=> $tbUsr->getUsrAtual()->uni_desc,
                           'set_nome' => $set_nome);
        if ($data_inicial)
            $array["data_inicial"] = $data_inicial;
        if ($data_final)
            $array["data_final"] = $data_final;
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        $this->render('rel-entrada-psicotropicos');
    }

        public function formBalancoPsicotropicosAction() {
        $this->view->title = "Balanço Completo de Psicotrópicos";
        $this->view->portarias = $this->portariaPsicotropicos();    
    }
    public function relBalancoPsicotropicosAction() {		

        $set_codigo = $this->_request->getPost("set_codigo", FALSE);

        $set_nome = $this->_request->getPost("set_nome", FALSE);    

        $portarias = $this->_request->getPost("psico_codigo", FALSE);
                $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);

        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
                //die($set_codigo."-".$data_inicial."-".$data_final);

        if (!$set_codigo) {
            $this->view->action = array("action" => "form-balanco-psicotropicos");
            $this->view->portarias =  $this->portariaPsicotropicos();    
            return $this->render("form-balanco-psicotropicos"); // mostra action para pedir os dados
        }
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $where = $this->tbPro->relBalancoPsicotropicos($this->view, $set_codigo, $data_inicial, $data_final, $portaria);
        $this->view->where = $where;
        $tbUsr = new Application_Model_Usuarios();
         $array = array('uni_desc'=> $tbUsr->getUsrAtual()->uni_desc,
                           'set_nome' => $set_nome);
        if ($data_inicial)
            $array["data_inicial"] = $data_inicial;
        if ($data_final)
            $array["data_final"] = $data_final;
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        $this->render('rel-balanco-psicotropicos');
	}
        
        public function formBalancoProdutoSetorAction() {
            $this->view->title .= " - Balanço Psicotrópicos";
            $this->view->portarias = $this->portariaPsicotropicos();

        }
               
        public function formSaidaProdutoSetorAction() {
            $this->view->title .= "- Saída Produtos";
            $this->view->portarias = $this->portariaPsicotropicos();

        }
              
        public function formAnvisaAction() {
            $this->view->title = "Relatório Anvisa";
            $this->view->portarias = $this->portariaPsicotropicos();
            
            
        }
        
        public function relAnvisaAction() {
            if ($this->_request->getPost("tp_rel") == 0) {
                $this->relAnvisaSinteticoAction();
            } else {
                $this->relAnvisaAnaliticoAction();
            } 
            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        }
        
        public function relAnvisaSinteticoAction() {
       		$this->view->title = "Relatório Anvisa Sintético";
             Zend_Layout::getMvcInstance()->setLayout("relatorio");
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);
		$portarias = $this->_request->getPost("psico_codigo", FALSE);
                $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);
                $pro_codigo = $this->_request->getPost("pro_codigo", FALSE);
		$filtro = $this->_request->getPost("filtro", FALSE);

	
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;


    		if (!$filtro) {
    			$this->view->action = array("action" => "anvisa");
    			return $this->render("setor-data-produto", NULL, TRUE); // mostra action para pedir os dados		
    		}
                //echo "<pre>".print_r($_POST,1);exit;
		$where = $this->tbPro->relAnvisa($this->view, $set_codigo, $data_inicial, $data_final,$pro_codigo,$portaria);
		//die($where);
		$this->relatorio($where,2);
        }
        
        public function relAnvisaAnaliticoAction() {
			$tbSec = new Application_Model_Secretaria();
			$tbConf = new Application_Model_Configuracao();
			$this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            Zend_Layout::getMvcInstance()->setLayout("retrato-print");
            $this->view->title = "Medicamentos Controlados - Livro de Psicotrópicos";
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $set_nome = $this->_request->getPost("set_nome", FALSE);
            $portarias = $this->_request->getPost("psico_codigo", FALSE);
            $portaria = "";
            foreach ($portarias as $p){
                $portaria .= $p.",";
            }
            $portaria = substr($portaria,0,-1);
            $pro_codigo = $this->_request->getPost("pro_codigo", FALSE);
            $tp_rel = $this->_request->getPost("tp_rel", FALSE);
            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $tbProd = new Application_Model_Produto();
            $dadosProd = $tbProd->dadosProdRelAnalitico($set_codigo, $data_inicial, $data_final,$pro_codigo,$portaria)->toArray();
// Lendo os produtos que contem dispensacao para incluir os pacientes que receberão
            $i = 0;
            foreach ($dadosProd as $prod) {
                $dadosProd[$i]["dispensacoes"] = $tbProd->dadosPacienteRelAnalitico($prod["pro_codigo"],$data_inicial,$data_final,$set_codigo,FALSE,$prod["ite_lote"])->toArray();
                $y = 0;
                foreach ($dadosProd[$i]["dispensacoes"] as $item) {
                    // Validando o tipo se é entrada ou saida, validação realizada por causa da transferência
                    if ($item["set_entrada"]==$set_codigo)
                        $dadosProd[$i]["dispensacoes"][$y]["mov_tipo"] = "E";
                    if ($item["set_saida"]==$set_codigo)
                        $dadosProd[$i]["dispensacoes"][$y]["mov_tipo"] = "S";
                    $y++;
                }
                //$dadosProd[$i]["entrada"] = $tbProd->entradas;
                $i++;
            }
            //echo "<pre>".print_r($dadosProd,1); die();
            // Dados cabeçalho relatório
            $tbUsr = new Application_Model_Usuarios();
			$this->view->usr = $tbUsr->getUsrAtual();
            $array = array('uni_desc'=> $tbUsr->getUsrAtual()->uni_desc,
                           'set_nome' => $set_nome);
            if ($data_inicial)
                $array["data_inicial"] = $data_inicial;
            if ($data_final)
                $array["data_final"] = $data_final;
            
            $this->view->params = serialize($array);
//            echo "<pre>".print_r($dadosProd,1)."</pre>";die();
            $this->view->dados = $dadosProd;
            $this->render("rel-anvisa-analitico");
        }
	
        public function balancoAction() {
		$this->view->title .= " - Balanço Psicotrópicos";
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);
		$psi = $this->_request->getPost("psi", FALSE);
	
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);

		if (!$set_codigo) {
			$this->view->action = array("action" => "balanco");
			return $this->render("setor-data-psicotropicos", NULL, TRUE); // mostra action para pedir os dados		
		}

		$where = $this->tbPro->relBalanco($this->view, $set_codigo, $data_inicial, $data_final,$psi);
		$this->relatorio($where);
	}
	public function transferenciasPorSetorAction() {
		$this->view->title .= " - Transferências por setor";
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);
	
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);

		if (!$set_codigo) {
			$this->view->action = array("action" => "transferencias-por-setor");
			return $this->render("setor-data", NULL, TRUE); // mostra action para pedir os dados		
		}
         $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;

		$where = $this->tbPro->relTransferencias($this->view, $set_codigo, $data_inicial, $data_final,$psi);
		$this->relatorio($where,2);
		
	
	}
        public function anvisaAction() {
		$this->view->title = "Relatório Anvisa";
		
		$set_codigo = $this->_request->getPost("set_codigo", FALSE);
               
                $pro_codigo = $this->_request->getPost("pro_codigo", FALSE);
		$filtro = $this->_request->getPost("filtro", FALSE);
	
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);

		if (!$filtro) {
			$this->view->action = array("action" => "anvisa");
			return $this->render("setor-data-produto", NULL, TRUE); // mostra action para pedir os dados		
		}
                //echo "<pre>".print_r($_POST,1);exit;
		$where = $this->tbPro->relAnvisa($this->view, $set_codigo, $data_inicial, $data_final,$pro_codigo);
		//die($where);
		$this->relatorio($where,2);
		
	
	}
        
         public function formRankingDeConsumoAction(){
            $this->view->title = "Ranking de consumo de Materiais";
            $tbPros = new Application_Model_ProdutoSubGrupo();
            $this->view->subgrupos = $tbPros->getSubGrupos();
        }
        
        public function relRankingConsumoAction(){
            $this->_helper->layout->setLayout("retrato-print");
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbPro = new Application_Model_Produto();
            $tbUsr = new Application_Model_Usuarios();
            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $pros_codigo = $this->_request->getPost("pros_codigo", FALSE);
            $quantidade = $this->_request->getPost("quantidade", FALSE);
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->produtos = $tbPro->getRankingProdutos($set_codigo,$quantidade,$data_inicial,$data_final,$pros_codigo);
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "RANKING DE CONSUMO";
            $tbUni = new Application_Model_Unidade();
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $tbUni->getUnidade($uni_codigo)
                            );
        $this->view->params = $params;
        }
        public function portariaPsicotropicos() {
            $tbPort = new Application_Model_PortariaPsicotropico();
            return $tbPort->getPortaria();
        }
        public function relCurvaAbcIndexAction(){
            $this->view->title = "Curva ABC de Consumo";
        }
        
        public function relCurvaAbcAction(){
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbPro = new Application_Model_Produto();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "CURVA ABC DE CONSUMO";
            $this->_helper->layout->setLayout("retrato-print");
            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $tbItensMov = new Application_Model_ItensMovimento();
            $this->view->produtos = $tbItensMov->getItensCurvaAbc($data_inicial, $data_final, $set_codigo);
            $this->view->total_lista = $this->calculaTotalCurvaAbcAction($this->view->produtos->toArray());
            $tbUni = new Application_Model_Unidade();
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $tbUni->getUnidade($uni_codigo)
                            );
        $this->view->params = $params;
        }
        
        public function calculaTotalCurvaAbcAction($produtos=FALSE){
            foreach ($produtos as $produto){
                $vlr_total_lista += $produto[vlr_total_item];
                $qtd_total_lista += $produto[total_qtde];
            }
            $total_lista = array("qtde_total"=>$qtd_total_lista,
                                 "vlr_total"=>$vlr_total_lista);
            return $total_lista;
        }
        
         public function formMovimentoEntradaAction() {
            $this->view->title .= "Movimentação de Entrada";
//            $tbPros = new Application_Model_ProdutoSubGrupo();
//die("Ola que tal");
  //          $this->view->subgrupos = $tbPros->getSubGrupos();
        }
        
        public function movimentoEntradaAction(){
            Zend_Layout::getMvcInstance()->setLayout("retrato-print");
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "Movimentos de Entrada";
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $pro_codigo = $this->_request->getPost("pro_codigo", FALSE);
            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $pros_codigo = $this->_request->getPost("pros_codigo", FALSE);
            $tbMov = new Application_Model_Movimento();
            $tbIte = new Application_Model_ItensMovimento();
            $mov = $tbMov->getMovimentosEntrada($set_codigo, $data_inicial, $data_final, $pro_codigo,null,$pros_codigo)->toArray();
            $i = 0;
            foreach($mov as $movimento){
                $mov[$i][$movimento[mov_codigo]] = $tbIte->getProdutosPorMovimento($movimento[mov_codigo],$pro_codigo)->toArray();
                $i++;
            }
            $this->view->relatorio = $mov;           

        }
        




        public function formRelacaoNotificacoesReceitaAction() {
        $this->view->title = "Relação de Notificações de Receita";
        $this->view->portarias = $this->portariaPsicotropicos();    
    }

    public function relRelacaoNotificacoesReceitaAction() {       
        $portarias = $this->_request->getPost("psico_codigo", FALSE);
                $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);

        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
                //die($set_codigo."-".$data_inicial."-".$data_final);
        
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $where = $this->tbPro->relRelacaoNotificacoesReceita($this->view, $data_inicial, $data_final, $portaria);
        $this->view->where = $where;
         


        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        $this->view->portarias = $this->portariaPsicotropicos();
        $this->render('rel-relacao-notificacoes-receita');
    }

}

