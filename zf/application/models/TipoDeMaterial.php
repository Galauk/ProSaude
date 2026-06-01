<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoDeMaterial extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipodematerial';// nome da tabela do banco
    protected $_primary = 'tma_codigo'; // pk da tabela

    public function listaTipoDeMaterial(){
        return $this->fetchAll();
    }
    

    public function getTipoPorProcedimento($proc_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("tma"=>"tipodematerial"))
                      ->join(array("txa"=>"tipodeexame"),"txa.tma_codigo=tma.tma_codigo","")
                      ->where("txa.proc_codigo=$proc_codigo");
        return $this->fetchRow($where);
    }
}
