<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbOrigem extends Elotech_Db_Table_Abstract {

    protected $_name = 'origem_do_paciente';
    protected $_primary = 'id_origem';

    public function getOrigem(){
        //die($dom_codigo);
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odp"=>"origem_do_paciente"))
                    ->order("id_origem");
        return $this->fetchAll($where);
    }

}
