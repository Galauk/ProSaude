<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTtipoAtend extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_atend';
    protected $_primary = 'co_cds_tipo_atend';
    protected $_sequence = 'seq_co_cds_tipo_atend';

    public function getDadosTema() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_atend"))
                ->order(array("co_cds_tipo_atend"));
        return $this->fetchAll($sql);
    }

}
