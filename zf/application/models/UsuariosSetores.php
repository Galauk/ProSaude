<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UsuariosSetores extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuarios_setores';
    protected $_primary = 'uset_codigo';
    protected $_dependentTables = array();
     protected $_sequence = 'seq_uset_usuarios_setores';
    
	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function getSetoresPorUsuario($usr_codigo=FALSE){
        
        if($usr_codigo == FALSE)
            return false;
         
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("usr_set" => "usuarios_setores"))
                      ->where("usr_codigo=?",$usr_codigo);
        
        return $this->fetchAll($where);
    }
    
    public function excluir($uset_codigo=FALSE) {
            $item = $this->fetchRow("uset_codigo=$uset_codigo");
            if ($item) {
                    $item->delete();
            }
    }
    
    public function getSetoresPorUsuarios($usr_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("uset"=>"usuarios_setores"))
                      ->join(array("set"=>"setor"),"set.set_codigo=uset.set_codigo")
                      ->where("usr_codigo=$usr_codigo");
        return $this->fetchAll($where);
    }

}
