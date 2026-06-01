<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlCdsFichaAtivColProf extends Elotech_Db_Table_Abstract {

    protected $_name = 'rl_cds_ficha_ativ_col_prof';
    protected $_primary = 'co_rl_cds_ficha_ativ_col_prof';
    protected $_sequence = 'seq_co_rl_cds_ficha_ativ_col_prof';
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rl" => "rl_cds_ficha_ativ_col_prof"))
                ->join(array("usr"=>"usuarios"),"rl.usr_codigo=usr.usr_codigo",array("usr_nome"))    
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
        return $this->fetchAll($sql);
    }
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Profissionais: ".$ex->getMessage());
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
