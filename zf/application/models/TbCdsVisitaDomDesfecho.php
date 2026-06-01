<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCdsVisitaDomDesfecho extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_visita_dom_desfecho';
    protected $_primary = 'co_cds_visita_dom_desfecho';
    

    public function getDesfecho(){
        //die($dom_codigo);
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdd"=>"tb_cds_visita_dom_desfecho"))
                    ->order("co_cds_visita_dom_desfecho");
        return $this->fetchAll($where);
    }
    
    
}
