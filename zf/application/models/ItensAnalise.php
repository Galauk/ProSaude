<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ItensAnalise extends Elotech_Db_Table_Abstract {

    protected $_name = 'itensanalise';
    protected $_primary = 'ite_codigo';

    /*public function getItensLaudo($meses=FALSE,$sexo=FALSE,$proc_codigo=FALSE){
        $condicoes = $this->verificaSubexames($proc_codigo)->toArray();
        $array_laudos = array();
        foreach($condicoes as $condicao){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ite"=>"itensanalise"),array("ite_codigo","ite_itemdoexame","ite_tipo_medida","sex_codigo"))
                          ->joinLeft(array("vlr"=>"valoresdereferencia"),"vlr.ite_codigo=ite.ite_codigo","vlr_valordereferencia")
                          ->joinLeft(array("sex"=>"subexame"),"sex.sex_codigo=ite.sex_codigo","sex_subexame")
                          ->where("vlr_faixa_etaria_inicio <= $meses OR vlr_faixa_etaria_inicio IS NULL")
                          ->where("vlr_faixa_etaria_fim > $meses OR vlr_faixa_etaria_fim IS NULL")
                          ->where("vlr_sexo = '$sexo' OR vlr_sexo is null")
                          ->order("ite.sex_codigo")
                          ->order("ite.ite_codigo");
            if($condicao[sex_codigo]== ""){
                $where->where("ite.txa_codigo=$condicao[txa_codigo]");
            }else{
                $where->where("ite.sex_codigo=$condicao[sex_codigo]");
            }
            $registros = $this->fetchAll($where);
            foreach($registros as $registro){
                $array_laudos[$registro[ite_codigo]] = array("sex_codigo"=>$registro[sex_codigo],
                                                             "ite_itemdoexame"=>$registro[ite_itemdoexame],
                                                             "vlr_valordereferencia"=>$registro[vlr_valordereferencia],
                                                             "ite_tipo_medida"=>$registro[ite_tipo_medida],
                                                             "sex_subexame"=>$registro[sex_subexame]);
            }
        }
        return $array_laudos;
        
    }*/
    
    private function verificaSubexames($proc_codigo){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("txa"=>"tipodeexame"))
                      ->joinLeft(array("sub"=>"subexame"),"sub.txa_codigo=txa.txa_codigo",array("sex_codigo","sex_subexame"))
                      ->where("proc_codigo=$proc_codigo");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getItens($txa_codigo){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("ite"=>"itensanalise"))
                      ->where("txa_codigo=$txa_codigo")
                      ->order("ite_codigo");
        return $this->fetchAll($where);
    }

}
