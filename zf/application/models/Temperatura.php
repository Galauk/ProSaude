<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

Class Application_Model_Temperatura extends Elotech_Db_Table_Abstract{
    protected $_name = "temperatura_geladeira";
    protected $_primary = "gel_codigo";
    
}

?>
