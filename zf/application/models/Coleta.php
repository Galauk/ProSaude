<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Coleta extends Elotech_Db_Table_Abstract {

	protected $_name = 'coleta';
	protected $_primary = 'col_codigo';

	public function getColeta($agei_codigo = FALSE) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("col" => "coleta"))
                            ->where("col.agei_codigo =?", $agei_codigo);

            return $this->fetchRow($where);
	}


}
