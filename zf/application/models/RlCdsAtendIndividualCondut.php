<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_RlCdsAtendIndividualCondut extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_atend_individual_condut';
    protected $_primary = 'co_rl_cds_atend_individual_condut';
    protected $_sequence = 'seq_co_rl_cds_atend_individual_condut';

    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar relação conduta: ".$ex->getMessage());
        }
        return true;
    }
    
    public function getDadosPorAtendimento($ateCod=FALSE) {
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbl" => "rl_cds_atend_individual_condut"),array("tp_cds_conduta"))
                    ->where("ate_codigo =?",$ateCod);
        return $this->fetchAll($sql);
    }
    
    public function getDadosPorVisita($ateCod=FALSE) {
        $ateCod = $ateCod;
        // die($ateCod);
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT cds_vd.ate_codigo, cds_vd.co_seq_cds_visita_domiciliar, cds_vm.co_cds_visita_dom_motivo
                    FROM tb_cds_visita_domiciliar AS cds_vd
                        INNER JOIN rl_cds_visita_dom_motivo AS cds_vm
                            ON cds_vd.co_seq_cds_visita_domiciliar = cds_vm.co_cds_visita_domiciliar
                        WHERE cds_vd.ate_codigo = $ateCod;
            "
        )->fetchAll();

        return $sql;
    }
    public function excluirPorAtendimento($ateCod){
        $item = $this->fetchAll("ate_codigo=$ateCod");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }
    
}