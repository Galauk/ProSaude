<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Especialidade extends Elotech_Db_Table_Abstract {

	protected $_name = 'especialidade';
	protected $_primary = 'esp_codigo';
	protected $_dependentTables = array('Agendamento', 'Encaminhamento');

	public function salvar(array $data) {

		return false; // não pode salvar especialidades;
	}

	public function getEspecialidade($esp_codigo){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("especialidade"), array("esp_mais_agendamento"))
				->where("esp_codigo=?", $esp_codigo);
           //    die($where);
		return $this->fetchRow($where);
	}
	
	public function selectTags() {
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("especialidade"), array("esp_codigo", "esp_nome"))
			->where("esp_encaminhamento=?", true)
			->order("esp_nome ASC");
		return parent::selectTag($where, "esp_nome");
	}

	/**
	 * Buscar as especialidades
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE) {
		if ($term)
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("esp" => "especialidade"), array("esp_codigo", "esp_nome"))
					->where("retira_acentos(esp_nome) ilike retira_acentos('%$term%')", "S")
					->order(array("esp_nome"))
					->limit(15);
                //die($where);
		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $usu) {
			$data = array();
			foreach ($usu as $key => $value) {
				$data [$key] = $value;
			}

			$out [] = array(
				"id" => $usu->esp_codigo,
				"label" => $usu->esp_nome,
				"data" => $data
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("esp_codigo" => "0", "esp_nome" => "")
			);
		}

		return $out;
	}
        
	public function getEspecialidadePorConvenio($coni_codigo=FALSE){
		if(empty($coni_codigo))
			return false;
		$where = $this->select()
						->setIntegrityCheck(FALSE)
						->from(array("esp"=>"especialidade"),"esp_codigo")
						->join(array("coni"=>"convenio_itens"),"coni.esp_codigo=esp.esp_codigo","")
						->where("coni.coni_codigo=?",$coni_codigo);
		return $this->fetchRow($where);
	}
        
	public function getEspecialidadePorMedico($usr_codigo=FALSE){
		if(empty($usr_codigo))
			return false;
		$where = $this->select()
						->setIntegrityCheck(FALSE)
						->from(array("esp"=>"especialidade"),array("esp_codigo","esp_nome"))
						->join(array("mede"=>"medico_especialidade"),"mede.esp_codigo=esp.esp_codigo","")
						->where("coni.coni_codigo=?",$coni_codigo);
		return $this->fetchAll($where);
	}
        
	public function getEspecialidadePorProfissionalUnidade($usrCodigo=FALSE, $codigoUnidade = FALSE){
	
		$sql = $this->select(FALSE)
					->distinct()
					->setIntegrityCheck(FALSE)
					->from(array("esp"=>"especialidade"),array("esp_codigo","esp_nome","cod_cbo"))
					->join(array("mede"=>"medico_especialidade"),"mede.esp_codigo=esp.esp_codigo","")
					->join(array("unu"=>"unidade_usuarios"),"mede.uni_codigo=unu.uni_codigo","")
					->where("mede.med_codigo = $usrCodigo")
					->where("unu.uni_codigo = $codigoUnidade")
					->where("mes_ativo = 'A'");
		return $this->fetchAll($sql);
	}

	public function getEspecialidadePorProfissionalGeral($usrCodigo=FALSE){
	
		$sql = $this->select(FALSE)
					->distinct()
					->setIntegrityCheck(FALSE)
					->from(array("esp"=>"especialidade"),array("esp_codigo","esp_nome","cod_cbo"))
					->join(array("mede"=>"medico_especialidade"),"mede.esp_codigo=esp.esp_codigo","")
					->join(array("unu"=>"unidade_usuarios"),"mede.uni_codigo=unu.uni_codigo","")
					->where("mede.med_codigo = $usrCodigo")
					->where("mes_ativo = 'A'");
		return $this->fetchAll($sql);
	}
	
	public function getEspecialidadePorCbo($codCbo=FALSE){
		if($codCbo){
			$sql = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("esp"=>"especialidade"),array("esp_codigo","esp_nome"))
					->where("esp.cod_cbo =?", $codCbo);
					return $this->fetchRow($sql);
		} else {
			return "error";
		}


	} 
}