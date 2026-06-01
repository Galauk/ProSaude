<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_RlCdsVisitaDomMotivo extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_visita_dom_motivo';
    protected $_primary = 'co_cds_visita_domiciliar_rl';
    

    public function salvar($dados) {

        // echo "<pre>";print_r($dados);die();
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: ".$ex->getMessage());
        }
        return true;
    }
    
    public function getMotivosDoAtendimento($co_cds_visita_domiciliar=false){
 
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("rcvdm"=>"rl_cds_visita_dom_motivo"))
                      ->where("rcvdm.co_cds_visita_domiciliar=$co_cds_visita_domiciliar");
        //die($where);
        return $this->fetchAll($where);
    }
    
}
