<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ControleFracionadoReserva extends Elotech_Db_Table_Abstract {

	protected $_name = 'controlefracionado_reserva';
	protected $_primary = 'cfr_codigo';

	/**
	 * Tipos de reserva
	 */
	const LEITO_GRADE = "lgra_codigo";

	public function salvar(array $data) {

		$this->notEmpty(array("cont_codigo", "cfr_quantidade"), $data);
		$this->maiorQueZero(array("cfr_quantidade"), $data);

		// Descomentar quando houver mais de um tipo de reserva
		//$this->peloMenosUm(array(self::LEITO_GRADE), $data);
		return parent::salvar($data);
	}

	/**
	 * Retorna os produtos (lote/validade/qtd) que estão na tabela de reserva de medicamentos
	 * @param string $tipo Tipo da reserva (para quê é essa reserva?)
	 * @param int $codigo código do $tipo da resrva
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getReservas($tipo, $codigo) {
		if (!$tipo || !$codigo)
			return FALSE;

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cfr" => "controlefracionado_reserva"), array("cfr_codigo", "cfr_quantidade"))
				->join(array("cont" => "controlefracionado"), "cont.cont_codigo=cfr.cont_codigo", "cont_codigo")
				->join(array("ite" => "itens_movimento"), "ite.ite_codigo=cont.ite_codigo", array("ite_lote", "ite_validade"))
				->join(array("pro" => "produto"), "pro.pro_codigo=ite.pro_codigo", array("pro_codigo", "pro_nome"))
				->where("$tipo = ?", $codigo)
				->order("pro_codigo");
		return $this->fetchAll($where);
	}

	public function reservasToTable($arr) {
		$out = array();
		foreach ($arr as $item) {
			if (!isset($out[$item->pro_codigo])) {
				$out[$item->pro_codigo] = array(
					"pro_nome" => $item->pro_nome,
					"cfr" => array()
				);
			}

			$out[$item->pro_codigo]['cfr'][$item->cont_codigo] = array(
				"pro_lote" => $item->ite_lote,
				"pro_validade" => $item->ite_validade,
				"total" => $item->cfr_quantidade
			);
		}

		return $out;
	}

	/**
	 * Retira produtos da controleFracionado e adiciona para reserva
	 * ATENÇÂO: os produtos precisam estar fracionados, pois o método não fraciona
	 * @param string $tipo Tipo da reserva (para quê é essa reserva?)
	 * @param int $codigo código do $tipo da resrva
	 * @param array $controleFracionado chave-valor: cont_codigo=>quantidade
	 * @return bool TRUE em caso de sucesso. 
	 */
	public function addParaReserva($tipo, $codigo, $controleFracionado,$usr_codigo) {
		$tbCont = new Application_Model_ControleFracionado();
		foreach ($controleFracionado as $cont_codigo => $quantidade) {
			$dados = array(
				"cont_codigo" => $cont_codigo,
				"cfr_quantidade" => $quantidade,
				$tipo => $codigo
			);

			// Coloca na reserva
			$this->salvar($dados);

			// retira do Cont
			$tbCont->dispensar($cont_codigo, $quantidade);
		}

		return TRUE;
	}

	/**
	 * Apagar deve ser usado quando a reserva for consumida ou DEPOIS de ser devolvida
	 * @param string $tipo
	 * @param int $codigo
	 * @param int $cont_codigo
	 * @return bool TRUE: sucesso. FALSE: não encontrou a reserva 
	 */
	public function apagarReserva($tipo, $codigo, $cont_codigo) {
		$reserva = $this->fetchRow("$tipo=$codigo AND cont_codigo=$cont_codigo");
		if ($reserva) {
			$reserva->delete();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Devolver é quando a fração não for utilizada, e deve voltar para estoque (controleFracionado)
	 * @param string $tipo
	 * @param int $codigo
	 * @return bool 
	 */
	public function devolver($tipo, $codigo) {
		$tbCont = new Application_Model_ControleFracionado();
		$resevas = $this->fetchAll("$tipo=$codigo");
		Zend_Registry::get("logger")->log("Devolvendo: $tipo=$codigo", Zend_Log::INFO);

		$this->getAdapter()->beginTransaction();

		try {
			foreach ($resevas as $item) {
				$tbCont->devolverFracao($item->cont_codigo, $item->cfr_quantidade);
				$this->apagarReserva($tipo, $codigo, $item->cont_codigo);
				Zend_Registry::get("logger")->log("cont_codig: ".$item->cont_codigo, Zend_Log::INFO);
			}
			$this->getAdapter()->commit();
		} catch (Exception $exc) {
			unset($exc); // sonar
			$this->getAdapter()->rollBack();
		}

		return TRUE;
	}

}
