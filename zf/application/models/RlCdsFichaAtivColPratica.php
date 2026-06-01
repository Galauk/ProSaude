<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsFichaAtivColPratica extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_ficha_ativ_col_pratica';
    protected $_primary = 'co_rl_cds_ficha_ativ_col_pratica';
    protected $_sequence = 'seq_co_rl_cds_ficha_ativ_col_pratica';
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rl" => "rl_cds_ficha_ativ_col_pratica"),array("co_cds_ativ_col_pratica"))
                ->join(array("tcacp"=>"tb_cds_ativ_col_pratica"),"tcacp.co_cds_ativ_col_pratica = rl.co_cds_ativ_col_pratica",array("no_cds_ativ_col_pratica"))
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
                // die($sql);
        return $this->fetchAll($sql);
    }
    
    public function salvar($dados) {
        // echo "<pre>";print_r($dados);die();
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Práticas / temas para saúde: ".$ex->getMessage());
        }
    }
    
    public function excluir($codFicha=FALSE){
        $item = $this->fetchAll("co_cds_ficha_ativ_col=$codFicha");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }

}
