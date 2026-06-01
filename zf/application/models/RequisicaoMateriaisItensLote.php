<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RequisicaoMateriaisItensLote extends Elotech_Db_Table_Abstract {
    
    protected $_name = "requisicao_materiais_itens_lote";
    protected $_primary = "remil_codigo";
    
    public function listaLotesPorRequisicao($codRequisicaoItens){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("remil"=>"requisicao_materiais_itens_lote"),array("remil_codigo","remil_lote","remil_quantidade"))
                    ->join(array("remi"=>"requisicao_materiais_itens"),"remil.remi_codigo=remi.remi_codigo",array("pro_codigo"))
                    ->where("remil.remi_codigo =?",$codRequisicaoItens);
        //die($sql);
        return $this->fetchAll($sql);
    }
    
    public function salvar(array $data) {

        // validação:
        //echo "<pre>".print_r($data,1);die();
        $this->emptyToUnset($data);
        $this->notEmpty(array("remi_codigo", "remil_quantidade","remil_lote"), $data);

        return parent::salvar($data);
    }
    
}
