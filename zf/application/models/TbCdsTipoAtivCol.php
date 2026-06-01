<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTipoAtivCol extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_ativ_col';
    protected $_primary = 'co_cds_tipo_ativ_col';

    public function getDadosTipoAtividade() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_ativ_col"))
                ->order(array("co_cds_tipo_ativ_col"));
        return $this->fetchAll($sql);
    }

}
