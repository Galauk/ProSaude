<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Secretaria extends Elotech_Db_Table_Abstract {

    protected $_name = 'secretaria';
    protected $_primary = 'codigo_secretaria';
    
    public function getDadosSec(){
        return $this->fetchRow();
    }

}
