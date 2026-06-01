<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_LeitoDispensacao extends Elotech_Db_Table_Abstract {

	protected $_name = 'leito_dispensacao';
	protected $_primary = 'ldis_codigo';
	protected $_referenceMap = array(
		'Grade' => array(
			'columns' => 'lgra_codigo',
			'refTableClass' => 'Application_Model_LeitoGrade',
			'refColumns' => 'lgra_codigo'
		)
	);

	public function salvar(array $data) {

		$this->notEmpty(array("lgra_codigo"), $data);
		$this->valoresPadrao($data);

		return parent::salvar($data);
	}

	private function valoresPadrao(&$data) {
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();

		if (empty($data['usr_codigo'])) {
			$data['usr_codigo'] = $usr->usr_codigo;
		}

		if (empty($data['ldis_datahora'])) {
			$data['ldis_datahora'] = date('Y-m-d H:i:s');
		}
	}

	/**
	 * Recebe o lgra_codigo, e um array (cont_codigo=>qtd) para fazer a dispensação.
	 * $num é a validação: só é válido, se essa for a ($num+1)ª dispensação
	 * @param int $lgra_codigo
	 * @param array $reservas
	 * @param int $num 
	 */
	public function dispensar($lgra_codigo, $reservas, $num,$usr_codigo=FALSE) {
		$tbLGra = new Application_Model_LeitoGrade();
		$tbLID = new Application_Model_LeitoDispensacaoItens();
		$tbCFR = new Application_Model_ControleFracionadoReserva();

		$grade = $tbLGra->fetchRow("lgra_codigo=$lgra_codigo");
		$total_itens = $grade->findDependentRowset("Application_Model_LeitoDispensacao")->count();

		if ($total_itens != $num)
			throw new Zend_Validate_Exception("O paciênte já tomou este medicamento!", 999);

		$dados = array(
			"lgra_codigo" => $lgra_codigo,
			"usr_codigo" => $usr_codigo
		);

		$this->getAdapter()->beginTransaction();

		try {
			$ldis_codigo = $this->salvar($dados); // salva a dispensação

			foreach ($reservas as $cont_codigo => $quantidade) {
				$dadosItens = array(
					"ldis_codigo" => $ldis_codigo,
					"cont_codigo" => $cont_codigo,
					"lid_quantidade" => $quantidade
				);
				$tbLID->salvar($dadosItens); // salva os itens
				$tbCFR->apagarReserva(Application_Model_ControleFracionadoReserva::LEITO_GRADE, $lgra_codigo, $cont_codigo); // apaga a reserva
			}

			// calcular o proximo
			$tbLGra->calcularProximo($lgra_codigo);

			$this->getAdapter()->commit();
		} catch (Exception $exc) {
			$this->getAdapter()->rollBack();
			throw $exc;
		}
	}

	public function getHistorico($lgra_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ldis" => "leito_dispensacao"), array("ldis_codigo", "ldis_datahora"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=ldis.usr_codigo", "usr_nome")
				->join(array("lid" => "leito_itens_dispensacao"), "lid.ldis_codigo=ldis.ldis_codigo", "lid_quantidade")
				->join(array("cont" => "controlefracionado"), "cont.cont_codigo=lid.cont_codigo", "")
				->join(array("ite" => "itens_movimento"), "ite.ite_codigo=cont.ite_codigo", array("ite_lote", "ite_validade"))
				->join(array("pro" => "produto"), "pro.pro_codigo=ite.pro_codigo", "pro_nome")
				->where("ldis.lgra_codigo=?",$lgra_codigo)
				->order("ldis_datahora DESC","ldis.ldis_codigo");
		
		return $this->fetchAll($where);
	}

}
