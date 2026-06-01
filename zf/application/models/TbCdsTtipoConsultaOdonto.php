<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsTtipoConsultaOdonto extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_tipo_consulta_odonto';
    protected $_primary = 'co_cds_tipo_consulta_odonto';
    protected $_sequence = 'seq_co_cds_tipo_consulta_odonto';

    public function getDadosTema() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_cds_tipo_consulta_odonto"))
                ->order(array("co_cds_tipo_consulta_odonto"));
        return $this->fetchAll($sql);
    }

}
