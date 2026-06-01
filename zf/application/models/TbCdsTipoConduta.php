<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTipoConduta extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_conduta';
    protected $_primary = 'co_cds_tipo_conduta';
    protected $_sequence = 'seq_co_cds_ativ_col_tema';

    public function getDados() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_conduta"))
                ->where("co_cds_tipo_conduta IN (1,2,3,9,12)")    
                ->order(array("co_cds_tipo_conduta"));
        return $this->fetchAll($sql);
    }
    
    public function getDadosEncaminhamento() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_conduta"))
                ->where("co_cds_tipo_conduta IN (11, 4, 5, 6, 7, 8, 10)")    
                ->order(array("co_cds_tipo_conduta"));
        return $this->fetchAll($sql);
    }
    
}
