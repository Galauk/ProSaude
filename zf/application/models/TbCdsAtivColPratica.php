<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsAtivColPratica extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_ativ_col_pratica';
    protected $_primary = 'co_cds_ativ_col_pratica';
    protected $_sequence = 'seq_co_cds_ativ_col_pratica';

    public function getDados() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_ativ_col_pratica"))
                ->order(array("co_cds_ativ_col_pratica"));
        return $this->fetchAll($sql);
    }

    public function getDadosTemas() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_ativ_col_pratica"))
                ->where("co_cds_ativ_col_pratica IN (29,19,1,4,5,7,8,10,13,14,15,6,16,17,18,21)")
                ->order(array("co_cds_ativ_col_pratica ASC"));
        return $this->fetchAll($sql);
    }

    public function getDadosPraticas() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_ativ_col_pratica"))
                ->where("co_cds_ativ_col_pratica IN (20,2,23,9,11,25,26,27,28,22,3,24,12,30)")
                ->order(array("co_cds_ativ_col_pratica ASC"));
        return $this->fetchAll($sql);
    }
}
