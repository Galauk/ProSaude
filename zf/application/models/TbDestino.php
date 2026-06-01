<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbDestino extends Elotech_Db_Table_Abstract {

    protected $_name = 'destino_do_paciente';
    protected $_primary = 'id_destino';

    public function getDestino(){
        //die($dom_codigo);
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ddp"=>"destino_do_paciente"))
                    ->order("id_destino");
        return $this->fetchAll($where);
    }

}
