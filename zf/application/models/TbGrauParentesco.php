<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbGrauParentesco extends Elotech_Db_Table_Abstract {
	protected $_name = 'tb_grau_parentesco';
	protected $_primary = 'tgp_codigo';

    public function getDescricao(){
        $sql = $this->getDefaultAdapter()->query("
			SELECT tgp_codigo,tgp_descricao FROM tb_grau_parentesco
    	")->fetchAll();
        return $sql;
    }
}
