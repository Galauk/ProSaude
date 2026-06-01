<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsAtivColTema extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_ativ_col_tema';
    protected $_primary = 'co_cds_ativ_col_tema';
    protected $_sequence = 'seq_co_cds_ativ_col_tema';

    public function getDadosTema() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_ativ_col_tema"))
                ->order(array("co_cds_ativ_col_tema"));
        return $this->fetchAll($sql);
    }

}
