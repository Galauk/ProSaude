<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoDeMetodos extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipodemetodos';// nome da tabela do banco
    protected $_primary = 'tpm_codigo'; // pk da tabela

    public function listaTiposDeMetodos(){
        return $this->fetchAll();
    }
    
    public function getTipoPorProcedimento($proc_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("tpm"=>"tipodemetodos"))
                      ->join(array("txa"=>"tipodeexame"),"txa.tpm_codigo=tpm.tpm_codigo","")
                      ->where("txa.proc_codigo=$proc_codigo");
        return $this->fetchRow($where);
    }
    

}
