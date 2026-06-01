<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsFichaAtivColTema extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_ficha_ativ_col_tema';
    protected $_primary = 'co_rl_cds_ficha_ativ_col_tema';
    protected $_sequence = 'seq_co_rl_cds_ficha_ativ_col_tema';
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rl" => "rl_cds_ficha_ativ_col_tema"),array("co_cds_ativ_col_tema"))
                ->join(array("tcact"=>"tb_cds_ativ_col_tema"),"tcact.co_cds_ativ_col_tema = rl.co_cds_ativ_col_tema",array("no_cds_ativ_col_tema"))
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
        return $this->fetchAll($sql);
    }
    
    public function salvar($dados) {
        // echo "<pre>";print_r($dados);die();
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Temas: ".$ex->getMessage());
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
