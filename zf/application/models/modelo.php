<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Modelo extends Elotech_Db_Table_Abstract {

    protected $_name = 'psicotropicos';
	protected $_primary = 'psico_codigo';
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

}
