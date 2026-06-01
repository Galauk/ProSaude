<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_SinaisVitais extends Elotech_Db_Table_Abstract {

	protected $_name = 'sinais_vitais';
	protected $_primary = 'si_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data) {
		$age = Application_Model_Agendamento::usuEmAberto();
		$tbUsr = new Application_Model_Usuarios();

		$nomes = array(
			"si_temperatura" => "temperatura",
			"si_peso" => "peso",
			"si_altura" => "altura",
			"si_perimetro_cefalico" => "perímetro cefálico",
			"si_pressao_sistolica" => "pressão sistólica",
			"si_pressao_diastolica" => "pressão diastólica",
			"si_freq_cardiaca" => "freq. cardíaca",
			"si_freq_respiratoria" => "freq. respiratória",
			"si_dados" => "outras informações"
		);
		$this->addRealName($nomes);

		
		$usr = $tbUsr->getUsrAtual();
		
		$data['usr_codigo'] = $usr->usr_codigo;
		$data['esp_codigo'] = $usr->esp_codigo;
		$data['si_data'] = "NOW()";

		$this->filterFloat(array("si_temperatura", "si_peso", "si_altura", "si_perimetro_cefalico"), $data);
		$this->filterDigits(array("si_pressao_sistolica", "si_pressao_diastolica", "si_freq_cardiaca", "si_freq_respiratoria"), $data);
		$this->emptyToNull($data);

		$range = array(
			"si_temperatura" => array(30, 45),
			"si_peso" => array(0, 200),
			"si_altura" => array(0, 2.5)
		);
		$this->range($range, $data);

		// Há alguma informação? (impedir envio de PC totalmente vazia)
		$this->peloMenosUm(array("si_temperatura", "si_peso", "si_altura", "si_perimetro_cefalico", "si_pressao_sistolica", "si_pressao_diastolica", "si_freq_cardiaca", "si_freq_respiratoria", "si_dados"), $data);
	//echo "<pre>".print_r($data,true);
			//exit();
		$si_codigo = parent::salvar($data);
		//die($si_codigo."si");
		// Procedimento realizado: aferição de pressão (0301100039)
		/*if (empty($data['si_codigo']) && !empty($data['si_pressao_sistolica'])) {
			
			$tbProc = new Application_Model_Procedimento();
			$tbPat = new Application_Model_ProcedimentoAtendimento();

			$proc = $tbProc->fetchRow("proc_codigo_sus='0301100039'");

			$dados = array(
				"si_codigo" => $si_codigo,
				"proc_codigo" => $proc->proc_codigo,
				"usr_codigo" => $data['usr_codigo']
			);
			echo "<pre>".print_r($dados,true);
			exit();
			$tbPat->salvar($dados,"S");
		}*/

		return $si_codigo;
	}

	public function temPreConsulta($age_codigo) {
		return $this->fetchRow("age_codigo=$age_codigo");
	}

	/**
	 * Se não passar usu_codigo, irá pegar somente as PC deste agendamento
	 * @param int $usu_codigo
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param array $opcoes
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function getHistorico($io_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE,$ate_codigo=FALSE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("si" => "sinais_vitais"), array("si_dados", "si_temperatura", "si_peso", "si_altura", "si_pressao_sistolica", "si_pressao_diastolica","si_freq_cardiaca","si_freq_respiratoria","si_perimetro_cefalico","si_glicose","si_codigo","si_data"))
				->join(array("ate" => "atendimento"), "ate.ate_codigo=si.ate_codigo", "ate.ate_codigo")
				->join(array("ati" => "atendimento_internacao"), "ate.ate_codigo=ati.ate_codigo", "io_codigo")
				->order(array("si.si_data DESC"));
		
		if($io_codigo){
			$where->where("ati.io_codigo=?", $io_codigo);
		}
		if($ate_codigo){
			$where->where("ate.ate_codigo=?", $ate_codigo);
		}
			

		if ($data_inicial)
			$where->where("si.pc_data >= ?", $data_inicial);

		if ($data_final)
			$where->where("si.pc_data <= ?", $data_final);
		
            // die($where);
		return $this->fetchAll($where);
	}

	public function getPC($pc_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pc" => "pre_consulta"), array("pc_dados","pc_temperatura", "pc_peso", "pc_altura", "pc_pressao_sistolica", "pc_pressao_diastolica","pc_clas_risco"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pc.usr_codigo", "usr_nome")
				->join(array("esp" => "especialidade"), "esp.esp_codigo=pc.esp_codigo", "esp_nome")
				->join(array("age" => "agendamento"), "age.age_codigo=pc.age_codigo", "age_data")
				->join(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
				->where("pc.pc_codigo=?", $pc_codigo);

		return $this->fetchRow($where);
	}

	/**
	 * 
	 * Ele retorna toda uma linha, não só o id da ultima pré-consulta
	 * @throws Exception
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getUltima() {
		$age = Application_Model_Agendamento::usuEmAberto();
		if (!$age)
			throw new Exception("Agendamento não encontrado");

		return $this->fetchRow("age_codigo=" . $age->age_codigo, "age_codigo DESC");
	}

}
