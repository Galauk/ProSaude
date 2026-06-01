<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsAtivColPublicoAlvo extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_ativ_col_publico_alvo';
    protected $_primary = 'co_cds_ativ_col_publico_alvo';
    protected $_sequence = 'seq_co_cds_ativ_col_publico_alvo';

    public function getDados() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_ativ_col_publico_alvo"))
                ->where("co_cds_ativ_col_publico_alvo <> 11")    
                ->order(array("co_cds_ativ_col_publico_alvo"));
        return $this->fetchAll($sql);
    }

}
