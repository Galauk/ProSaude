<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Distrito extends Elotech_Db_Table_Abstract {

    protected $_name = 'distrito';
    protected $_primary = 'dis_codigo';

    public function salvar(array $data) {
		///throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
	
}
