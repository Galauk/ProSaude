<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbSituacaoMoradia extends Elotech_Db_Table_Abstract {

	protected $_name = 'tb_situacao_moradia';
	protected $_primary = 'tsm_codigo';

    public function getDescricao(){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("tsm"=>"tb_situacao_moradia"));
        return $this->fetchAll($sql);
    }
}
