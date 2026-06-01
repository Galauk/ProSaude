<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsAtendOdontTipVigBuc extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_atend_odont_tip_vig_buc';
    protected $_primary = 'co_rl_cds_atend_odont_tip_vig_buc';
    protected $_sequence = 'seq_co_rl_cds_atend_odont_tip_vig_buc';
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar relação Vigilância: ".$exc->getMessage());
        }
    }
    
    public function excluirPorAtendimento($ateCod){
        $item = $this->fetchAll("ate_codigo=$ateCod");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }
    
    public function getDadosPorAtendimento($ateCod=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbl"=>"rl_cds_atend_odont_tip_vig_buc"))
                    ->where("ate_codigo =?",$ateCod);
        return $this->fetchAll($sql);
    }

}
