<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_MedicamentoEspecial extends Elotech_Db_Table_Abstract {

	protected $_name = 'relatorio_solicitacao_medicamentos';
	protected $_primary = 'id';

	/**
	* Persiste um item (insert ou update)
	* @param array $data array de chave=>valor, cada chave corresponde a um atributo
	* @return int primary key do item (nextVal para insert) 
	*/
	public function salvar(array $data) {
		// throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
		return parent::salvar($data);
	}

	public function getRelatorios() {
		$query = $this->select(FALSE)->order('id DESC');
		

		// die($query);
		return $this->fetchAll($query);
	}
	
	public function getRelatorio($id = NULL) {
		return $this->fetchRow("id = $id");
	}
	
	public function getRelatorioMedicamento($id = NULL) {
		return $this->fetchRow("id=$id");
	}

	public function deletarRelatorio($id) {
		$this->delete("id = $id");

		return true;

	}
}