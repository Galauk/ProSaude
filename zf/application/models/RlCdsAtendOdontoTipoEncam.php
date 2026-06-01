<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsAtendOdontoTipoEncam extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_atend_odonto_tipo_encam';
    protected $_primary = 'co_rl_cds_atend_odonto_tipo_encam';
    protected $_sequence = 'seq_co_rl_cds_atend_odonto_tipo_encam';
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar relação Conduta: ".$ex->getMessage());
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
                    ->from(array("tbl"=>"rl_cds_atend_odonto_tipo_encam"))
                    ->where("ate_codigo =?",$ateCod);
        return $this->fetchAll($sql);
    }

}
