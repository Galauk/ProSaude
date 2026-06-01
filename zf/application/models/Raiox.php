<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Raiox extends Elotech_Db_Table_Abstract {

    protected $_name = 'raiox';
	protected $_primary = 'rai_codigo';

	public function getRaiox(){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("rai"=>"raiox"))
				->order("rai_codigo");
		
		return $this->fetchAll($where);
	
	
	}
}
