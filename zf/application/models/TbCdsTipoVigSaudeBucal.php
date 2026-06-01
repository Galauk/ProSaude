<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTipoVigSaudeBucal extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_vig_saude_bucal';
    protected $_primary = 'co_cds_tipo_vig_saude_bucal';
    protected $_sequence = 'seq_co_cds_tipo_vig_saude_bucal';

    public function getDados() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_vig_saude_bucal"))
                ->order(array("co_cds_tipo_vig_saude_bucal"));
        return $this->fetchAll($sql);
    }

}
