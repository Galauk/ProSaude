<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_MotivosFaltas extends Elotech_Db_Table_Abstract {

    protected $_name = 'motivos_faltas';
    protected $_primary = 'mof_codigo';

   
    
    public function getMotivos(){
           $sql = $this->select()
                       ->setIntegrityCheck(FALSE)
                       ->from(array("mof"=>"motivos_faltas"));
           return $this->fetchAll($sql);
    }

  
}
