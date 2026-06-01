<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbMonitoramento extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_monitoramento';
	protected $_primary = 'id_monitoramento';
    protected $_dependentTables = array();

    
    public function getMonitoramento($est_codigo=false) {
		$where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("moni" => "tb_monitoramento"));

        return $this->fetchAll($where);
    }

}
