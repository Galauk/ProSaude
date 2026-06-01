<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_CategoriaConvenios extends Elotech_Db_Table_Abstract {

    protected $_name = 'categoria_convenios';
    protected $_primary = 'catc_codigo';
    //protected $_sequence = 'seq_dom_codigo';
    //protected $_dependentTables = array();

    public function salvar(array $data) {
	try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Erro ao cadastrar Categoria: ", $exc->getMessage());
        }
    }
    
    public function listaDados(){
        return $this->fetchAll();
    }
    
    public function listaDadosEdicao($catc_codigo=FALSE){
        if ($catc_codigo!=FALSE) {
            return $this->fetchRow("catc_codigo='$catc_codigo'");
        }
    }
    
    public function listaDadosPeloNome($catc_nome=FALSE){
        if ($catc_nome!=FALSE) {
            return $this->fetchRow("catc_nome='$catc_nome'");
        }
    }
    
    public function excluir($catc_codigo=FALSE){
        $item = $this->fetchRow("catc_codigo = $catc_codigo"); 
        if ($item)
            $item->delete();
        return true;
    }
    
}
