<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Prevalencia extends Elotech_Db_Table_Abstract {

	protected $_name = 'prevalencia';
	protected $_primary = 'prev_codigo';
    protected $_sequence = 'seq_prev_codigo';

    public function getValor($co_ciap=FALSE, $uf_codigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("prev" => "prevalencia"), array("prev_valor"));

                    if($data_inicial){
                        $where->where("prev.co_seq_ciap=",$co_ciap);
                    }
                    if($data_final){
                        $where->where("prev.uf_codigo=",$uf_codigo);
                    }
        return $this->fetchRow($sql);
    }

}