<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Produto extends Elotech_Db_Table_Abstract {

	protected $_name = 'produto';
	protected $_primary = 'pro_codigo';
	protected $_dependentTables = array('LeitoGradeItens');

	/**
	 * Motivos da perda (vacina)
	 */
	const QUEBRA_DE_FRASCO = 1;
	const FALTA_DE_ENERGIA = 2;
	const FALHA_NO_EQUIPAMENTO = 3;
	const VALIDADE_VENCIDA = 4;
	const PROCEDIMENTO_INADEQUADO = 5;
	const FALHA_NO_TRANSPORTE = 6;
	const OUTROS_MOTIVOS = 7;
        
        /**
	 * Busca medicamentos não-controlados
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscarMedicamentosPosto($term=FALSE) {
		return $this->buscarMedicamentos($term, FALSE);
	}

	/**
	 * Busca medicamentos controlados
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscarMedicamentosControlado($term=FALSE) {
		return $this->buscarMedicamentos($term, TRUE);
	}
	
	public function buscarMedicamentosControladoCodigo($term=FALSE) {
		return $this->buscarMedicamentosCodigo($term, TRUE);
	}

	public function buscarMedicamentosCodigo($cod){
		$where = $this->select(FALSE)->setIntegrityCheck(false)->where("CAST(pro_codigo AS TEXT) LIKE '%{$cod}%'");

		if ($controlado === true) {
			$where->where("psico_codigo IS NOT NULL");
		}
		// die($where);

		$RETORNO = $this->fetchAll($where);

		// error_reporting(E_ALL);
		// print_r($RETORNO);
		// die();
		return $RETORNO;
	}

	public function buscarMedicamentos($term=FALSE, $controlado=NULL, $limite=FALSE,$movimento=FALSE) {
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("pro" => "produto"),"pro.*,(select sum(sal_qtde) from saldo where pro_codigo = pro.pro_codigo) as saldo")
			->joinLeft(array("umed" => "unidmedida"), "pro.umed_codigo = umed.umed_codigo", array("umed_nome"))
			->where("pro_situacao='A'")
			// ->where("permite_prescricao='S'")
			->order("pro_nome");

		if ($term) {
			$where->where("pro_nome ilike '%$term%'");
		}

		if ($controlado === TRUE) {
			$where->where("psico_codigo IS NOT NULL");
		} 

		if ($limite) {
			$where->limit($limite);
		}
                
		if($movimento){
			$where->join (array("im"=>"itens_movimento"), "im.pro_codigo=p.pro_codigo");
		}

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $pro) {
			$out [] = array(
				"id" => $pro->pro_codigo,
				"label" => $pro->pro_nome,
				"data" => array("pro_codigo" => $pro->pro_codigo,"pro_nome" => $pro->pro_nome,"umed_nome" => $pro->umed_nome,"saldo" => $pro->saldo)
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}
		return $out;
	}
        
	public function buscarMedicamentosComMovimentacoes(){
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("pro" => "produto"),array("pro_codigo","pro_nome"))
			->distinct("DISTINCT")
			->join(array("im"=>"itens_movimento"), "im.pro_codigo=pro.pro_codigo","")
			->where("pro_tipo='M'")
			->where("pro_situacao='A'")
			->where("pro_horus is null")
			->order("pro_nome");
		//die($where);
		return $this->fetchAll($where);
		
	}
        
	public function buscarProdutos($term=FALSE,$limit=FALSE,$set_codigo=FALSE){
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
            ->from(array("pro" => "produto"))
            ->join(array("gru" => "grupo"), "pro.gru_codigo = gru.gru_codigo", "gru_nome")
			->where("pro_situacao='A'")
			->order("pro_nome");
                
		if ($term){
			$where->where("pro_nome ilike '%$term%'");
		}

        // die($where);
		if ($limite) {
			$where->limit($limite);
		}
		
		//**Essa parte estabelece que o produto buscado tem que estar no setor passado no parametro**/
		$all = $this->fetchAll($where);
		$out = array();
		
		$tbProSet = new Application_Model_ProdutoSetor();
		
		foreach ($all as $pro) {
			/**Essa parte  apenas verifica se o produto tem vinculo com o setor passado no parametro e insere true ou false pra variavel produto_vinculo_setor**/
			if($set_codigo){
				$verifica_vinculo_setor = $tbProSet->verificaVinculoProdutoSetor($pro->pro_codigo,$set_codigo);
			}else{
				$verifica_vinculo_setor = 0;
			}
			$out [] = array(
				"id" => $pro->pro_codigo,
				"label" => $pro->pro_nome,
				"data" => array("pro_frmmin" => $pro->pro_frmmin,"pro_codigo" => $pro->pro_codigo,"pro_nome" => $pro->pro_nome,"pro_validade" => $pro->pro_validade,"pro_fracionado" => $pro->pro_fracionado,"produto_vinculo_setor"=>$verifica_vinculo_setor, "grupo" => $pro->gru_nome)
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}
		return $out;
	}        
        
	public function buscarProdutosComEstoque($term=FALSE,$limit=FALSE,$set_codigo=FALSE,$tipo=FALSE){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"))
				->distinct()
				->join(array("proset"=>"produto_setor"),"proset.pro_codigo=pro.pro_codigo","")
				->joinLeft(array("psico"=>"psicotropicos"),"pro.psico_codigo=psico.psico_codigo","psico_exige_codigo")
				->where("proset.set_codigo=$set_codigo")
				->where("pro_situacao='A'")
				->order("pro_nome");
		
		if($tipo == "S"){// sem validade
			$where->where("pro.pro_codigo in (select pro_codigo from saldo where set_codigo = $set_codigo and sal_qtde > 0)" );
		}else{
            $where->where("pro.pro_codigo in (select pro_codigo from saldo where set_codigo = $set_codigo and sal_qtde > 0 and sal_validade > CURRENT_DATE)" );
		}
          
		if ($term){
            $where->where("pro_nome ilike '%$term%'");	
		}

		if ($limit) {
            $where->limit($limit);
		}
		
		//die($where);
		$all = $this->fetchAll($where);
		$out = array();
		foreach ($all as $pro) {
            $out [] = array(
				"id" => $pro->pro_codigo,
              	"label" => $pro->pro_nome,
              	"data" => array("pro_codigo" => $pro->pro_codigo,"pro_nome" => $pro->pro_nome,"pro_validade" => $pro->pro_validade,"pro_fracionado" => $pro->pro_fracionado,"produto_vinculo_setor"=>$verifica_vinculo_setor,"psico_codigo"=>$pro->psico_codigo,'psico_exige_codigo'=>$pro->psico_exige_codigo)
            );
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}
		
		return $out;
	}
        
	public function buscarMedicamentosHorus($term=FALSE, $controlado=NULL, $limite=FALSE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"))
				->where("pro_tipo='M'")
				->where("pro_situacao='A'")
				->where("gru_codigo=99482")
				->where("pro_horus is not null")
				->where("pro_codigo not in (select distinct pro_codigo from itens_movimento)")
				->order("pro_nome");
		
		if ($term){
			$where->where("pro_nome ilike '%$term%'");
		}

		if ($limite) {
			$where->limit($limite);
		}

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $pro) {
			$out [] = array(
				"id" => $pro->pro_codigo,
				"label" => $pro->pro_nome,
				"data" => array("pro_codigo" => $pro->pro_codigo,"pro_nome" => $pro->pro_nome, "horus" => $pro->pro_horus)
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}
		return $out;
	}

	/**
	 * Seleciona os lotes necessários para atingir a quantidade selecionada.
	 */
	public function selecionaMelhorLote($produtos, $set_codigo) {
		$tbCont = new Application_Model_ControleFracionado();
		$tbSal = new Application_Model_Saldo();
		$saida = array();

		foreach ($produtos as $pro_codigo => $quantidade) {
			$dosesSelecionadas = 0;
			$desse = 0;

			// pega todos os lotes fracionados
			$fracoes = $tbCont->getLotesFracionados($pro_codigo, $set_codigo);
			foreach ($fracoes as $fracao) {
				if ($quantidade == $dosesSelecionadas){
					break; // sai do foreach;
				}

				if ($quantidade >= $dosesSelecionadas + $fracao->cont_dose) {
					$pegar = $fracao->cont_dose;
				} else {
					$pegar = $quantidade - $dosesSelecionadas;
				}

				$dosesSelecionadas += $pegar;	
				$desse += $pegar;

				if (!isset($saida[$pro_codigo])){
					$saida[$pro_codigo] = array(
						"pro_nome" => $fracao->pro_nome,
						"solicitado" => $quantidade,
						"cont" => array()
					);
				}
				
				$saida[$pro_codigo]['cont'][$fracao->cont_codigo] = array(
					"pro_lote" => $fracao->ite_lote,
					"pro_validade" => $fracao->ite_validade,
					"total" => $desse
				);
			}

			// há doses suficientes?			
			if ($quantidade > $dosesSelecionadas) {
				$naoFrac = $tbSal->getLotes($pro_codigo, $set_codigo,TRUE,1);

				// para cada lote/validade
				foreach ($naoFrac as $lote) {
					$desse = 0;
					for ($x = 1; $x <= $lote->sal_qtde; $x++) {
						if ($quantidade == $dosesSelecionadas)
							break;

						if ($quantidade >= $dosesSelecionadas + $lote->sal_dose_lote) {
							$pegar = $lote->sal_dose_lote;
						} else {
							$pegar = $quantidade - $dosesSelecionadas;
						}
						$dosesSelecionadas += $pegar;
						$desse += $pegar;

						if (!isset($saida[$pro_codigo])){
							$saida[$pro_codigo] = array(
								"pro_nome" => $lote->pro_nome,
								"solicitado" => $quantidade
							);
						}

						$saida[$pro_codigo]['saldo'][$lote->sal_codigo] = array(
							"pro_lote" => $lote->sal_lote,
							"pro_validade" => $lote->sal_validade,
							"total" => $desse
						);
					}
				}
			}

			// há doses suficientes?
			if ($quantidade > $dosesSelecionadas) {
				if($dosesSelecionadas == 0){
					$saida[$pro_codigo]['pro_nome'] = $this->fetchRow("pro_codigo=$pro_codigo")->pro_nome;
				}

				$saida[$pro_codigo]['solicitado'] = $quantidade;
				$saida[$pro_codigo]['faltou'] = $quantidade - $dosesSelecionadas;
			}
		}

		return $saida;
	}

	/**
	 * Seleciona qual o melhor lote/validade do produto em estoque (saldo)
	 * Obs.: não olha os produtos já fracionados
	 * @param array $pro_codigo 
	 */
	public function selecionaMelhorLoteNaoFracionado($pro_codigo, $set_codigo) {
		$pro_codigo = (array) $pro_codigo;

		$subSelect3 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal_3" => "saldo"), "MIN(sal_validade)")
				->where("sal_3.pro_codigo=sal_2.pro_codigo")
				->where("sal_3.set_codigo=sal_2.set_codigo")
				->where("sal_3.sal_qtde > 0")
				->where("sal_3.sal_validade >= CURRENT_DATE");


		$subSelect2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal_2" => "saldo"), "MIN(sal_2.sal_qtde)")
				->where("sal_2.pro_codigo=sal_1.pro_codigo")
				->where("sal_2.set_codigo=sal_1.set_codigo")
				->where("sal_2.sal_qtde > 0")
				->where("sal_2.sal_validade >= CURRENT_DATE")
				->where("sal_2.sal_validade = ?", $subSelect3);

		$subSelect1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal_1" => "saldo"), "MIN(sal_1.sal_codigo)")
				->where("sal_1.pro_codigo=sal.pro_codigo")
				->where("sal_1.set_codigo=sal.set_codigo")
				->where("sal_1.sal_qtde > 0")
				->where("sal_1.sal_validade >= CURRENT_DATE")
				->where("sal_1.sal_qtde = ?", $subSelect2);

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("sal" => "saldo"), array("(sal.pro_codigo)","sal_codigo", "sal_qtde", "sal_lote", "sal_validade", "sal_dose_lote"))
				->join(array("ite" => "itens_movimento"),"ite.pro_codigo = sal.pro_codigo","fab_codigo")
				->where("sal.set_codigo=?", $set_codigo)
				->where("sal.sal_qtde > 0")
				->where("sal.sal_validade >= CURRENT_DATE")
				->where("sal.pro_codigo in (?)", $pro_codigo)
				->where("sal.sal_codigo = ?", $subSelect1)
				->where("fab_codigo is not null")
				->group(array("sal.pro_codigo", "sal_codigo", "sal_qtde", "sal_lote", "sal_validade", "sal_dose_lote","fab_codigo"))
				->order(array("sal_validade", "sal_qtde"));

		return $this->fetchAll($where);
	}

	/**
	 * Soma os totais do produto no setor, que ainda não venceu
	 * @param int/array $pro_codigo
	 * @param int $set_codigo 
	 */
	public function totalValido($pro_codigo, $set_codigo) {
		$pro_codigo = (array) $pro_codigo;
		
		$sal_qtde = "COALESCE(sal.sal_qtde,0) 
		- (SELECT COALESCE(sum(remil_quantidade),0) 
			FROM requisicao_materiais_itens remi
			JOIN requisicao_materiais_itens_lote remil
				ON remil.remi_codigo = remi.remi_codigo
			JOIN requisicao_materiais rem
				ON rem.rem_codigo=remi.rem_codigo
			WHERE remi.remi_status = 'E'
				AND remi.pro_codigo = sal.pro_codigo
				AND set_codigo_sol = $set_codigo
				AND remil.remil_lote = sal.sal_lote) AS sal_qtde";

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal" => "saldo"), array("pro_codigo", "$sal_qtde","sal_qtde as saldo_original"))
				->where("pro_codigo in (?)", $pro_codigo)
				->where("set_codigo=?", $set_codigo)
				->where("sal_qtde > 0")
				->where("sal_validade > CURRENT_DATE")
				->group("pro_codigo")
				->group("sal_qtde")
				->group("sal_lote");

		return $this->fetchAll($where);
	}

	/**
	 * Busca qual o melhor lote (já fracionado) de cada produto do array
	 * @param array $pro_codigo
	 * @param int $set_codigo
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function selecionaMelhorLoteFracionado($pro_codigo, $set_codigo) {
		$pro_codigo = (array) $pro_codigo;

		$subSelect3 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cont_4" => "controlefracionado"), "")
				->join(array("ite_4" => "itens_movimento"), "ite_4.ite_codigo=cont_4.ite_codigo", "MIN(ite_4.ite_validade)")
				->where("cont_4.cont_dose > 0")
				->where("cont_4.set_codigo=cont_3.set_codigo")
				->where("ite_4.pro_codigo=ite_3.pro_codigo");

		$subSelect2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cont_3" => "controlefracionado"), "MIN(cont_dose)")
				->join(array("ite_3" => "itens_movimento"), "ite_3.ite_codigo=cont_3.ite_codigo", "")
				->where("cont_3.cont_dose > 0")
				->where("cont_3.set_codigo=cont_2.set_codigo")
				->where("ite_3.pro_codigo=ite_2.pro_codigo")
				->where("ite_3.ite_validade in (?)", $subSelect3);

		$subSelect1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cont_2" => "controlefracionado"), "MIN(cont_codigo)")
				->join(array("ite_2" => "itens_movimento"), "ite_2.ite_codigo=cont_2.ite_codigo", "")
				->where("cont_2.cont_dose > 0")
				->where("cont_2.set_codigo=cont.set_codigo")
				->where("ite_2.pro_codigo=ite.pro_codigo")
				->where("cont_2.cont_dose in (?)", $subSelect2);


		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cont" => "controlefracionado"), array("cont_codigo", "cont_dose", "ite_codigo"))
				->join(array("ite" => "itens_movimento"), "ite.ite_codigo=cont.ite_codigo", array("pro_codigo", "ite_lote", "ite_validade"))
				->where("cont_dose > 0")
				->where("set_codigo = ?", $set_codigo)
				->where("pro_codigo in (?)", $pro_codigo)
				->where("cont_codigo in (?)", $subSelect1);
                //die($where);

		return $this->fetchAll($where);
	}

	public function selecionaMelhoresVacinas() {
		$tbUsr = new Application_Model_Usuarios();
		$set_codigo = $tbUsr->getUsrAtual()->set_codigo;

		$tbCar = new Application_Model_Carteirinha();
		$pro_codigo = $tbCar->getProdutosDaCarteirinha();

		return array(
			"fechado" => $this->totalValido($pro_codigo, $set_codigo)->toArray(),
			"aberto" => $this->selecionaMelhorLoteFracionado($pro_codigo, $set_codigo)->toArray()
		);
	}

	/**
	 * Faz um movimento de saida (baixando o saldo) e adiciona na tabela controlefracionado
	 * @param int $set_codigo
	 * @param int $sal_codigo
	 * @param int $quantidade
	 * @param int $fracoes se informado, $quantidade será a quantidade necessária para ter $fracaoes frações
	 * @return int cont_codigo 
	 */
	public function fracionarFromSaldo($set_codigo,$sal_codigo,$quantidade=1, $fracoes=FALSE,$fab_codigo){
		$tbMov = new Application_Model_Movimento();
		$tbIte = new Application_Model_MovimentoItens();
		$tbCont = new Application_Model_ControleFracionado();
		$tbSaldo = new Application_Model_Saldo();

		Zend_Registry::get("logger")->log("iniciar Trasaction", Zend_Log::INFO);
		$this->getAdapter()->beginTransaction();

		try {
			$saldo = $tbSaldo->fetchRow("sal_codigo=$sal_codigo");
			if($fracoes)
				$quantidade = ceil($fracoes/(int)$saldo->sal_dose_lote);
			
			Zend_Registry::get("logger")->log("selecionado melhor lote", Zend_Log::INFO);
				
			// Faz o movimento
			$dadosMovimento = array(
				"mov_tipo" => Application_Model_Movimento::SAIDA,
				"mov_saida" => Application_Model_Movimento::DISPENSACAO,
				"set_saida" => $set_codigo
			);
			$mov_codigo = $tbMov->salvar($dadosMovimento);
			Zend_Registry::get("logger")->log("adicionado movimento: $mov_codigo", Zend_Log::INFO);

			// Faz o item do movimento
			$dadosItensMovimento = array(
				"mov_codigo" => $mov_codigo,
				"pro_codigo" => $saldo->pro_codigo,
				"ite_lote" => $saldo->sal_lote,
				"ite_validade" => $saldo->sal_validade,
				"ite_dose" => (int) $saldo->sal_dose_lote,
                                "fab_codigo" => (int) $fab_codigo,
				"ite_quantidade" => $quantidade // só pode fracionar um por vez
			);
			Zend_Registry::get("logger")->log($dadosItensMovimento, Zend_Log::INFO);
			$ite_codigo = $tbIte->salvar($dadosItensMovimento);
			Zend_Registry::get("logger")->log("adicionado itens_movimento: $ite_codigo", Zend_Log::INFO);

			// salva no controlefracionado
			$dadosControleFracionado = array(
				"cont_dose" => ($saldo->sal_dose_lote*$quantidade),
				"set_codigo" => $set_codigo,
				"ite_codigo" => $ite_codigo
			);
			
			$cont_codigo = $tbCont->salvar($dadosControleFracionado);
			
			Zend_Registry::get("logger")->log("adicionado controlefracionado", Zend_Log::INFO);
			$this->getAdapter()->commit();

			return $cont_codigo;
		} catch (Exception $e) {
			Zend_Registry::get("logger")->log("Exception: " . $e->getMessage(), Zend_Log::WARN);
			Zend_Registry::get("logger")->log($e->getTrace(), Zend_Log::INFO);
			echo "set: $set_codigo, sal: $sal_codigo, qtde: $quantidade, fracao: $fracoes\n";
			echo "exc: ".$e->getMessage();
			echo "\n: ".$e->getTraceAsString();
			$this->getAdapter()->rollBack();
		}
	}
	
	/**
	 * Fraciona um produto.
	 * @example vacinas => doses
	 * @example Febre Amarela (1 frasco) => 25x doses de F.A.
	 * @param int $pro_codigo
	 * @param int $set_codigo (Opcional)
	 */
	public function fracionar($pro_codigo, $set_codigo=FALSE, $sal_codigo=FALSE) {
		Zend_Registry::get("logger")->log("Fracionar: $pro_codigo", Zend_Log::INFO);
		if (!$set_codigo) {
			$tbUsr = new Application_Model_Usuarios();
			$set_codigo = $tbUsr->getUsrAtual()->set_codigo;
		}

		// verifica se já existe fracionado
		$fracionado = $this->selecionaMelhorLoteFracionado($pro_codigo, $set_codigo);
		if ($fracionado->count()) {
			Zend_Registry::get("logger")->log($fracionado->toArray(), Zend_Log::INFO);
			
			return false;
		}
		$saldo = $this->selecionaMelhorLoteNaoFracionado($pro_codigo, $set_codigo);
		return $this->fracionarFromSaldo($set_codigo, $saldo[0]['sal_codigo'],1,false,$saldo[0]['fab_codigo']);		
	}

	public function fracionarVarios($produtos,$set_codigo=FALSE){
		
		if (!$set_codigo) {
			$tbUsr = new Application_Model_Usuarios();
			$set_codigo = $tbUsr->getUsrAtual()->set_codigo;
		}
		
		$cont = array();
		foreach($produtos as $sal_codigo => $quantidade){
			$cont_codigo = $this->fracionarFromSaldo($set_codigo, $sal_codigo, false, $quantidade);
			
			echo "cont_codigo: $cont_codigo\n";
			$cont [$cont_codigo]= $quantidade;
		}
		
		return $cont;
	}
	
	/**
	 * Descarta um produto da tabela controlefracionado
	 * @param int $pro_codigo
	 * @param int $set_codigo 
	 */
	public function descartar($pro_codigo, $motivo=self::OUTROS_MOTIVOS, $set_codigo=FALSE) {
		if (!$set_codigo) {
			$tbUsr = new Application_Model_Usuarios();
			$set_codigo = $tbUsr->getUsrAtual()->set_codigo;
		}

		// verifica se existe fracionado
		$fracionado = $this->selecionaMelhorLoteFracionado($pro_codigo, $set_codigo);
		if (!$fracionado->count()) { // se não houver
			Zend_Registry::get("logger")->log("Produto $pro_codigo não possui frações/doses abertas", Zend_Log::INFO);
			return false;
		}

		$fracionado = $fracionado->current();
		$dados = array(
			"cont_codigo" => $fracionado->cont_codigo,
			"cont_dose" => 0,
			"cont_perda" => $fracionado->cont_dose,
			"cont_perda_motivo" => $motivo,
			"set_codigo" => $set_codigo,
			"ite_codigo" => $fracionado->ite_codigo
		);
		Zend_Registry::get("logger")->log($dados, Zend_Log::INFO);
		$tbCont = new Application_Model_ControleFracionado();
		$tbCont->salvar($dados);
	}

	/* RELATORIOS */

	public function relMedicamentosPorValidade(&$dados, $set_codigo, $data_inicial=FALSE, $data_final=FALSE) {
		$dados->title = "Produtos a vencer";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("pro_nome" => "Medicamento", "sal_lote" => "Lote", "sal_validade" => "Validade", "sal_qtde" => "Quant."),
			"formato" => array("sal_validade" => "data", "sal_qtde" => "num")
		);

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal" => "saldo"), array("sal_lote", "sal_validade", "sal_qtde"))
				->join(array("pro" => "produto"), "pro.pro_codigo=sal.pro_codigo", "pro_nome")
				->where("sal.sal_qtde > 0")
				->where("sal.set_codigo=?", $set_codigo)
				->order("pro_nome");

		if ($data_inicial)
			$where->where("sal.sal_validade >= ?", $data_inicial);

		if ($data_final)
			$where->where("sal.sal_validade <= ?", $data_final);

		return $where;
	}
	public function relEntradaPsicotropicos(&$dados, $set_codigo, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE) {
           // die("CHEGO");
		$dados->title = "Entrada Psicotrópicos";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("mov_nr_nota" => "Número da Nota" ,"pro_nome" => "Medicamento", "for_nome" => "Fornecedor", "for_cnpj" => "CNPJ", "ite_quantidade" => "Quant. Adiquirida"),
			"formato" => array("ite_quantidade" => "num")
		);

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"), array("pro_nome","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao", "pro_tipo"))
				->join(array("ite" => "itens_movimento"), "ite.pro_codigo=pro.pro_codigo", "SUM(ite_quantidade) AS ite_quantidade" )
				->join(array("mov" => "movimento"), "mov.mov_codigo=ite.mov_codigo",array("mov_nr_nota"))
				->join(array("f"=>"fornecedor"),"f.for_codigo=mov.for_codigo",array("for_nome","for_cnpj"))
				->join(array("set"=>"setor"),"set.set_codigo=mov.set_entrada")
				->join(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo","psico_nome")


				->where("pro.pro_tipo = 'M'")
				->where("mov.mov_tipo = 'E'")
				->where("set.set_codigo=?",$set_codigo)
				->group(array("for_nome","pro_nome","for_cnpj","set.set_codigo","mov_nr_nota","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao", "pro_tipo","psico_nome"))
				->order("pro_nome");

		if ($data_inicial){
			$where->where("mov.mov_data>= ?", $data_inicial);
		}

		if ($data_final){
			$where->where("mov.mov_data <= ?", $data_final);
		}

		if ($portaria){
			$where->where("psi.psico_codigo in ($portaria)");

        }
        //die($where);
        return $this->fetchAll($where);
	}

	public function relBalancoPsicotropicos(&$dados, $set_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE) {
		$dados->title = "Balanço Completo de Psicotrópicos";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("pro_nome" => "Medicamento", "ite_quantidade" => "Quant. Adiquirida", "sal_qtde" => "Quant."),
			"formato" => array("ite_quantidade" => "num", "sal_qtde" => "num")
		);

		$entrada = $this->getEntradaPsicotropicos($set_codigo,$data_inicial,$data_final,$portaria);
		$dispensacao =  $this->getDispensacaoPsicotropicos($set_codigo,$data_inicial,$data_final,$portaria,"ent.pro_codigo = pro.pro_codigo");
		$perda = $this->getPerdaPsicotropicos($set_codigo,$data_inicial,$data_final,$portaria,"ent.pro_codigo = pro.pro_codigo");

      
                $where = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->from(array("ent" => $entrada),array("*",new Zend_Db_Expr ("(".$dispensacao.")"),new Zend_Db_Expr ("(".$perda.")")));

        return $this->fetchAll($where);
	}


	public function getEntradaPsicotropicos($set_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE)  {
$testei = "select calcula_estoque(pro.pro_codigo, $set_codigo,  '$data_inicial') qtd_inicial";
$testef = "select calcula_estoque( pro.pro_codigo, $set_codigo, '$data_final') qtd_final";


			$where1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"), array("pro_codigo","pro_nome","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao"))
				->join(array("ite" => "itens_movimento"), "ite.pro_codigo=pro.pro_codigo",array("SUM(ite_quantidade) AS entrada_qtd","($testei)" ,"($testef)" ))
				->join(array("mov" => "movimento"), "mov.mov_codigo=ite.mov_codigo","")
				->joinLeft(array("setor"=>"setor"),"setor.set_codigo=mov.set_entrada")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo","psico_nome")
				->where("pro.psico_codigo is not null")
				->where("mov.mov_tipo = 'E'")
				
				->group(array("pro_nome","pro.pro_codigo" ,"setor.set_codigo","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao","psico_nome","mov_tipo","mov_entrada"));

		if ($data_inicial){
			$where1->where("mov.mov_data >= ?", $data_inicial);
		}

		if ($data_final){
			$where1->where("mov.mov_data <= ?", $data_final);
		}

		if ($portaria){
			$where1->where("psi.psico_codigo in ($portaria)");

        }

		if ($set_codigo){
			$where1->where("setor.set_codigo=?",$set_codigo);

        }
        return $where1;
	}



		public function getDispensacaoPsicotropicos($set_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE,$onSql=FALSE)  {
                    $testei = "select calcula_estoque(pro.pro_codigo, $set_codigo,  '$data_inicial') qtd_inicial";
                    $testef = "select calcula_estoque( pro.pro_codigo, $set_codigo, '$data_final') qtd_final";


//		$where2 = $this->select(FALSE)
//				->setIntegrityCheck(FALSE)
//				->from(array("pro" => "produto"), array("pro_nome","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao"))
//				->join(array("ite" => "itens_movimento"), "ite.pro_codigo=pro.pro_codigo", array(new Zend_Db_Expr ('0 AS entrada_qtd'),"SUM(ite_quantidade) AS dispensacao_qtd",new Zend_Db_Expr ('0 AS perda_qtd'),"($testei)" ,"($testef)"))
//				->join(array("mov" => "movimento"), "mov.mov_codigo=ite.mov_codigo","")
//				
//				->joinLeft(array("setor"=>"setor"),"setor.set_codigo=mov.set_saida")
//				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo","psico_nome")
//				->where("pro.psico_codigo is not null")
//				->where("mov.mov_tipo = 'S'")
//				->where("mov.mov_saida = 'D'")
                    $where2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"), array(""))
				->join(array("ite" => "itens_movimento"), "ite.pro_codigo=pro.pro_codigo", array("SUM(ite_quantidade) AS dispensacao_qtd"))
				->join(array("mov" => "movimento"), "mov.mov_codigo=ite.mov_codigo","")
				
				->joinLeft(array("setor"=>"setor"),"setor.set_codigo=mov.set_saida","")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo","")
				->where("pro.psico_codigo is not null")
				->where("mov.mov_tipo = 'S'")
				->where("mov.mov_saida = 'D'")
                               
				
				->group(array("pro_nome","pro.pro_codigo" ,"setor.set_codigo","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao","psico_nome","mov_tipo","mov_saida"));

		if ($data_inicial){
			$where2->where("mov.mov_data >= ?", $data_inicial);
		}
                if($onSql){
                     $where2->where($onSql);
                }

		if ($data_final){
			$where2->where("mov.mov_data <= ?", $data_final);
		}

		if ($portaria){
			$where2->where("psi.psico_codigo in ($portaria)");

                 }

		if ($set_codigo){
			$where2->where("setor.set_codigo=?",$set_codigo);

                 }
        return $where2;
	}



		public function getPerdaPsicotropicos($set_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE,$onSql=FALSE)  {
                $testei = "select calcula_estoque(pro.pro_codigo, $set_codigo,  '$data_inicial') qtd_inicial";
                $testef = "select calcula_estoque( pro.pro_codigo, $set_codigo, '$data_final') qtd_final";

		$where3 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pro" => "produto"), array())
				->join(array("ite" => "itens_movimento"), "ite.pro_codigo=pro.pro_codigo",array("SUM(ite_quantidade) AS perda_qtd"))
				->join(array("mov" => "movimento"), "mov.mov_codigo=ite.mov_codigo","")
				
				->joinLeft(array("setor"=>"setor"),"setor.set_codigo=mov.set_entrada","")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo","")
				->where("pro.psico_codigo is not null")
				->where("mov.mov_tipo = 'S'")
				->where("mov.mov_saida = 'S'")
				
				->group(array("pro_nome","pro.pro_codigo" ,"setor.set_codigo","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao","psico_nome","mov_tipo","mov_saida"));

		if ($data_inicial){
			$where3->where("mov.mov_data >= ?", $data_inicial);
		}

		if ($data_final){
			$where3->where("mov.mov_data <= ?", $data_final);
		}
                if($onSql){
                     $where3->where($onSql);
                }
		if ($portaria){
			$where3->where("psi.psico_codigo in ($portaria)");

                 }
		if ($set_codigo){
			$where3->where("setor.set_codigo=?",$set_codigo);

                 }
        return $where3;
	}
	
	public function relBalanco($set_codigo, $data_inicial=FALSE, $data_final=FALSE,$psi,$portaria=FALSE) {
		//die(var_dump($portaria));
		// Título Relatório
                //$dados->title = "Balanço Psicotrópicos";
		// Serializando os POSTS
                //$dados->params = serialize($_POST);
		//$dados->config = array(
                    // Formando as TR do relatório
                //   "th" => array("pro_codigo_dcb"=>"Nº do Código DCB","pro_descricao_dcb"=>"Descriminação DCB","pro_nome" => "Medicamento","pro_apresentacao_concentracao"=>"Apresentação e Concentração", "saldo_inicial"=>"Estoque Inicial", "entrada" => "Entrada", "saida" => "Saida", "perda" => "Perdas", "saldo" => "Estoque Final"),
                    // Formatando os valores do relatório
                //    "formato" => array("entrada" => "num","saida" => "num","saldo" => "num","saldo_inicial"=>"num", "perda" => "num")
		//);
                // Validação 
                if ($data_inicial){
                    $datai = " AND mov_data >= '$data_inicial'";
                    $dataiSaldo = " AND sal_data >= '$data_inicial'";
                    $datafSaldoInicial = "AND mov_data < '$data_inicial'";
                }
		if ($data_final){
                    $dataf = " AND mov_data <= '$data_final'";
                    $datafSaldo = " AND sal_data <= '$data_final'";
                } else{
                    //$datafSaldo = " AND sal_data <= 'NOW()'";
                    $datafSaldoInicial =  " AND mov_data <= 'NOW()'";
		}
		// Validações Psicotropicos
                if($psi=="s"){
                    $psicotropicos = " AND psico_codigo is not null";
		}
                // Validações de Data, Data Inicial e Final Lógica Antiga
		 
               
                // Consulta SQL
                $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("pro" => "produto"), array("pro_nome",
        "pro_codigo_dcb",
        "pro_descricao_dcb",
        "pro_apresentacao_concentracao",
        "pro_tipo",
        "(SELECT 
            SUM(ite_quantidade)
         FROM 
            produto p2
         JOIN 
            itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
         JOIN 
            movimento m2 ON m2.mov_codigo = i2.mov_codigo
         WHERE
             1=1
             $psicotropicos
             $datai
             $dataf
             AND (mov_tipo = 'E' or mov_tipo = 'T') 
             AND set_entrada = $set_codigo
             AND pro.pro_codigo = p2.pro_codigo) as entrada",
          "(SELECT 
                SUM(ite_quantidade) 
            FROM 
                produto p2
            JOIN 
                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
            JOIN
                movimento m2 ON i2.mov_codigo = m2.mov_codigo
            WHERE 
                1=1
                $psicotropicos
                $datai
                $dataf
                AND (mov_tipo = 'S' OR mov_tipo = 'T')
                AND set_saida = $set_codigo
                AND pro.pro_codigo = p2.pro_codigo
                AND (mov_saida <> 'R' AND mov_saida <> 'S-PE')) as saida",
            "((SELECT 
                SUM(ite_quantidade)
             FROM 
                produto p2
             JOIN 
                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
             JOIN 
                movimento m2 ON m2.mov_codigo = i2.mov_codigo
             WHERE
                 1=1
                 $psicotropicos
                 $datafSaldoInicial
                 AND (mov_tipo = 'E' or mov_tipo = 'T') 
                 and mov_entrada is not null 
                 AND set_entrada = $set_codigo
                 AND pro.pro_codigo = p2.pro_codigo) -
           (SELECT 
                COALESCE(SUM(ite_quantidade),0)  
            FROM 
                produto p2
            JOIN 
                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
            JOIN
                movimento m2 ON i2.mov_codigo = m2.mov_codigo
            WHERE 
                1=1
                $psicotropicos
                $datafSaldoInicial
                AND (mov_tipo = 'S' OR mov_tipo = 'T')
                and mov_saida is not null 
                AND set_saida = $set_codigo
                AND pro.pro_codigo = p2.pro_codigo)) as saldo_inicial",
          "(SELECT 
                SUM(ite_quantidade) 
            FROM 
                produto p2
            JOIN 
                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
            JOIN
                movimento m2 ON i2.mov_codigo = m2.mov_codigo
            WHERE 
                1=1
                $psicotropicos
                $datai
                $dataf
                AND (mov_tipo = 'S')
                AND (mov_saida = 'R' or mov_saida = 'S-PE')
                AND set_saida = $set_codigo
                AND pro.pro_codigo = p2.pro_codigo) as perda"))
				->joinLeft(array("ite"=>"itens_movimento"), "ite.pro_codigo=pro.pro_codigo","")
				->joinLeft(array("mov"=>"movimento"), "mov.mov_codigo=ite.mov_codigo","")
				->joinLeft(array("f"=>"fornecedor"),"f.for_codigo=mov.for_codigo","")
				->joinLeft(array("set"=>"setor"),"set.set_codigo=mov.set_entrada","")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo",array("psico_nome"))
                                ->where("mov.set_entrada= $set_codigo OR mov.set_saida = $set_codigo")		
                                ->where ("pro.pro_tipo = 'M'")

				->group(array("for_nome","pro_nome","for_cnpj","set.set_codigo","pro.pro_codigo","mov_data","psico_nome"))
				->order("pro_nome");
		if($psi=="s"){
			$where->where("pro.psico_codigo is not null");
		}
		
                if ($data_inicial)
			$where->where("mov.mov_data>= ?", $data_inicial);
		
		if ($data_final)
			$where->where("mov.mov_data <= ?", $data_final);
                if($portaria){
                	foreach($portaria as $p){
                		//-> Comentei pois estava dando problemas 19/09/2018 :: DILEE
                		//die(var_dump($p));
                		//$where->where("pro.psico_codigo =?",$p);
                	}
                }
                       // $where->where("pro.psico_codigo = $portaria");
                // echo "<pre>".print_r($where,1); exit;
                //die($where->__toString());
		//return $where;
              //  die($where);
                return $this->fetchAll($where);
                       
	}



public function getSaida($set_codigo, $data_inicial=FALSE, $data_final=FALSE,$psi,$portaria=FALSE) {
		//die(var_dump($portaria));
		// Título Relatório
                //$dados->title = "Balanço Psicotrópicos";
		// Serializando os POSTS
                //$dados->params = serialize($_POST);
		//$dados->config = array(
                    // Formando as TR do relatório
                //   "th" => array("pro_codigo_dcb"=>"Nº do Código DCB","pro_descricao_dcb"=>"Descriminação DCB","pro_nome" => "Medicamento","pro_apresentacao_concentracao"=>"Apresentação e Concentração", "saldo_inicial"=>"Estoque Inicial", "entrada" => "Entrada", "saida" => "Saida", "perda" => "Perdas", "saldo" => "Estoque Final"),
                    // Formatando os valores do relatório
                //    "formato" => array("entrada" => "num","saida" => "num","saldo" => "num","saldo_inicial"=>"num", "perda" => "num")
		//);
                // Validação 
                if ($data_inicial){
                    $datai = " AND mov_data >= '$data_inicial'";
                    $dataiSaldo = " AND sal_data >= '$data_inicial'";
                    $datafSaldoInicial = "AND mov_data < '$data_inicial'";
                }
		if ($data_final){
                    $dataf = " AND mov_data <= '$data_final'";
                    $datafSaldo = " AND sal_data <= '$data_final'";
                } else{
                    //$datafSaldo = " AND sal_data <= 'NOW()'";
                    $datafSaldoInicial =  " AND mov_data <= 'NOW()'";
		}
		// Validações Psicotropicos
                if($psi=="s"){
                    $psicotropicos = " AND psico_codigo is not null";
		}
                // Validações de Data, Data Inicial e Final Lógica Antiga
		 
               
                // Consulta SQL
                $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("pro" => "produto"), array("pro_nome",
                                                                        "pro_codigo_dcb",
                                                                        "pro_descricao_dcb",
                                                                        "pro_apresentacao_concentracao",
                                                                        "pro_tipo"
                                                                        ,
                                                                          "(SELECT 
                                                                                SUM(ite_quantidade) 
                                                                            FROM 
                                                                                produto p2
                                                                            JOIN 
                                                                                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                            JOIN
                                                                                movimento m2 ON i2.mov_codigo = m2.mov_codigo
                                                                            WHERE 
                                                                                1=1
                                                                                $psicotropicos
                                                                                $datai
                                                                                $dataf
                                                                                AND (mov_tipo = 'S' OR mov_tipo = 'T')
                                                                                AND set_saida = $set_codigo
                                                                                AND pro.pro_codigo = p2.pro_codigo
                                                                                AND (mov_saida <> 'R' AND mov_saida <> 'S-PE')) as saida"))
				->joinLeft(array("ite"=>"itens_movimento"), "ite.pro_codigo=pro.pro_codigo","")
				->joinLeft(array("mov"=>"movimento"), "mov.mov_codigo=ite.mov_codigo",array("mov_nr_nota"))
				->joinLeft(array("f"=>"fornecedor"),"f.for_codigo=mov.for_codigo",array("for_nome","for_cnpj"))
				->joinLeft(array("set"=>"setor"),"set.set_codigo=mov.set_entrada","")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo",array("psico_nome"))
                                ->where("mov.set_entrada= $set_codigo OR mov.set_saida = $set_codigo")		
                                ->where ("pro.pro_tipo = 'M'")
                                ->where ("(SELECT 
                                                                                SUM(ite_quantidade) 
                                                                            FROM 
                                                                                produto p2
                                                                            JOIN 
                                                                                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                            JOIN
                                                                                movimento m2 ON i2.mov_codigo = m2.mov_codigo
                                                                            WHERE 
                                                                                1=1
                                                                                $psicotropicos
                                                                                $datai
                                                                                $dataf
                                                                                AND (mov_tipo = 'S' OR mov_tipo = 'T')
                                                                                AND set_saida = $set_codigo
                                                                                AND pro.pro_codigo = p2.pro_codigo
                                                                                AND (mov_saida <> 'R' AND mov_saida <> 'S-PE')) is not null")

				->group(array("for_nome","pro_nome","for_cnpj","set.set_codigo","pro.pro_codigo","mov_data","mov.mov_nr_nota","psico_nome"))
				->order("pro_nome");
		if($psi=="s"){
			$where->where("pro.psico_codigo is not null");
		}
		
                if ($data_inicial)
			$where->where("mov.mov_data>= ?", $data_inicial);
		
		if ($data_final)
			$where->where("mov.mov_data <= ?", $data_final);
                if($portaria){
                	foreach($portaria as $p){
                		//die(var_dump($p));
                		$where->where("pro.psico_codigo =?",$p);
                	}
                }
                       // $where->where("pro.psico_codigo = $portaria");
                // echo "<pre>".print_r($where,1); exit;
                //die($where->__toString());
		//return $where;
                // die($where);
                return $this->fetchAll($where);
                       
	}




public function getEntrada($set_codigo, $data_inicial=FALSE, $data_final=FALSE,$psi,$portaria=FALSE) {
		//die(var_dump($portaria));
		// Título Relatório
                //$dados->title = "Balanço Psicotrópicos";
		// Serializando os POSTS
                //$dados->params = serialize($_POST);
		//$dados->config = array(
                    // Formando as TR do relatório
                //   "th" => array("pro_codigo_dcb"=>"Nº do Código DCB","pro_descricao_dcb"=>"Descriminação DCB","pro_nome" => "Medicamento","pro_apresentacao_concentracao"=>"Apresentação e Concentração", "saldo_inicial"=>"Estoque Inicial", "entrada" => "Entrada", "saida" => "Saida", "perda" => "Perdas", "saldo" => "Estoque Final"),
                    // Formatando os valores do relatório
                //    "formato" => array("entrada" => "num","saida" => "num","saldo" => "num","saldo_inicial"=>"num", "perda" => "num")
		//);
                // Validação 
                if ($data_inicial){
                    $datai = " AND mov_data >= '$data_inicial'";
                    $dataiSaldo = " AND sal_data >= '$data_inicial'";
                    $datafSaldoInicial = "AND mov_data < '$data_inicial'";
                }
		if ($data_final){
                    $dataf = " AND mov_data <= '$data_final'";
                    $datafSaldo = " AND sal_data <= '$data_final'";
                } else{
                    //$datafSaldo = " AND sal_data <= 'NOW()'";
                    $datafSaldoInicial =  " AND mov_data <= 'NOW()'";
		}
		// Validações Psicotropicos
                if($psi=="s"){
                    $psicotropicos = " AND psico_codigo is not null";
		}
                // Validações de Data, Data Inicial e Final Lógica Antiga
		 
               
                // Consulta SQL
                $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("pro" => "produto"), array("pro_nome",
                                                                        "pro_codigo_dcb",
                                                                        "pro_descricao_dcb",
                                                                        "pro_apresentacao_concentracao",
                                                                        "pro_tipo",
                                                                        "(SELECT 
                                                                            SUM(ite_quantidade)
                                                                         FROM 
                                                                            produto p2
                                                                         JOIN 
                                                                            itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                         JOIN 
                                                                            movimento m2 ON m2.mov_codigo = i2.mov_codigo
                                                                         WHERE
                                                                             1=1
                                                                             $psicotropicos
                                                                             $datai
                                                                             $dataf
                                                                             AND (mov_tipo = 'E' or mov_tipo = 'T') 
                                                                             AND set_entrada = $set_codigo
                                                                             AND pro.pro_codigo = p2.pro_codigo) as entrada"))
				->joinLeft(array("ite"=>"itens_movimento"), "ite.pro_codigo=pro.pro_codigo","ite_quantidade")
				->joinLeft(array("mov"=>"movimento"), "mov.mov_codigo=ite.mov_codigo",array("mov_nr_nota"))
				->joinLeft(array("f"=>"fornecedor"),"f.for_codigo=mov.for_codigo",array("for_nome","for_cnpj"))
				->joinLeft(array("set"=>"setor"),"set.set_codigo=mov.set_entrada","")
				->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo",array("psico_nome"))
                                ->where("mov.set_entrada= $set_codigo OR mov.set_saida = $set_codigo")
                                ->where("(mov_tipo = 'E')")		
                                ->where ("pro.pro_tipo = 'M'")

				->group(array("for_nome","pro_nome","for_cnpj","set.set_codigo","pro.pro_codigo","mov_data","mov_nr_nota", "psico_nome","ite_quantidade"))
				->order("pro_nome");
		if($psi=="s"){
			$where->where("pro.psico_codigo is not null");
		}
		
                if ($data_inicial)
			$where->where("mov.mov_data>= ?", $data_inicial);
		
		if ($data_final)
			$where->where("mov.mov_data <= ?", $data_final);
                if($portaria){
                	foreach($portaria as $p){
                		//die(var_dump($p));
                		$where->where("pro.psico_codigo =?",$p);
                	}
                }
                       // $where->where("pro.psico_codigo = $portaria");
                // echo "<pre>".print_r($where,1); exit;
                //die($where->__toString());
		//return $where;
            //     die($where);
                return $this->fetchAll($where);
                       
	}

	public function relEstoquePsicotropico(&$dados, $set_codigo) {
		$dados->title = "Estoque de Psicotrópicos";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("pro_nome" => "Medicamento", "sal_lote" => "Lote", "sal_validade" => "Validade", "sal_qtde" => "Quant.", "psico_nome" => "Portaria"),
			"formato" => array("sal_validade" => "data", "sal_qtde" => "num")
		);
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("sal" => "saldo"), array("sal_lote", "sal_validade", "sal_qtde"))
				->join(array("pro" => "produto"), "pro.pro_codigo=sal.pro_codigo", "pro_nome")
				->join(array("psico" => "psicotropicos"), "psico.psico_codigo=pro.psico_codigo", "psico_nome")
				->where("sal.sal_qtde > 0")
				->where("sal.set_codigo=?", $set_codigo)
				->order("pro_nome");
				// die($where);
                return $where;
	}

	public function relTransferencias(&$dados, $set_codigo, $data_inicial=FALSE, $data_final=FALSE) {
		$dados->title = "Transferências por fornecedores";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("set_nome" => "Setor de Destino","mov_data" => "Data","pro_nome" => "Produto", "ite_quantidade" => "Quantidade"),
			"formato" => array("ite_quantidade" => "num","mov_data" => "data")
		);

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("mov" => "movimento"),"mov_data")				
				->join(array("ite" => "itens_movimento"), "ite.mov_codigo=mov.mov_codigo", "ite_quantidade" )
				->join(array("pro" => "produto"),"ite.pro_codigo=pro.pro_codigo", array("pro_nome"))				
				->join(array("set"=>"setor"),"set.set_codigo=mov.set_entrada","set_nome")
				->where("mov.mov_tipo = 'T'")
				->where("mov.set_saida=?",$set_codigo)
				->order(array("set_nome","mov_data","pro_nome"));

		if ($data_inicial)
			$where->where("mov.mov_data>= ?", $data_inicial);

		if ($data_final)
			$where->where("mov.mov_data <= ?", $data_final);
		//die($where);
		return $where;
	}
        public function relAnvisa(&$dados, $set_codigo, $data_inicial=FALSE, $data_final=FALSE,$pro_codigo=FALSE,$portaria=FALSE) {                
//		$dados->title = "Relatório Anvisa";
//		$dados->params = serialize($_POST);
                
                if ($data_inicial)
			$whereIni = " AND m2.mov_data >= '$data_inicial'";
		
		if ($data_final)
			$whereFim = " AND m2.mov_data <= '$data_final'";
		
		if ($set_codigo){
			$whereSet = " AND m2.set_saida = $set_codigo";
                }
                if($pro_codigo){
                    $wherePro = " AND p2.pro_codigo = $pro_codigo";
                }else{
                     $wherePro = " AND i2.pro_codigo = p.pro_codigo";
                }
                
		$dados->config = array(
			"th" => array("mov_data" => "Data","usu_nome" => "Nome","pro_nome" => "Produto","psico_nome" => "Portaria" ,"ite_quantidade" => "Quantidade","set_nome" => "Setor","usr_nome"=>"Médico","total"=>"Total"),
			"formato" => array("ite_quantidade" => "num","mov_data" => "data","total" => "num")
		);
                $where = "SELECT  psi.psico_nome,u.usu_nome,
				  p.pro_nome,
				  ite_quantidade,
				  m.mov_data,
				  set_nome,
				  CASE 
				   when 
					usr_nome is not null 
				   then
					usr_nome
				   when 
					med_nome is not null    
				   then
					med_nome
				   else 
					'Médico Desconhecido'        
				  END usr_nome ,
                                  
                                  (SELECT SUM(ite_quantidade) 
                                     FROM movimento m2
                                     JOIN itens_movimento i2 
                                       ON m2.mov_codigo =i2.mov_codigo
                                     JOIN usuario u2
                                       ON u2.usu_codigo = m2.usu_codigo
                                     JOIN produto p2
                                       ON p2.pro_codigo = i2.pro_codigo    
                                    WHERE mov_tipo = 'S' and 
                                            u2.usu_codigo is not null
                                            and p2.psico_codigo is not null
                                            $whereIni $whereFim $whereSet $wherePro) as total
		  from movimento m
		  join itens_movimento i
		   on i.mov_codigo = m.mov_codigo
		  join setor s
			on s.set_codigo = m.set_saida
		  join usuario u
			on u.usu_codigo = m.usu_codigo
		  join produto p
			on p.pro_codigo = i.pro_codigo
		  left join usuarios us
			on us.usr_codigo = m.med_codigo_interno
                  left join psicotropicos as psi
                        on psi.psico_codigo = p.psico_codigo
		  left join medico me
			on me.med_codigo = m.med_codigo_externo
			where mov_tipo = 'S' and 
		u.usu_codigo is not null
		and p.psico_codigo is not null";
                
                $where .= " AND pro_situacao <> ''";

		if ($data_inicial)
			$where .= " AND m.mov_data >= '$data_inicial'";
		
		if ($data_final)
			$where .= " AND m.mov_data <= '$data_final'";
		
		if ($set_codigo){
			$where .= " AND m.set_saida = $set_codigo";
                }
		if ($portaria){
			$where .= " AND psi.psico_codigo in ($portaria)";
                }
		if($pro_codigo){
                    $where .= " AND p.pro_codigo = $pro_codigo ORDER BY m.mov_data,pro_nome";
                }else{
                     $where .= " ORDER BY m.mov_data ,pro_nome";
                }
                

		return $where;
	}
        
        public function dadosProdRelAnalitico($set_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $pro_codigo=FALSE,$portaria=FALSE){
            // Validacoes de buscas
            if ($data_inicial){
                $dataImov = "AND m2.mov_data >= '$data_inicial'";                                                    
                $datafSaldoInicial = "AND mov_data < '$data_inicial'";
            }
            if ($data_final){
                $datafImov = "AND m2.mov_data <= '$data_final'";
            }
            if ($data_inicial == "" && $data_final == "") { 
                //echo "BVVV";
                // Pega a data do último movimento do setor e coloca saldo
                $datafSaldoInicial =  "AND mov_data < (SELECT 
                                                        mov_data
                                                      FROM
                                                        movimento AS mov
                                                      INNER JOIN 
                                                        itens_movimento AS ite ON mov.mov_codigo=ite.mov_codigo
                                                      WHERE
                                                        (set_entrada = $set_codigo OR set_saida = $set_codigo) AND
                                                        (ite.pro_codigo = p2.pro_codigo)
                                                      ORDER BY
                                                        mov_data ASC
                                                        LIMIT 1)";
            }
            
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("ite"=>"itens_movimento"),array("pro_codigo","ite_lote",
                                                                     "((SELECT 
                                                                            SUM(ite_quantidade)
                                                                     FROM 
                                                                            produto p2
                                                                     JOIN 
                                                                            itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                     JOIN 
                                                                            movimento m2 ON m2.mov_codigo = i2.mov_codigo
                                                                     WHERE
                                                                             1=1 
                                                                             $datafSaldoInicial
                                                                             AND (mov_tipo = 'E' or mov_tipo = 'T') 
                                                                             AND set_entrada = $set_codigo
                                                                             AND ite.pro_codigo = p2.pro_codigo and ite.ite_lote = i2.ite_lote) -
                                                                    (SELECT 
                                                                            COALESCE(SUM(ite_quantidade),0) 
                                                                    FROM 
                                                                            produto p2
                                                                    JOIN 
                                                                            itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                    JOIN
                                                                            movimento m2 ON i2.mov_codigo = m2.mov_codigo
                                                                    WHERE 
                                                                            1=1
                                                                            $datafSaldoInicial
                                                                            AND (mov_tipo = 'S' OR mov_tipo = 'T')
                                                                            AND set_saida = $set_codigo
                                                                            AND ite.pro_codigo = p2.pro_codigo and ite.ite_lote = i2.ite_lote)) as saldo_inicial",
                                                                      "(SELECT 
                                                                                SUM(ite_quantidade)
                                                                         FROM 
                                                                                produto p2
                                                                         JOIN 
                                                                                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                         JOIN 
                                                                                movimento m2 ON m2.mov_codigo = i2.mov_codigo
                                                                         WHERE
                                                                                 1=1
                                                                                 $dataImov
                                                                                 $datafImov
                                                                                 AND (mov_tipo = 'E' or mov_tipo = 'T') 
                                                                                 AND set_entrada = $set_codigo
                                                                                 AND ite.pro_codigo = p2.pro_codigo and ite.ite_lote = i2.ite_lote) as saldo_entrada",
                                                                        "(SELECT 
                                                                            COALESCE(SUM(ite_quantidade),0) 
                                                                        FROM 
                                                                            produto p2
                                                                        JOIN 
                                                                                itens_movimento i2 ON i2.pro_codigo = p2.pro_codigo
                                                                        JOIN
                                                                                movimento m2 ON i2.mov_codigo = m2.mov_codigo
                                                                        WHERE 
                                                                                1=1
                                                                                $dataImov
                                                                                $datafImov
                                                                                AND (mov_tipo = 'S' OR mov_tipo = 'T')
                                                                                AND set_saida = $set_codigo
                                                                                AND ite.pro_codigo = p2.pro_codigo and ite.ite_lote = i2.ite_lote
                                                                                AND (mov_saida <> 'R' AND mov_saida <> 'S-PE')) as saldo_saida",
                                                                        "(SELECT 
                                                                             SUM(ite_quantidade) 
                                                                          FROM 
                                                                              itens_movimento ite2
                                                                          JOIN
                                                                              movimento m2 ON ite2.mov_codigo = m2.mov_codigo
                                                                          WHERE 
                                                                              ite.pro_codigo = ite2.pro_codigo
                                                                              $dataImov
                                                                              $datafImov
                                                                              AND set_saida = $set_codigo
                                                                              and ite.ite_lote = ite2.ite_lote
                                                                              AND (mov_tipo = 'S')
                                                                              AND (mov_saida = 'S-PE' OR mov_saida = 'R')) as saldo_perda"))
                        ->join(array("prod"=>"produto"),"ite.pro_codigo=prod.pro_codigo",array("pro_nome"))
                        ->join(array("mov"=>"movimento"),"ite.mov_codigo=mov.mov_codigo","")
                        ->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=prod.psico_codigo",array("psico_nome"))
                        ->where("prod.psico_codigo IS NOT NULL")
                        ->where("mov.set_entrada = '".$set_codigo."' OR mov.set_saida = '".$set_codigo."'")
                        ->order("pro_nome");
            // Validacoes de buscas
            if ($data_inicial)
                $sql->where("mov.mov_data >= '$data_inicial'");
            if ($data_final)
                $sql->where("mov.mov_data <= '$data_final'");
            if($pro_codigo)
                $sql->where("prod.pro_codigo =?", $pro_codigo);
              if($portaria) 
                $sql->where("psi.psico_codigo in ($portaria)");
              
            // die($sql);
             return $this->fetchAll($sql);
        }
        
        public function dadosPacienteRelAnalitico($pro_codigo,$data_inicial,$data_final,$set_codigo,$portaria=FALSE,$ite_lote=FALSE){
            $sql = $this->select(FALSE)
           				->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("mov"=>"movimento"),array("usu_codigo","mov_codigo","mov_data","set_entrada","set_saida","mov_tipo AS mov_tipo_imp","mov_saida","mov_nr_nota","mov_observacao"))
                        ->join(array("ite"=>"itens_movimento"),"mov.mov_codigo=ite.mov_codigo",array("ite_quantidade","ite_cod_receita","ite_codigo","ite_lote"))
                        ->joinLeft(array("usu"=>"usuario"),"mov.usu_codigo=usu.usu_codigo",array("usu_prontuario","usu_nome"))
                        ->joinLeft(array("usrs"=>"usuarios"),"mov.usr_codigo=usrs.usr_codigo",array("usr_nome"))
                        ->joinLeft(array("forn"=>"fornecedor"),"mov.for_codigo=forn.for_codigo",array("for_nome"))
                        ->joinLeft(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo")
                        ->joinLeft(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo",array("psico_nome"))
                        ->where("ite.pro_codigo =?",$pro_codigo)
                        ->where("set_entrada = $set_codigo OR set_saida = $set_codigo");
                        // Validacoes de buscas
                        if ($data_inicial)
                            $sql->where("mov.mov_data >= '$data_inicial'");
                        if ($data_final)
                            $sql->where("mov.mov_data <= '$data_final'");
                        
                       if($portaria) 
                            $sql->where("psi.psico_codigo = '$portaria'");

                       if($ite_lote) 
                            $sql->where("ite.ite_lote = '$ite_lote'");
                       
            $sql->order(array("mov_data","ite_codigo","usu_nome"));
            
              // die($sql);
            return $this->fetchAll($sql);
        }
        
        
	/**
	 * Atualiza todas as tabelas do sistema que estão vinculadas a um paciente duplicado para um único paciente
	 * depois remove-os.
	 * @param int $correto
	 * @param array $duplicados 
	 * @return array quantos registros foram atualizados e quantos foram removidos por tabela
	 */
	public function removerDuplicacoes($correto, $duplicados){
		Zend_Registry::get("logger")->log(array($correto,$duplicados), Zend_Log::INFO);
		
		$out = array();
		$tbCar = new Application_Model_Carteirinha();
		$tbCot = new Application_Model_CotaSetor();
		$tbEve = new Application_Model_EventoProduto();
		$tbInv = new Application_Model_InventarioProduto();
		$tbIte = new Application_Model_MovimentoItens();
		$tbiReq = new Application_Model_ItensRequisicao();
		$tbLei = new Application_Model_LeitoGradeItens();
		$tbSet = new Application_Model_ProdutoSetor();
		$tbPro = new Application_Model_ProgramaProduto();
		$tbReq = new Application_Model_RequisicaoCompraProduto();
		$tbSal = new Application_Model_Saldo();
		$tbVac = new Application_Model_VacinaUsuario();
                $tbIteBkp = new Application_Model_ItensMovimentoBkp();
	

		
		
		$this->getAdapter()->beginTransaction();
		
		try{
			$out['car'] = $tbCar->atualizarPro($duplicados, $correto);
			$out['cot'] = $tbCot->atualizarPro($duplicados, $correto);
			$out['eve'] = $tbEve->atualizarPro($duplicados, $correto);
			$out['inv'] = $tbInv->atualizarPro($duplicados, $correto);
			$out['ite'] = $tbIte->atualizarPro($duplicados, $correto);
			$out['ireq'] = $tbiReq->atualizarPro($duplicados, $correto);
			$out['lei'] = $tbLei->atualizarPro($duplicados, $correto);
			$out['set'] = $tbSet->atualizarPro($duplicados, $correto);
			$out['pro'] = $tbPro->atualizarPro($duplicados, $correto);
			$out['req'] = $tbReq->atualizarPro($duplicados, $correto);
			$out['sal'] = $tbSal->atualizarPro($duplicados, $correto);
			$out['vac'] = $tbVac->atualizarPro($duplicados, $correto);
                        $out['bkp'] = $tbIteBkp->atualizarPro($duplicados, $correto);
				
			$this->getAdapter()->commit();
			
		} catch (Exception $e){
			$this->getAdapter()->rollBack();
			Zend_Registry::get("logger")->log($e->getMessage(), Zend_Log::INFO);
			return false;
		}
		
		$removidos = $this->remover($duplicados);			
		
                
		return array(array_sum($out),$removidos);
	}
	
	/**
	 * Recebe um array de usu_codigo e remove todos
	 * @param array $usu_codigo
	 * @return int Número de linhas removidas
	 */
	public function remover($pro_codigo){
                
		$where = $this->select()->where("pro_codigo IN (?)", $pro_codigo)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];
		
		//die($where);
		return $this->delete($where);
	}
        
        public function getProduto($pro_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("pro"=>"produto"))
                          ->join(array("umed"=>"unidmedida"),"umed.umed_codigo=pro.umed_codigo","umed_nome")
                          ->where("pro_codigo=$pro_codigo");
            return $this->fetchRow($where);
        }
        
        
        public function getProdutoComEstoque($pro_codigo=FALSE,$set_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("pro"=>"produto"))
                          ->join(array("umed"=>"unidmedida"),"umed.umed_codigo=pro.umed_codigo","umed_nome")
                          ->join(array("sal"=>"saldo"),"sal.pro_codigo=pro.pro_codigo","")
                          ->where("sal_qtde > 0")
                          ->where("set_codigo = $set_codigo")
                          ->where("sal.pro_codigo=$pro_codigo");
            //die($where);
            return $this->fetchRow($where);
        }
        
        public function getRankingProdutos($set_codigo=FALSE,$quantidade=FALSE,$data_inicial=FALSE,$data_final=FALSE,$pros_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("p"=>"produto"),"pro_nome")
                          ->join(array("im"=>"itens_movimento"),"im.pro_codigo=p.pro_codigo",array("quantidade"=>"sum(ite_quantidade)"))
                          ->join(array("m"=>"movimento"),"m.mov_codigo=im.mov_codigo","")
                          ->where("mov_tipo='S'")
                          ->group("pro_nome")
                          ->having("sum(ite_quantidade) > 0")
                          ->order("2 desc");
            
            if($set_codigo)
                $where->where("set_saida=$set_codigo");
            
            if($quantidade)
                $where->limit($quantidade);
            
            if($data_inicial)
                $where->where("mov_data >= '$data_inicial'");
            
            if($data_final)
                $where->where("mov_data <= '$data_final'");
            
            if($pros_codigo)
                $where->where("pros_codigo=$pros_codigo");
            
            
            return $this->fetchAll($where);
        }
        
        public function relRelacaoNotificacoesReceita(&$dados, $data_inicial=FALSE, $data_final=FALSE, $portaria=FALSE) {
           // die("CHEGO");
		$dados->title = "Relação de Notificações de Receita";
		$dados->params = serialize($_POST);
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("rec" => "receita"), array("rec_codigo","to_char(rec_data, 'dd/mm/yyyy') as rec_data"))
				->join(array("irec"=>"itemreceita"),"irec.rec_codigo = rec.rec_codigo","irec_quantidade")
				->join(array("pro"=>"produto"),"irec.pro_codigo = pro.pro_codigo",array("pro_codigo_dcb","pro_descricao_dcb","pro_nome","pro_apresentacao_concentracao"))
				->join(array("ate"=>"atendimento"),"rec.ate_codigo = ate.ate_codigo")
				->join(array("usr"=>"usuarios"),"usr.usr_codigo = ate.med_codigo",array("usr_nome", "usr_num_conselho"))
				->joinLeft(array("ite"=>"itens_movimento"),"ite.pro_codigo=irec.pro_codigo and ite.ite_cod_receita = rec.rec_codigo","ite_quantidade")
				->join(array("psi"=>"psicotropicos"),"psi.psico_codigo = pro.psico_codigo","psico_nome")
				->group(array("rec.rec_codigo","rec.rec_data","irec.irec_quantidade","pro.pro_codigo_dcb","pro.pro_descricao_dcb","pro.pro_nome","pro.pro_apresentacao_concentracao","usr.usr_nome", "usr.usr_num_conselho","ite.ite_quantidade", "ate.ate_codigo", "psico_nome"))
				->order("rec_data");

		if ($data_inicial){
			$where->where("rec.rec_data>= ?", $data_inicial);
		}

		if ($data_final){
			$where->where("rec.rec_data <= ?", $data_final);
		}

		if ($portaria){
			$where->where("psi.psico_codigo in ($portaria)");

        }
        return $this->fetchAll($where);
	}

	public function getFracionamentoMinimo($pro_codigo) {
        $where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("p"=>"produto"), "pro_frmmin")
			->where("pro_codigo = $pro_codigo");
		// echo '<pre>';print_r($where);die();
		return $this->fetchRow($where);
    }
 	
 	public function quantidadeDePacientesAtendidosPorSubGrupo($recebeCodigoSubGrupo, $recebeDataInicio, $recebeDataFim){
 		// echo "<pre>";var_dump($recebeCodigoSubGrupo);die();
 		if ($recebeCodigoSubGrupo == 9999999) {

	 		$sql = $this->getDefaultAdapter()->query(
	 			"SELECT  pro.pro_nome, ite_mov.ite_quantidade, mov.mov_tipo, pro_sub.pros_descricao, pro.pro_codigo FROM itens_movimento AS ite_mov
					INNER JOIN movimento AS mov
						ON ite_mov.mov_codigo = mov.mov_codigo
					INNER JOIN produto AS pro
						ON pro.pro_codigo = ite_mov.pro_codigo
					INNER JOIN produto_subgrupo AS pro_sub
						ON pro.pros_codigo = pro_sub.pros_codigo
				WHERE mov.mov_tipo = 'S' and pro.pro_nome <> '' 
					and mov.mov_data BETWEEN '$recebeDataInicio' AND  '$recebeDataFim'  
						order by pro.pro_codigo desc
				"
	 		)->fetchAll();

	 		return $sql;
 		} else{
 			$sql = $this->getDefaultAdapter()->query(
	 			"SELECT  pro.pro_nome, ite_mov.ite_quantidade, mov.mov_tipo, pro_sub.pros_descricao, pro.pro_codigo FROM itens_movimento AS ite_mov
					INNER JOIN movimento AS mov
						ON ite_mov.mov_codigo = mov.mov_codigo
					INNER JOIN produto AS pro
						ON pro.pro_codigo = ite_mov.pro_codigo
					INNER JOIN produto_subgrupo AS pro_sub
						ON pro.pros_codigo = pro_sub.pros_codigo
				WHERE mov.mov_tipo = 'S' and pro_sub.pros_codigo = $recebeCodigoSubGrupo and pro.pro_nome <> '' 
					and mov.mov_data BETWEEN '$recebeDataInicio' AND  '$recebeDataFim'  
						order by pro.pro_codigo desc
				"
	 		)->fetchAll();

 		}

 		return $sql;
	}    
}