<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Estratificacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'estratificacao';
	protected $_primary = 'est_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    public function getEstratificacoes($est_codigo=false) {
		$where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("est" => "estratificacao"));
                
                if($est_codigo){
               		$where->where("est_codigo=?",$est_codigo);                	
                }
        return $this->fetchAll($where);
    }

}
