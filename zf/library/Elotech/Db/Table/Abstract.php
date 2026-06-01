<?php

class Elotech_Db_Table_Abstract extends Zend_Db_Table_Abstract {

	private $_fieldNames = array(
		"esp_codigo" => "especialidade",
		"med_codigo" => "médico",
		"pro_codigo" => "produto",
		"proc_codigo" => "procedimento",
		"usu_codigo" => "paciente",
		"usr_codigo" => "usuário",
		"pc_codigo" => "pré-consulta",
		"ate_codigo" => "atendimento",
		"pe_codigo" => "posto de enfermagem",
		"ate_reclamacao" => "descrição do paciente", // tem nome diferente se for unico (Evolução)
		"ate_exame_fisico" => "exame físico",
		"ate_diagnostico" => "diagnóstico",
		"ate_tratamento" => "conduta/tratamento",
		"ate_curativo" => "curativos/motivo do procedimento",
		"lgm_descricao" => "descrição",
		"lei_codigo" => "leito",
        "usr_codigo_solicitante"=>"medico solicitante",

	);

	/**
	 * Campos que serão listados no jqGrid
	 * @var array
	 */
	private $fields;

    public function init(){
        $stmt = $this->getDefaultAdapter()->query("SET datestyle = 'ISO, DMY'");
    }

    public function validateBeforeSave($data){
        //implementar nas classes específicas
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

	public function salvar(array $data) {
		//error_reporting(E_ALL);
		//echo "<pre>"; print_r($data);
		$this->validateBeforeSave($data);
		$pk = $this->_primary;
	
		if (is_array($pk)){
			$pk = current($pk);
		}
		// echo "<pre>";
		// print_r($data);
		
		if ($data[$pk]) {
			$id = $data[$pk];
			unset($data[$pk]);
			
			parent::update($data, $pk . "=" . $id);
			
			return $id;
		} else {
			unset($data[$pk]);

			try{
				$id = parent::insert($data);
				
				return $id ? $id : $this->getDefaultAdapter()->lastInsertId();
			} catch (Exception $exc) {
				print_r($exc->getMessage());
			}
		}
	}

	/**
	 * Valida se todos os itens em $field estão presentes em $dados
	 * @param $field Campos a serem validados
	 * @param $data Valores informado para ::salvar()
	 */
	protected function notEmpty($field, &$data) {
		$validator = new Zend_Validate_NotEmpty();

		foreach ($field as $campo) {
			trim($data[$campo]);
			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception(sprintf("O campo \"%s\" é obrigatório e deve ser preenchido.", $this->_realName($campo)), 1001);
		}

		return $this;
	}


	protected function isDate($fields, &$data) {
		$validator = new Zend_Validate_Regex("/^((([0][1-9]|[12][0-9])\/02\/(19|20)([13579][26]|[02468][048]))|(([0][1-9]|[1][0-9]|[2][0-8])\/02\/(19|20)([02468][12356]|[013579][13579]))|((([0][1-9]|[12][0-9]|30)\/(0[469]|11)|([0][1-9]|[12][0-9]|3[01])\/(0[13578]|1[02]))\/(19|20)[0-9][0-9]))$/");
		foreach ($fields as $campo) {
			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception("Informe uma data válida.", 9999);
			else {
				list($d, $m, $y) = explode("/", $data[$campo]);
				$data[$campo] = "$y-$m-$d";
			}
		}
		return $this;
	}

	protected function isFloat($field, $data) {
		$validator = new Zend_Validate_Float();

		foreach ($field as $campo) {
			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception(sprintf("O campo \"%s\" precisa ser um número.", $this->_realName($campo)), 1005);
		}

		return $this;
	}

	protected function isInteger($field, $data) {
		$validator = new Zend_Validate_Int();

		foreach ($field as $campo) {
			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception(sprintf("O campo \"%s\" precisa ser um número.", $this->_realName($campo)), 1006);
		}

		return $this;
	}

	protected function range($field, $data) {
		$validator = new Zend_Validate_Between(0, 0);

		// $field = array( "campo" => array($min,$max) )

		foreach ($field as $campo => $range) {
			if (!isset($data[$campo]))
				continue;

			$validator->setMin($range[0])->setMax($range[1]);

			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception(sprintf("O campo \"%s\" precisa ser um número entre %d e %d.", $this->_realName($campo), $range[0], $range[1]), 1007);
		}

		return $this;
	}

	protected function equals($field, $data) {
		$validator = new Zend_Validate_Identical();

		foreach ($field as $a => $b) {
			if (!$validator->setToken($data[$a])->isValid($data[$b]))
				throw new Zend_Validate_Exception(sprintf("Os campos \"%s\" e \"%s\" devem ser iguais.", $this->_realName($a), $this->_realName($b)), 1002);
		}
		return $this;
	}

	protected function maiorQueZero($field, $data, $opcionais=array()) {
		$validator = new Zend_Validate_GreaterThan(0);

		foreach ($field as $campo) {
			if (empty($data[$campo]) && isset($opcionais[$campo]) && $opcionais[$campo])
				continue;

			if (!$validator->isValid($data[$campo]))
				throw new Zend_Validate_Exception(sprintf("Informe o campo \"%s\".", $this->_realName($campo)), 1007);
		}

		return $this;
	}

	protected function peloMenosUm($field, $data) {
		$i = 0;
		foreach ($field as $valor) {
			if (!empty($data[$valor])) {
				$i++;
			}
		}
		if (!$i) {
			$arr = array();
			foreach ($field as $valor) {
				$arr[] = $this->_realName($valor);
			}
			if (count($arr) > 1) {
				$ultimo_campo = array_pop($arr);
				$campos = implode(", ", $arr) . " ou $ultimo_campo";
			} else {
				$campos = implode(", ", $arr);
			}
			throw new Zend_Validate_Exception(sprintf("Informe pelo menos um dos seguintes campos: %s.", $campos), 1000);
		}
	}

	protected function minLength($field, $data, $opcionais = array()) {
		$validator = new Zend_Validate_GreaterThan(0);

		foreach ($field as $campo => $min) {
			$len = strlen($data[$campo]);
			if (!$len && isset($opcionais[$campo]) && $opcionais[$campo])
				continue;

			if (!$validator->setMin($min - 1)->isValid($len))
				throw new Zend_Validate_Exception(sprintf("O campo \"%s\" precisa ter %d caractres ou mais.", $this->_realName($campo), $min), 1003);
		}

		return $this;
	}

	protected function unico($field, $data) {
		foreach ($field as $campo) {
			if ($data[$this->_primary])
				$where = "$campo = '" . $data[$campo] . "' AND " . $this->_primary . " <> " . $data['id'];
			else
				$where = "$campo = '" . $data[$campo] . "'";

			if ($this->fetchAll($where)->count())
				throw new Zend_Validate_Exception(sprintf("%s já está em uso e precisa ser único.", ucfirst($this->_realName($campo))), 1004);
		}

		return $this;
	}

	protected function emptyToNull(&$data) {
		foreach ($data as $k => $v) {
			if (empty($v))
				$data[$k] = NULL;
		}

		return $this;
	}

	protected function emptyToUnset(&$data,$zeroIsNull=TRUE) {
		foreach ($data as $k => $v) {
			if ($v == ""){
				unset($data[$k]);
			}
		}

		return $this;
	}

	protected function filterDigits($field, array &$data) {
		$filtro = new Zend_Filter_Digits();

		foreach ($field as $campo) {
			if (isset($data[$campo]))
				$data[$campo] = $filtro->filter($data[$campo]);
		}

		return $this;
	}

	protected function filterFloat($field, array &$data) {
		$filtro = new Zend_Filter_PregReplace();
		$filtro->setMatchPattern("/[^0-9\.]+/")->setReplacement("");

		foreach ($field as $campo) {
			if (isset($data[$campo]))
				$data[$campo] = $filtro->filter($data[$campo]);
		}

		return $this;
	}

	protected function addRealName($fieldNames) {
		$this->_fieldNames = array_merge($this->_fieldNames, $fieldNames);
		return $this;
	}

	private function _realName($field) {
		if (isset($this->_fieldNames[$field]))
			return $this->_fieldNames[$field];

		return $field;
	}

	protected function selectTag($where, $texto, $value=NULL, $first=NULL, $tag=TRUE, $name=NULL, $id=NULL, $foco=FALSE, $selected=0,$action=FALSE) {
		if (!$value)
			$value = current($this->_primary);

		if (!$name)
			$name = $value;

		if (!$id)
			$id = $name;

		$all = $this->fetchAll($where);
		$out = "";
		if ($tag)
			$out = "<select name=\"$name\" id=\"$id\"" . ($foco ? " class=\"focus \"" : "") . " class='ui-state-default' style=\"width:302px;\" ".$action.">\n";
		

		if ($first) {
			if (is_array($first))
				$out .= "\t<option value=\"" . $first[0] . "\">" . $first[1] . "</option>\n";
			else
				$out .= "\t<option value=\"\">-- Selecione --</option>\n";
		}
		foreach ($all as $option) {
			if( $option->$value == $selected)
				$out .= "\t<option value=\"" . $option->$value . "\" selected>" . trim($option->$texto) . "</option>\n";
			else
				$out .= "\t<option value=\"" . $option->$value . "\">" . trim($option->$texto) . "</option>\n";
		}

		if ($tag)
			$out .= "</select>\n";

		return $out;
	}

	public function getGridResource($page=1, $limit=FALSE, $sidx=NULL, $sord="ASC", $where=NULL) {

		if (is_null($sidx) || $sidx == "id") {
			$pk = (array) $this->_primary;
			$pk = $pk[1];
			$sidx = $pk;
		}

		$item = $this->fetchAll($where);
		$count = count($item);

		if ($count > 0) {
			if ($limit)
				$total_pages = ceil($count / $limit);
			else
				$total_pages = 1;
		} else {
			$total_pages = 0;
		}

		if ($page > $total_pages)
			$page = $total_pages;

		$offset = ($page * $limit - $limit);
		if ($offset < 1)
			$offset = 0;

		if (is_null($where) || is_string($where))
			$item = $this->fetchAll($where, "$sidx $sord", $limit, $offset);
		else {
			$where->order("$sidx $sord")
					->limit($limit, $offset);

			$item = $this->fetchAll($where);
		}


		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i = 0;

		$pk = (array) $this->_primary;
		$pk = $pk[1];

		foreach ($item as $row) {
			$responce->rows[$i]['id'] = $row->$pk;
			$arr = array();
			foreach ($this->fields as $campo)
				$arr [] = $row->$campo;

			$responce->rows[$i]['cell'] = $arr;
			$i++;
		}

		return $responce;
	}

	/**
	 * Atualiza todas os itens, alterando o usu_codigo.
	 * Método usado para tirar a duplicação de pacientes
	 * @see Application_Model_Usuario::removerDuplicacoes()
	 * @param array|int $de
	 * @param int $para
	 * @return int Número de linhas atualizadas
	 */
	public function atualizarUsu($de, $para){
		$de = (array)$de;

		$data = array("usu_codigo" => $para);
		$where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];

		Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

		return $this->update($data, $where);
	}

	/**
	 * Atualiza todas os itens, alterando o usu_codigo.
	 * Método usado para tirar a duplicação de produto
	 * @see Application_Model_Usuario::removerDuplicacoes()
	 * @param array|int $de
	 * @param int $para
	 * @return int Número de linhas atualizadas
	 */
	public function atualizarPro($de, $para){
		$de = (array)$de;

		$data = array("pro_codigo" => $para);
		$where = $this->select()->where("pro_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];

		Zend_Registry::get("logger")->log("Atualizando produtos em ".$this->_name, Zend_Log::INFO);
		Zend_Registry::get("logger")->log("sql".$where, Zend_Log::INFO);

		return $this->update($data, $where);
	}

}

