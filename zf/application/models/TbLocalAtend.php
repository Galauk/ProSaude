<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbLocalAtend extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_local_atend';
    protected $_primary = 'co_local_atend';
    protected $_sequence = 'seq_co_local_atend';

    public function selectTag($selected=FALSE) {
        $where = $this->select()->order("co_local_atend ASC");
        return parent::selectTag($where, "no_local_atend", "co_local_atend", $first, TRUE, "co_local_atend", "co_local_atend", NULL, $selected);
    }

    public function selectTagLocalOdontologia($selected=FALSE) {
        $where = $this->select()->order("co_local_atend ASC")
                ->where("co_local_atend BETWEEN 1 AND 10");
        return parent::selectTag($where, "no_local_atend", "co_local_atend", $first, TRUE, "co_local_atend", "co_local_atend", NULL, $selected);
    }

    public function selectTagLocalAtendimento($selected=FALSE) {
        $where = $this->select()->order("co_local_atend ASC")
                ->where("co_local_atend BETWEEN 1 AND 10");
        return parent::selectTag($where, "no_local_atend", "co_local_atend", $first, TRUE, "co_local_atend", "co_local_atend", NULL, $selected);
    }
}
