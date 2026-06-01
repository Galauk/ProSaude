<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCdsVisitaDomiciliar extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_visita_domiciliar';
    protected $_primary = 'co_seq_cds_visita_domiciliar';
    

    public function salvar($dados) {
      // echo "<pre>";print_r($dados);die();
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: ".$ex->getMessage());
        }
      
    }
    
    public function getVisitaDoAtendimento($ateCodigo = false){

        $sql = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("tcvd"=>"tb_cds_visita_domiciliar"),array("co_seq_cds_visita_domiciliar","co_cds_visita_dom_desfecho","ate_codigo"))
                      ->where("tcvd.ate_codigo=$ateCodigo")
                      ->order("co_seq_cds_visita_domiciliar DESC");
        //die($sql);
        return $this->fetchRow($sql);
    }
    
}
