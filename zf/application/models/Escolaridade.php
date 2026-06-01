<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Escolaridade extends Elotech_Db_Table_Abstract {

    protected $_name = 'escolaridade';
    protected $_primary = 'ecd_codigo';
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
 

}
