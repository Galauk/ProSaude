<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsFichaAtivColPubAlvo extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_ficha_ativ_col_pub_alvo';
    protected $_primary = 'co_rl_cds_ficha_ativ_col_pub_alvo';
    protected $_sequence = 'seq_co_rl_cds_ficha_ativ_col_pub_alvo';
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rl" => "rl_cds_ficha_ativ_col_pub_alvo"),array("co_cds_ativ_col_publico_alvo"))
                ->join(array("tcacpa"=>"tb_cds_ativ_col_publico_alvo"),"tcacpa.co_cds_ativ_col_publico_alvo = rl.co_cds_ativ_col_publico_alvo",array("no_cds_ativ_col_publico_alvo"))
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
        return $this->fetchAll($sql);
    }
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Público alvo: ".$ex->getMessage());
        }
    }
    
    public function excluir($codFicha=FALSE){
        $item = $this->fetchAll("co_cds_ficha_ativ_col=$codFicha");
        if(count($item)>0){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }

}
