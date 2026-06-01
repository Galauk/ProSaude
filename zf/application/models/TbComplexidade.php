<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbComplexidade extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_complexidade';
    protected $_primary = 'co_complexidade';
    
    public function getComplexidadePorSigla($sigla=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tc"=>"tb_complexidade"),array("co_complexidade","no_complexidade","sg_complexidade"))
                    ->where("sg_complexidade='$sigla'");
        
        return $this->fetchRow($sql);
    }
    
}
