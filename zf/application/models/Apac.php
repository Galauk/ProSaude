<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Apac extends Elotech_Db_Table_Abstract {

    protected $_name = 'apac';
	protected $_primary = 'apac_codigo';
    protected $_dependentTables = array();

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }

	/**
	 * Atualiza todas os agendamentos, alterando o usu_codigo.
	 * Método usado para tirar a duplicação de pacientes
	 * @see Application_Model_Usuario::removerDuplicacoes()
	 * @param array|int $de
	 * @param int $para 
	 * @return int Número de linhas atualizadas
	 */
	public function atualizarUsu($de, $para){
		$de = (array)$de;
		
		$data = array("pac_codigo" => $para);
		$where = $this->select()->where("pac_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];
		
		Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
		
		return $this->update($data, $where);
	}


}
