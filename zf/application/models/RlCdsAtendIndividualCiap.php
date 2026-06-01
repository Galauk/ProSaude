<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_RlCdsAtendIndividualCiap extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_atend_individual_ciap';
    protected $_primary = 'id';
    

    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar CIAP: ".$ex->getMessage());
        }
        return true;
    }
    
    public function limpaCiapAtendimento($ate_codigo){
        $where = $this->select()->where("ate_codigo IN (?)", $ate_codigo)->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        return $this->delete($where);
    }
    
    public function getCiapAtendimento($ate_codigo=false){
        if(empty($ate_codigo))
            return false;
        
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("ate_ciap"=>"rl_cds_atend_individual_ciap"),"")
                      ->join(array("ciap"=>"tb_ciap"),"ciap.co_seq_ciap=ate_ciap.co_ciap",array("co_seq_ciap","ds_ciap","co_ciap"))
                      ->where("ate_codigo=$ate_codigo");
        // die($where);
        return $this->fetchAll($where);
    }
    
    
}
