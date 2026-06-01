<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Conselho extends Elotech_Db_Table_Abstract {

    protected $_name = 'conselho';
    protected $_primary = 'con_codigo';
   // protected $_dependentTables = array('Atendimento');

    public function getConselhos()
    {
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("con" => "conselho"), array("(con_descricao || ' - ' || no_curto_conselho) as con_descricao", "con_codigo"))
            ->order("con_descricao");

        return $this->fetchAll($where);
    }

    public function getConselho($id){
        if($id != ""){
            $where = $this->select(false)->where("con_codigo = $id");
            $resultSet = $this->fetchRow($where);

            return $resultSet->con_codigo;
        } else {
            return 0;
        }
        
    }
   
 
}
