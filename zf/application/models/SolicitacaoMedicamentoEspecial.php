<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_SolicitacaoMedicamentoEspecial extends Elotech_Db_Table_Abstract {

    protected $_name = 'solicitacao_medicamento_especial';
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

    public function salvarPosologia($data) {
    	$recebePosologia = $data;
    	$recebeId = intval($recebePosologia[id]);
        $sql = $this->getDefaultAdapter()->query("
				UPDATE solicitacao_medicamento_especial SET quantidade = $recebePosologia[quantidade], frequencia = $recebePosologia[frequencia]
					WHERE id = $recebeId
        	");
        return $sql;
        
	}
	
	public function getRelatorioMedicamentos($id = NULL) {
		if($id != NULL){
			return $this->fetchAll("rel_sol_med_id = $id");
		}
	}

	public function deletarRelatorio($id) {
		$this->delete("rel_sol_med_id = $id");

		return true;
	}

	

}