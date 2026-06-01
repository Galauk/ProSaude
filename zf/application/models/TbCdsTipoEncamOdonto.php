<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTipoEncamOdonto extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_encam_odonto';
    protected $_primary = 'co_cds_tipo_encam_odonto';
    protected $_sequence = 'seq_co_cds_tipo_encam_odonto';

    public function getDados() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_encam_odonto"))
                ->where("co_cds_tipo_encam_odonto IN (16,12,13,14,15)")
                ->order(array("co_cds_tipo_encam_odonto"));
        return $this->fetchAll($sql);
    }
    
    public function getEncaminhamentos() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_encam_odonto"))
                ->where("co_cds_tipo_encam_odonto NOT IN (16,12,13,14,15)")
                ->order(array("co_cds_tipo_encam_odonto"));
        return $this->fetchAll($sql);
    }
    
}
