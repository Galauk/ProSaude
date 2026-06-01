<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_LeitoGradeModelo extends Elotech_Db_Table_Abstract {

    protected $_name = 'leito_grade_modelo';
	protected $_primary = 'lgm_codigo';
    protected $_dependentTables = array('LeitoGradeModeloItens');

    public function salvar(array $data) {
		//die("here");
		if($data['lgm_codigo'] == "_empty")
			unset($data['lgm_codigo']);
		
                        //echo "aaaaa";die();
			//$this->notEmpty(array("lgc_codigo","lgm_descricao", "lgm_intervalo","lgm_repeticoes"), $data);
		
        return parent::salvar($data);
    }
	
	public function getModelos($lgc_codigo){
		return $this->fetchAll("lgc_codigo=$lgc_codigo", "lgm_descricao");
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

	public function getModelo($lgm_codigo){
		$tbLGMI = new Application_Model_LeitoGradeModeloItens();
		
		$modelo = $this->fetchRow("lgm_codigo=$lgm_codigo");		
		$produtos = $tbLGMI->getModelo($lgm_codigo);
		
		$out = array(
			"intervalo" => $modelo->lgm_intervalo,
			"repeticoes" => $modelo->lgm_repeticoes,
			"produtos" => $produtos->toArray()
		);
		
		return $out;
	}
}
