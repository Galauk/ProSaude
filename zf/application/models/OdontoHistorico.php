<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_OdontoHistorico extends Elotech_Db_Table_Abstract {

	protected $_name = 'odonto_historico';
	protected $_primary = 'od_hist_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data) {
		
		$this->valoresPadrao($data);
		if($data['od_finalizado'] == "S"){
			$tbOd = new Application_Model_Odonto();
			$tbOd->finalizarTratamento($data['od_codigo']);
		}
		unset($data['od_finalizado']);
		
		$temp = $data['dente_situacao'];
		unset($data['dente_situacao']);
		foreach($temp as $item){
			$data['dente_situacao'] = $item;
			$ultimo_id = parent::salvar($data);
		}

		return $ultimo_id;
	}

	private function valoresPadrao(&$data) {

		// Odonto
		if (is_null($data['od_codigo']) || empty($data['od_codigo'])) {
			$tbOd = new Application_Model_Odonto();
			
			$data['od_codigo'] = $tbOd->getOdontoAberto();
		}

		// data
		if (is_null($data['od_hist_data']) || empty($data['od_hist_data']))
			$data['od_hist_data'] = date("Y-m-d");
		
		//Nenhuma face?
		if (is_null($data['dente_face']) || empty($data['dente_face']))
			$data['dente_face'] = "N";
		
		// Mais de uma face?
		if(is_array($data['dente_face']))
			$data['dente_face'] = implode(";",$data['dente_face']);
		
	}

}
