<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_LeitoGrade extends Elotech_Db_Table_Abstract {

	protected $_name = 'leito_grade';
	protected $_primary = 'lgra_codigo';
	protected $_dependentTables = array('LeitoGradeItens');
	protected $_referenceMap = array(
		'Paciente' => array(
			'columns' => 'usu_codigo',
			'refTableClass' => 'Application_Model_Usuario',
			'refColumns' => 'usu_codigo'
		),
		'Leito' => array(
			'columns' => 'lei_codigo',
			'refTableClass' => 'Application_Model_Leito',
			'refColumns' => 'lei_codigo'
		)
	);

	const ATIVO = 1;
	const CONCLUIDO = 2;
	const CANCELADO = 0;

	public function salvar(array $data) {

		$this->valoresPadrao($data);
		$this->notEmpty(array("lgra_intervalo", "lgra_repeticoes"), $data);
		$this->emptyToUnset($data);

		//echo "<pre>".print_r($data,1);exit;

		return parent::salvar($data);
	}

	public function valoresPadrao(&$data) {
		if (empty($data['lgra_data']))
			$data['lgra_data'] = date("Y-m-d");

		if (empty($data['lgra_hora']))
			$data['lgra_hora'] = date("H:i");

		if (empty($data['usu_codigo']) && $data['lei_codigo']) {
			$tbLeito = new Application_Model_Leito();
			$data['usu_codigo'] = $tbLeito->buscar($data['lei_codigo'])->usu_codigo;
		}

		if (empty($data['lei_codigo']) && $data['usu_codigo']) {
			$tbLeito = new Application_Model_Leito();
			$data['lei_codigo'] = $tbLeito->buscar(FALSE, $data['usu_codigo'])->lei_codigo;
		}
	}

	/**
	 * Calcula quando será a proxima dipensação
	 * Se não houver mais, o método irá chamar o método 'concluir'
	 * @see Application_Model_LeitoGrade::concluir
	 * @param int $lgra_codigo
	 * @return bool 
	 */
	public function calcularProximo($lgra_codigo) {
		$grade = $this->fetchRow("lgra_codigo=$lgra_codigo");
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from("leito_dispensacao")
				->order("ldis_datahora DESC");
		//die($where);
		$itens = $grade->findDependentRowset("Application_Model_LeitoDispensacao", "Grade", $where);

		$total = $itens->count();

		if ($total == $grade->lgra_repeticoes) {
			$this->alterarStatus($grade, self::CONCLUIDO);
			return TRUE;
		}

		$ultimo = $itens->current();

		$dh = explode(" ", $ultimo->ldis_datahora);
		list($y, $m, $d) = explode("-", $dh[0]);
		list($h, $i, ) = explode(":", $dh[1]);
		$mkProximo = mktime($h + $grade->lgra_intervalo, $i, 0, $m, $d, $y);

		$grade->lgra_proximo = date("Y-m-d H:i:s", $mkProximo);
		$grade->save();
		return TRUE;
	}

	public function cancelar($lgra_codigo) {
		$grade = $this->find($lgra_codigo)->current();

		// se houver medicamentos reservados: devolver
		$reservados = $this->buscarRerservas($lgra_codigo);
		if ($reservados->count()) {
			$tbCFR = new Application_Model_ControleFracionadoReserva();
			$tbCFR->devolver(Application_Model_ControleFracionadoReserva::LEITO_GRADE, $lgra_codigo);
		}

		return $this->alterarStatus($grade, self::CANCELADO);
	}

	/**
	 * Altera o status da grade 
	 * @param Zend_Db_Table_Row_Abstract $grade
	 * @param int $status
	 * @return bool
	 */
	public function alterarStatus($grade, $status) {
		$grade->lgra_status = $status;
		$grade->save();
		return TRUE;
	}

	/**
	 * Salva uma grade e os itens
	 * @param array $arr
	 * @return int lgra_codigo 
	 */
	public function salvarFromArray($arr) {
		$this->getAdapter()->beginTransaction();
		//echo "<pre>".print_r($arr,1);exit;
		try {
			$dados = array(
				"lgra_codigo" => $arr['lgra_codigo'],
				"lgra_intervalo" => $arr['lgra_intervalo'],
				"lgra_repeticoes" => $arr['lgra_repeticoes'],
				"lgra_data" => $arr['lgra_data'],
				"lgra_hora" => $arr['lgra_hora'],
				"lgra_proximo" => $arr['lgra_proximo'],
				"io_codigo" => $arr['io_codigo']
			);

			$lgra_codigo = $this->salvar($dados);
			$tbLIG = new Application_Model_LeitoGradeItens();

			$produtos = $arr['pro_codigo'];
			$dados = array("lgra_codigo" => $lgra_codigo);
			foreach ($produtos as $pro_codigo => $lig_quantidade) {
				$dados['pro_codigo'] = $pro_codigo;
				$dados['lig_quantidade'] = $lig_quantidade;
                                $dados['adm_codigo'] = $arr["adm"][$pro_codigo];
                                
				$tbLIG->salvar($dados);
			}
			$this->getAdapter()->commit();

			return $lgra_codigo;
		} catch (Exception $exc) {
			Zend_Registry::get("logger")->log($exc->getMessage(), Zend_Log::INFO);
			$this->getAdapter()->rollBack();
			throw new Zend_Exception($exc->getMessage());
		}
	}

	/**
	 * Seleciona os proximos leitos a receber medicamentos
	 * @param int $set_codigo
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function getProximos($set_codigo=FALSE) {
		if (!$set_codigo) {
			$tbUsu = new Application_Model_Usuarios();
			$set_codigo = $tbUsu->getUsrAtual()->set_codigo;
		}
                /**
		 * Mostrar os leitos que devem receber medicamentos nos próximos $tempo (minutos)
		 * @var $tempo string
		 */
		$tbConfig = new Application_Model_Configuracao();
		$tempo = $tbConfig->getConfig("LEITO_TEMPO_FILA") . ' minutes';
                $tempo = "120 minutes";
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("lgra" => "leito_grade"), array("lgra_codigo", "lgra_intervalo", "lgra_repeticoes", "lgra_proximo"))
				->join(array("io" => "internacao_observacao"), "lgra.io_codigo = io.io_codigo", null)
				->join(array("pl" => "paciente_leito"), "pl.io_codigo = io.io_codigo", NULL)
				->join(array("lei" => "leito"), "lei.lei_codigo=pl.lei_codigo", array("lei_numero"))
				->join(array("qua" => "quarto"), "qua.qua_codigo=lei.qua_codigo", array("apt_codigo", "qua_numero"))
				->join(array("ai" => "atendimento_internacao"), "ai.io_codigo = io.io_codigo", "")
				->join(array("at" => "atendimento"), "at.ate_codigo = ai.ate_codigo", "")
				->join(array("ag" => "agendamento"), "ag.age_codigo = at.age_codigo", "")
				->join(array("usu" => "usuario"), "usu.usu_codigo=ag.usu_codigo", array("usu_nome"))
				->joinLeft(array("ldis" => "leito_dispensacao"), "ldis.lgra_codigo=lgra.lgra_codigo", array("vezes_medicado" => "count(ldis.ldis_codigo)"))
				->group(array("lgra.lgra_codigo", "lgra_intervalo", "lgra_repeticoes", "lgra_proximo", "usu_nome", "lei_numero", "apt_codigo", "qua_numero"))
				->where("io.io_situacao_internacao = 2")
				->where("qua.set_codigo=?", $set_codigo)
				->where("lgra_status=?", self::ATIVO)
				->where("(lgra_proximo < NOW()+?", $tempo)
				->orWhere("lgra_proximo IS NULL)")
				->order(new Zend_Db_Expr('lgra_proximo ASC NULLS FIRST'));
		//die($where);
		return $this->fetchAll($where);
	}

	public function getMelhoresLotes($lgra_codigo) {
		$tbUsr = new Application_Model_Usuarios();
		$tbPro = new Application_Model_Produto();

		$set_codigo = $tbUsr->getUsrAtual()->set_codigo;
		$produtos = array();

		$itens = $this->getItensFromGrade($lgra_codigo);
		foreach ($itens as $item)
			$produtos [$item->pro_codigo] = $item->lig_quantidade;

		$melhores = $tbPro->selecionaMelhorLote($produtos, $set_codigo);
		//die("<pre>".print_r($melhores,1));
		return $melhores;
	}

	public function buscarRerservas($lgra_codigo) {
		$tbCFR = new Application_Model_ControleFracionadoReserva();
		return $tbCFR->getReservas(Application_Model_ControleFracionadoReserva::LEITO_GRADE, $lgra_codigo);
	}

	public function getItensFromGrade($lgra_codigo) {
		return $this->fetchRow("lgra_codigo=$lgra_codigo")->findDependentRowset("Application_Model_LeitoGradeItens");
	}

	public function buscarGrades($io_codigo) {
		return $this->fetchAll("io_codigo=$io_codigo AND lgra_status=" . self::ATIVO, array("lgra_data", "lgra_hora"));
	}

}
