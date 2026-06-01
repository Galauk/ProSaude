<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCdsConsumoAlimentarResposta extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_consumo_alimentar_resposta';
    protected $_primary = 'co_seq_cds_con_ali_resp';
    

    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: ".$ex->getMessage());
        }
    }

    public function excluir($codConsumo){
        $item = $this->fetchAll("co_cds_consumo_alimentar=$codConsumo");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }
}
