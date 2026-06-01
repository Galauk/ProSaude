<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_VeiculoTipoTransporte extends Elotech_Db_Table_Abstract {

    protected $_name = 'veiculo_tipo_transporte';
    protected $_primary = 'veit_codigo';   

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

