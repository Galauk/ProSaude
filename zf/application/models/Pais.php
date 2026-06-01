<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Pais extends Elotech_Db_Table_Abstract {

    protected $_name = 'pais';
    protected $_primary = 'pais_codigo';
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
 

}
