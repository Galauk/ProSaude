<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoAtendimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipo_atendimento';// nome da tabela do banco
    protected $_primary = 'tat_codigo'; // pk da tabela

    public function getTiposDeAtendimento(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo <> '3'");
        return $this->fetchAll($sql);
    }
    
    public function getTiposDeAtendimento01(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN ('1','2')");
        return $this->fetchAll($sql);
    }
    
    public function getTiposDeAtendimento02(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN ('4','5','6')");
        return $this->fetchAll($sql);
    }
    public function getTiposDeAtendimento03(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN ('7','8','9')");
        return $this->fetchAll($sql);
    }
    
    
    public function getTiposDeAtendimentoFicha(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN (2)");
        return $this->fetchRow($sql);
    }
    
    public function getDemandaEspontanea(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN (4,5,6)");
        return $this->fetchAll($sql);
    }

    public function getLocalAtendimentoOdonto(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ta"=>"tipo_atendimento"))
                    ->where("tat_codigo IN (2,4,5,6)");
        return $this->fetchAll($sql);
    }

}
