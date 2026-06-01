<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_PMA extends Elotech_Db_Table_Abstract {

	protected $_name = 'pma2';
	protected $_primary = 'pma_codigo';
	protected $_dependentTables = array();

	public function salvar($data) {

		return parent::salvar($data);
	}

	public function editar($pma_codigo, $digitado, $original) {
		$tbUsr = new Application_Model_Usuarios();
		$tbPMAR = new Application_Model_PMARelacao();
		$tbPMAA = new Application_Model_PMAAtributos();
		$attr = $tbPMAA->getChaveAtributos();
		$usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;

		foreach ($digitado as $chave => $valor) {

			if ($valor == $original[$chave])
				continue;

			$dados = $tbPMAR->fetchRow("pma_codigo=$pma_codigo AND pmaa_codigo=" . $attr[$chave]);
			$dados->pmar_valor_digitado = $valor;
			$dados->usr_codigo = $usr_codigo;
			$dados->pmar_data_digitado = date("Y-m-d H:i:s");
			$dados->save();
		}
	}

	public function delPma($pma_codigo) {
		$tbPMAR = new Application_Model_PMA();
		$dados = $tbPMAR->delete("pma_codigo=$pma_codigo");
	}	


	
	public function criar($mes, $unidade=0, $area=0) {
		// transforma em arrays
		$uni_array = $this->verifciarSeVeio0($unidade, new Application_Model_Unidade(), "uni_codigo");
                                                
		$area_array = $this->verifciarSeVeio0($area, new Application_Model_Area, "area_codigo");
		$tbFun = new Application_Model_Funcoes();
		list($data_inicial, $data_final, $mes, $ano) = $tbFun->getPrimeiroEUltimoDia($mes);

		$tbPMAR = new Application_Model_PMARelacao();
		$tbPMAA = new Application_Model_PMAAtributos();
		$attr = $tbPMAA->getChaveAtributos();

		$retorno = array();


		$tbUsr = new Application_Model_Usuarios();
		$usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
		foreach ($uni_array as $uni_codigo) { // para cada unidade
			foreach ($area_array as $area_codigo) { // para cada area
				// criar um registro na tabela PMA2
				$pma = array(
					"pma_seguimento" => "01",
					"uni_codigo" => $uni_codigo,
					"area_codigo" => $area_codigo,
					"pma_mes" => $data_inicial,
					"usr_codigo" => $usr_codigo
				);

				$this->getDefaultAdapter()->beginTransaction();
                                

				try {
					$pma_codigo = $this->salvar($pma);
					$retorno []= $pma_codigo;

					// dados do relatório (calculado)
					$dadosPma = $this->getDadosPMA($data_inicial, $data_final, $area_codigo, $uni_codigo);

					$relacao = array(
						"pma_codigo" => $pma_codigo
					);

					foreach ($dadosPma as $itemPma => $valor) { // para cada atributo
						if (!isset($attr[$itemPma]))
							continue; // totais não são salvos

						$relacao['pmaa_codigo'] = $attr[$itemPma];
						$relacao['pmar_valor_sistema'] = $valor;
						$relacao['pmar_valor_digitado'] = $valor;
						$tbPMAR->salvar($relacao);
					}

					$this->getDefaultAdapter()->commit();
				} catch (Exception $e) {
					$this->getDefaultAdapter()->rollBack();
				}
			}
		}
		
		return $retorno;
	}

	/**
	 *
	 * @param int $codigo
	 * @param Zend_Db_Table_Abstract $obj
	 * @param string $chave_primaria 
	 */
	private function verifciarSeVeio0($codigo, $obj, $chave_primaria) {
		if ($codigo == 0) {
			$retorno = array();
			$tudo = $obj->fetchAll();
			foreach ($tudo as $item)
				$retorno [] = $item->$chave_primaria;

			return $retorno;
		}

		return array($codigo);
	}

	public function getDadosPMA($data_inicial, $data_final, $area_codigo, $uni_codigo) {
		$tbAte = new Application_Model_Atendimento();
		$dados = array(
			"PROCEDIMENTOS.REUNIOES" => 0,
			"PROCEDIMENTOS.AT" => $tbAte->getTotalDeAtendimentoEspecificoParaAcidendeDeTrabalho($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"MARCADORES.RN_2500" => $tbAte->getTotalDeRecemNascidoComPesoMenorQue2500($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"MARCADORES.GRAVIDEZ_20" => $tbAte->getTotalDeGravidezEmMenorDe20Anos($data_inicial, $data_final, $area_codigo, $uni_codigo),
			'MARCADORES.HOSP_PNEUMONIA' => 0,
			'MARCADORES.HOSP_DESIDRATACAO' => 0,
			'MARCADORES.HOSP_ALCOOL' => 0,
			'MARCADORES.HOSP_DIABETES' => 0,
			'MARCADORES.HOSP_QUALQUER' => 0,
			'MARCADORES.HOSP_PSIQUIATRICO' => 0,
			'MARCADORES.OBITOS_MULHERES' => $tbAte->getTotalDeObtiosEmMulheresDe10A49Anos($data_inicial, $data_final, $area_codigo, $uni_codigo),
			'MARCADORES.OBITOS_ADOLESCENTES' => $tbAte->getTotalDeObitosEmAdolescentesPorViolencia($data_inicial, $data_final, $area_codigo, $uni_codigo)
		);

		$dados = array_merge(
				$tbAte->getAtendimentosPorFaixaEtaria($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalPorTipoDeAtendimento($data_inicial, $data_final, $area_codigo, $uni_codigo),
                        
				$tbAte->getTotalDeVisitasDociliares($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalDeEncaminhamentos($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalDeProcedimentosNoAtendimento($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalDeExamesComplementaresSolicitados($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalMarcadoresPorCid($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$tbAte->getTotalDeObitosEmMenoresDe1Ano($data_inicial, $data_final, $area_codigo, $uni_codigo),
				$tbAte->getTotalDeAtendimentoIndividualEnfermeiro($data_inicial, $data_final, $area_codigo, $uni_codigo), 
				$dados
		);

                                        

		$tbUni = new Application_Model_Unidade();
		$tbArea = new Application_Model_Area();

		list(, $mes, $ano) = explode("-", $data_inicial);
		$tbConfig = new Application_Model_Configuracao();

		$dados['CABECALHO.MUNICIPIO'] = $tbConfig->getConfig("CID_CODIGO_IBGE");
		$dados['CABECALHO.SEGMENTO'] = "01";
		$dados['CABECALHO.UNIDADE'] = $tbUni->find($uni_codigo)->current()->uni_cnes;
		$dados['CABECALHO.AREA'] = $tbArea->find($area_codigo)->current()->area_desc;
		$dados['CABECALHO.MES'] = $mes;
		$dados['CABECALHO.ANO'] = $ano;

		return $dados;
	}

	public function filtrar($limite=15, $mes=FALSE, $uni_codigo=FALSE, $area_codigo=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("pma" => "pma2"), array("pma_codigo", "pma_data", "pma_mes"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pma.usr_codigo", "usr_nome")
				->join(array("uni" => "unidade"), "uni.uni_codigo=pma.uni_codigo", "uni_desc")
				->join("area", "area.area_codigo=pma.area_codigo", "area_desc")
				->order(array("pma.pma_mes desc"));

		if ($uni_codigo)
			$where->where("uni.uni_codigo=?", $uni_codigo);

		if ($area_codigo)
			$where->where("area.area_codigo=?", $area_codigo);

		if ($mes) {
			$tbFun = new Application_Model_Funcoes();
			list($data_inicial, $data_final, $mes, $ano) = $tbFun->getPrimeiroEUltimoDia($mes);
			$where->where("pma.pma_mes=?", $data_inicial);
		}

		if ($limite)
			$where->limit($limite);

		return $this->fetchAll($where);
	}

	public function carregarPma($pma_codigo, $incluirValorCalculado=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from("pma2", array("pma_seguimento", "pma_mes", "pma_seguimento"))
				->join(array("uni" => "unidade"), "uni.uni_codigo=pma2.uni_codigo", array("uni_desc", "uni_cnes"))
				->join("area", "area.area_codigo=pma2.area_codigo", "area_desc")
				->join(array("pmar" => "pma2_relacao"), "pmar.pma_codigo=pma2.pma_codigo", array("pmar_valor_sistema", "pmar_valor_digitado", "pmar_data_digitado"))
				->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=pmar.usr_codigo", "usr_nome")
				->join(array("pmaa" => "pma2_atributos"), "pmaa.pmaa_codigo=pmar.pmaa_codigo", "pmaa_chave")
				->where("pma2.pma_codigo=?", $pma_codigo);

		$itens = $this->fetchAll($where);
		$dados = array();
		$calc = array();
		$info = array();
		foreach ($itens as $item) {
			$dados[$item->pmaa_chave] = $item->pmar_valor_digitado;
			$calc[$item->pmaa_chave] = $item->pmar_valor_sistema;
			$info[$item->pmaa_chave] = (object) array(
						"usr_nome" => $item->usr_nome,
						"data" => $item->pmar_data_digitado
			);
		}

		// somar totais
		$dados['CONSULTA.SUBTOTAL'] = $dados['CONSULTA.MENOR_DE_1_ANO'] + $dados['CONSULTA.DE_1_A_4'] + $dados['CONSULTA.DE_5_A_9'] + $dados['CONSULTA.DE_10_A_14'] + $dados['CONSULTA.DE_15_A_19'] + $dados['CONSULTA.DE_20_A_39'] + $dados['CONSULTA.DE_40_A_49'] + $dados['CONSULTA.DE_50_A_59'] + $dados['CONSULTA.60_OU_MAIS'];
		$dados['CONSULTA.TOTAL'] = $dados['CONSULTA.FORA_DA_AREA'] + $dados['CONSULTA.SUBTOTAL'];

		$dados['VISITAS.TOTAL'] = $dados['VISITAS.MEDICO'] + $dados['VISITAS.ENFERMEIRO'] + $dados['VISITAS.SUPERIOR'] + $dados['VISITAS.MEDIO'] + $dados['VISITAS.ACS'];


		list(, $mes, $ano) = explode("-", $item->pma_mes);

		$tbConfig = new Application_Model_Configuracao();

		$dados['CABECALHO.MUNICIPIO'] = $tbConfig->getConfig("CID_CODIGO_IBGE");
		$dados['CABECALHO.SEGMENTO'] = $item->pma_seguimento;
		$dados['CABECALHO.UNIDADE'] = $item->uni_cnes;
		$dados['CABECALHO.AREA'] = $item->area_desc;
		$dados['CABECALHO.MES'] = $mes;
		$dados['CABECALHO.ANO'] = $ano;

		if ($incluirValorCalculado)
			return array($dados, $calc, $info);

		return $dados;
	}

}
