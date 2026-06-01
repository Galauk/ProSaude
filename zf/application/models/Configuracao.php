<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Configuracao extends Elotech_Db_Table_Abstract {

	protected $_name = 'config';
	protected $_primary = 'conf_codigo';

	const STRING = 1;
	const BOOL = 2;
	const INT = 3;
	const DATA = 4;

	public function salvar(array $data) {
		// este salvar recebe um array diferente!

		foreach ($data['config'] as $key => $valor) {
			$dados = array();
			switch ($data['tipo'][$key]) {
				case self::STRING:
					$dados['conf_valor_string'] = $valor;
					break;
				case self::BOOL:
                                        
					$dados['conf_valor_bool'] = $valor;
					break;
				case self::INT:
					$this->isInteger(array(0), array($valor));
					$dados['conf_valor_int'] = $valor;
					break;
				case self::DATA:
					$this->isDate(array(0), array($valor));
					$dados['conf_valor_data'] = $valor;
					break;

				default:
					break;
			}

			$dados['conf_codigo'] = $key;
			parent::salvar($dados);
		}

		return true;
	}

	/**
	 * Busca uma configuração no banco de dados
	 * @param string $chave
	 * @return mixed 
	 */
	public function getConfig($chave) {
		$valor = null;
            
		$config = $this->fetchRow("conf_chave='$chave'");
			   
		if (!$config)
                    
			return false;

		switch ($config->conf_tipo) {
			case self::STRING:
				return $config->conf_valor_string;
				break;
			case self::BOOL:
				$valor = ($config->conf_valor_bool == 't' ? TRUE : FALSE);
   
				return ($config->conf_valor_bool == 't' ? TRUE : FALSE);
				break;
			case self::INT:
				$valor .= $config->conf_valor_int;
				return $valor;
				break;
			case self::DATA:
				return $config->conf_valor_data;
				break;

			default:
				return $config->conf_valor_string;
				break;
		}
	}

	public function getIniConfig($path) {
		$ini = Zend_Registry::get("config");

		try {
			return $ini->$path;
		} catch (Exception $exc) {
			unset($exc); // sonar
			return FALSE;
		}
	}
        
        public function getDadosConfigPelaChave($chave=FALSE){
             $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from("config")
                          ->where("conf_chave='$chave'");
                         // die($where);
            return $this->fetchRow($where);
        }
        
        public function salvarDadosConfig($data=FALSE){
            try{
                return parent::salvar($data);
            } catch (Exception $exc) {
                throw new Zend_Validate_Exception("Falha ao atualizar a configuração: ".$exc->getMessage());
            }
        }
        
        public function getConfigPorCategoria($cac_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from("config")
                          ->where("cac_codigo=$cac_codigo");
            return $this->fetchAll($where);
		}
		
		public function getVersaoSaude(){
			$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("conf" => "config"), array("conf.conf_valor_string"))
				->where("conf.conf_codigo=8"); //Versão Saude
			return $this->fetchAll($where);
		}

}
