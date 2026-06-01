<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_LeitoGradeCategoria extends Elotech_Db_Table_Abstract {

    protected $_name = 'leito_grade_categoria';
	protected $_primary = 'lgc_codigo';

    public function salvar(array $data) {
        
        if(empty($data['lgc_codigo']))
                $this->notEmpty(array("lgc_descricao"), $data);
        
        $this->peloMenosUm(array("lgc_descricao"), $data);
        return parent::salvar($data);
    }
	/**
	 * Retorna o nome da categoria
	 * @param int $lgc_codigo
	 * @return string
	 */
	
	public function getNomeCategoria($lgc_codigo){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("lgc"=>"leito_grade_categoria"))
				->where("lgc_codigo = ?",$lgc_codigo);
		
		return $this->fetchRow($where)->lgc_descricao;
		
	}
	
	public function getCategorias(){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("lgc"=>"leito_grade_categoria"))
				->order("lgc_descricao");
		
		return $this->fetchAll($where);
	}
        
        public function pesquisarCategoria($dados) {
		$where = $this->select(true);
		if (is_string($dados))
			$where->where("lgc_descricao ilike '%$dados%'");

		return $this->fetchAll($where);
	}
        
        public function editar($id){
            $line = $this->fetchRow("lgc_codigo=$id");
            return $line;
        }
        
        public function excluir($id){
            $tbLGM = new Application_Model_LeitoGradeModelo();
            /* @var $tbLGM zend_db_table_abstract */
            $filhos = $tbLGM->fetchAll("lgc_codigo=$id");
            if($filhos){
                throw new Zend_Validate_Exception("Esse registro Possui dependências"); // ;) try!
            }
            else {

                $item = $this->fetchRow("lgc_codigo=$id");
                if ($item)
                    $item->delete();
            } // nem pode ser isso
            
            return true;
                
        }
}
