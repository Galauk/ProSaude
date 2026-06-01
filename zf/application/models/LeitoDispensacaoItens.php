<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_LeitoDispensacaoItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'leito_itens_dispensacao';
	protected $_primary = 'lid_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		$validar = array("cont_codigo","lid_quantidade","ldis_codigo");
		$this->filterDigits($validar, $data);
		$this->notEmpty($validar, $data);
		$this->maiorQueZero($validar, $data);
		
        return parent::salvar($data);
    }

}
