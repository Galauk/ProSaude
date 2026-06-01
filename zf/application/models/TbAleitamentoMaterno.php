<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbAleitamentoMaterno extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_aleitamento_materno';
	protected $_primary = 'am_codigo';

	public function recuperaDadosAleitamentoMaterno(){

		$sql = $this->getDefaultAdapter()->query(
			'SELECT * FROM tb_aleitamento_materno'
		)->fetchAll();
		return $sql;
	}

}
