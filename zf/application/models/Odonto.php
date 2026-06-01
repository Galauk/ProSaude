<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Odonto extends Elotech_Db_Table_Abstract {

	protected $_name = 'odonto';
	protected $_primary = 'od_codigo';
	protected $_dependentTables = array();
	private $dentes_adulto = array(
		1 => 'Incisivo Central',
		2 => 'Incisivo Lateral',
		3 => 'Canino',
		4 => '1º Premolar',
		5 => '2º Premolar',
		6 => '1º Mmolar',
		7 => '2º Molar',
		8 => '3º Molar'
	);
	/**
	 * @var array mapeamento dos dentes de criancas
	 */
	private $dentes_crianca = array(
		1 => 'Incisivo Central',
		2 => 'Incisivo Lateral',
		3 => 'Canino',
		4 => '1º Molar',
		5 => '2º Molar'
	);
	/**
	 * @var array mapeamento das faces
	 */
	private $faces = array(
		'O' => 'Oclusal / Incisal', // face que bate dente com dente. A parte que mastiga
		'V' => 'Vestibular', // a parte do dente que fica em contato com a bochecha
		'L' => 'Lingual / Palatina', // a face que fica voltada pra dentro da boca. Contato com a lingua.
		'M' => 'Mesial', // face da frente, em direcao a ponta da lingua
		'D' => 'Distal'  // face de tras, em direcao a garganta
	);
	private $situacaoes = array(
		1 => "Restauracao a ser realizada",
		2 => "Restauracao realizada",
		3 => "Restauracao pre-existente mantida",
		4 => "Dente Ausente",
		5 => "Exodontia a ser realizada",
		6 => "Exodontia realizada",
		7 => "Selante a ser realizado",
		8 => "Selante realizado",
		9 => "Terapia pulpar realizada",
		10 => "Terapia pulpar pre-existente",
		11 => "Terapia pulpar pre-existente em boas condicoes"
	);
	private $legendas;

	/** 
	 * Model para controle da tabela Odonto
	 * @param array $config 
	 * @see Elotech_Db_Table_Abstract::__construct()
	 */
	public function __construct($config = array()) {
		$this->legendas = array(
			1 => "Face circulada em " . $vermelho,
			2 => "Face preenchida em " . $vermelho,
			3 => "Face preenchida em " . $azul,
			4 => "Traço vertical em " . $azul,
			5 => "Traço diagonal em " . $vermelho,
			6 => "X em " . $vermelho,
			7 => "S em " . $vermelho,
			8 => "S circulado em " . $vermelho,
			9 => "Triangulo vazio em " . $vermelho,
			10 => "Triangulo cheio em " . $vermelho,
			11 => "Triangulo cheio em " . $azul
		);

		parent::__construct($config);
	}

	/**
	 * Salva um tratamento odontológico
	 * @param array $data
	 * @return int 
	 */
	public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);

		return parent::salvar($data);
	}

	/**
	 * Retorna o od_codigo para fazer a FK no OdontoHistorico.
	 * Se não houver, ele irá criar.
	 * @param int $usu_codigo=FALSE Opcional. Se não informado será usado o agen-
	 * damento atual como filtro
	 * @return int 
	 */
	public function getOdontoAberto($usu_codigo=FALSE) {
		$age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FAlSE)
				->from(array("od" => "odonto"), "od_codigo")
				->join(array("age" => "agendamento"), "age.age_codigo=od.age_codigo", "")
				->where("od_finalizado='N'")
				->order("od_data DESC");

		if ($usu_codigo)
			$where->where("age.usu_codigo=?", $usu_codigo);

		else {
			$where->join(array("age2" => "agendamento"), "age2.usu_codigo=age.usu_codigo", "")
					->where("age2.age_codigo=?", $age_codigo);
		}

		$odonto = $this->fetchRow($where);

		// Se não houver, criar um novo
		if (!$odonto) {
			$odonto = $this->fetchNew();
			$odonto->age_codigo = $age_codigo;
			return $odonto->save(); // Não passa pelo salvar
		}

		return $odonto->od_codigo;
	}

	public function finalizarTratamento($od_codigo){
		$od = $this->find($od_codigo)->current();
		$od->od_finalizado = "S";
		$od->od_datafinal = date("Y-m-d");
		$od->save();
		return TRUE;
	}
	
	/**
	 * Adiciona os valores padrão ao objeto $data
	 * @param array $data 
	 */
	private function valoresPadrao(&$data) {
		// Data. Padrão: hoje
		if (is_null($data['od_data']) || empty($data['od_data']))
			$data['od_data'] = date("Y-m-d");

		// Hora. Padrão: agora
		if (is_null($data['od_hora']) || empty($data['od_hora']))
			$data['od_hora'] = date("H:i:s");

		// Finalizar? (nunca)
		if (is_null($data['od_finalizado']) || empty($data['od_finalizado']))
			$data['od_finalizado'] = "N";

		// Agendamento. Padrão: agendamento aberto (session)
		if (is_null($data['age_codigo']) || empty($data['age_codigo']))
			$data['age_codigo'] = Application_Model_Agendamento::usuEmAberto()->age_codigo;
	}

	/**
	 * Traz a lista de todos os procedimentos feitos em todos os dentes
	 * @param int $usu_codigo Opcional. Se não informado será usado o agendamento
	 * aberto como referência ao usuário
	 * @param int $denteNum
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getTodosProcedimentos($usu_codigo=FALSE, $denteNum=FALSE) {

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("od" => "odonto"), "")
				->join(array("odh" => "odonto_historico"), "odh.od_codigo=od.od_codigo", array("od_hist_data", "dente_num", "dente_face", "dente_situacao", "dente_anotacao", "od_finalizado"))
				->join(array("age" => "agendamento"), "age.age_codigo=od.age_codigo", "");

		if ($usu_codigo)
			$where->where("age.usu_codigo=?", $usu_codigo);

		else {
			$age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
			$where->join(array("age2" => "agendamento"), "age2.usu_codigo=age.usu_codigo", "")
					->where("age2.age_codigo=?", $age_codigo);
		}

		if ($denteNum)
			$where->where("odh.dente_num=?", $denteNum);


		return $this->fetchAll($where);
	}

	/**
	 * Transforma um Rowset em um Array para ser enviado via json (ajax)
	 * @param Zend_Db_Table_Row_Abstract $itens
	 * @return array 
	 */
	public function toJson($itens) {
		$out = array();
		foreach ($itens as $item) {
			$faces = (substr($item->dente_face, -1) == ";" ? substr($item->dente_face, 0, -1) : $item->dente_face);
			$faces = explode(";", $faces);

			$out [] = array(
				"n" => $item->dente_num,
				"f" => $faces,
				"s" => $item->dente_situacao
			);
		}

		return $out;
	}

	/**
	 * Retorna as siuações (procedimentos)
	 * @return array 
	 */
	public function getSituacao() {
		return $this->situacaoes;
	}

	/**
	 * Retorna o nome do dente
	 * @param int $num numero do dente
	 * @example getNome(46): 1º Mmolar inferior direito
	 * @return string nome do dente
	 */
	public function getNome($num) {

		$q = $num[0];
		$d = $num[1];

		// adulto
		if ($q <= 4) {
			$qs = ( $q == 1 || $q == 2 ? 'superior' : 'inferior' );
			$qp = ( $q == 1 || $q == 4 ? 'direito' : 'esquerdo' );
			return $this->dentes_adulto[$d] . ' ' . $qs . ' ' . $qp;
		}
		// crianca
		else {
			$qs = ( $q == 5 || $q == 6 ? 'superior' : 'inferior' );
			$qp = ( $q == 5 || $q == 8 ? 'direito' : 'esquerdo' );
			return $this->dentes_crianca[$d] . ' ' . $qs . ' ' . $qp;
		}
	}

}
