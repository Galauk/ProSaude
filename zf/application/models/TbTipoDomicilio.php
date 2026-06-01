<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbTipoDomicilio extends Elotech_Db_Table_Abstract {

	protected $_name = 'tb_tipo_domicilio';
	protected $_primary = 'ttd_codigo';

    public function getDescricao(){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("ttd"=>"tb_tipo_domicilio"));
        return $this->fetchAll($sql);
    }
}
