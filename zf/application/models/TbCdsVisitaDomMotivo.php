<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCdsVisitaDomMotivo extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_visita_dom_motivo';
    protected $_primary = 'co_cds_visita_dom_motivo';
    

    public function getMotivosParte01(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (1,29)")
                    ->order("co_cds_visita_dom_motivo");
        return $this->fetchAll($where);
    }
    
    public function getMotivosParte02(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (25,26,27,28,31)")
                    ->order("co_cds_visita_dom_motivo");
                    // die($where);
        return $this->fetchAll($where);
    }

    public function getBuscaAtiva(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (2,3,4,30)")
                    ->order("co_cds_visita_dom_motivo");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getAcompanhamento(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (5,6,7,8,9,10,11,12,13,14,15,16,17,18,32,33,19,20,21,22,23,24)")
                    ->order("co_cds_visita_dom_motivo");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getControleAmbiental(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (34,35,36,37)")
                    ->order("co_cds_visita_dom_motivo");
                    // die($where);
        return $this->fetchAll($where);
    }


    public function getOutros(){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tcvdm"=>"tb_cds_visita_dom_motivo"))
                    ->where("co_cds_visita_dom_motivo IN (25,27,31,28)")
                    ->order("co_cds_visita_dom_motivo");
                    // die($where);
        return $this->fetchAll($where);
    }
    
}
