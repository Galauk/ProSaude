<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ProdutoSetor extends Elotech_Db_Table_Abstract {

    protected $_name = 'produto_setor';
    protected $_primary = 'prset_codigo';
    protected $_dependentTables = array();
     protected $_sequence = 'seq_prset_codigo';
    
	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function verificaVinculoProdutoSetor($pro_codigo=FALSE,$set_codigo=FALSE){
        
        if($pro_codigo == FALSE || $set_codigo == false)
            return false;
         
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("pro_set" => "produto_setor"),"COUNT(prset_codigo) as qtde")
                      ->where("pro_codigo=?",$pro_codigo)
                      ->where("set_codigo=?",$set_codigo);
        
        $count = $this->fetchRow($where);
        if($count->qtde >= 1){
            $produto_vinculado = 1;
        }else{
            $produto_vinculado = 0;
        }
        
        return $produto_vinculado;
    }

}
