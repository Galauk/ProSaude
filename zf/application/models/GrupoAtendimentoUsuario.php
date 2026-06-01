<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_GrupoAtendimentoUsuario extends Elotech_Db_Table_Abstract {

	protected $_name = 'grupo_atendimento_usuario';
	protected $_primary = 'gau_codigo';
	protected $_dependentTables = array();

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
	public function salvar(array $data) {
		throw new Zend_Validate_Exception("Este método ainda não possui validações", 1000);
		return parent::salvar($data);
	}

	/**
	 * Atenção!
	 * usu_codigo é único nesta tabela!
	 * 
	 * 
	 * Atualiza todas os itens, alterando o usu_codigo.
	 * Método usado para tirar a duplicação de pacientes
	 * @see Application_Model_Usuario::removerDuplicacoes()
	 * @param array|int $de
	 * @param int $para 
	 * @return int Número de linhas atualizadas
	 */
	public function atualizarUsu($de, $para) {
		$de = (array) $de;

		$tudo = $de;
		$tudo[] = $para;
		
		// selecionar todos os relacionamentos que envolvem esses usuario
		$where = current($this->select()->where("usu_codigo IN (?)", $tudo)->getPart(Zend_Db_Table_Select::WHERE));
		$rel = $this->fetchAll($where);
		
		$gruposDoUsuario = array();
		$relacionamentosQueSeraoRemovidos = array();
		
		// olha cada relaciomento
		// ignora a primeira aparição de um grupo
		// mas salva as seguintes no array: $relacionamentosQueSeraoRemovidos
		foreach($rel as $item){
			if(in_array($item->gruate_codigo, $gruposDoUsuario)){
				$relacionamentosQueSeraoRemovidos []= $item->gau_codigo;
			} else {
				$gruposDoUsuario []= $item->gruate_codigo;
			}
		}
		
		if(count($relacionamentosQueSeraoRemovidos)){
			// exclui os relacionamentos que serão duplicados
			$where = current($this->select()->where("gau_codigo IN (?)", $relacionamentosQueSeraoRemovidos)->getPart(Zend_Db_Table_Select::WHERE));
			Zend_Registry::get("logger")->log(sprintf("Enxcluindo os grupo_atendimento_usuario (%s) para evitar duplicidade de USUxGRUPO", implode(",",$relacionamentosQueSeraoRemovidos)), Zend_Log::INFO);
			$this->delete($where);
		}

		// agora é seguro fazer a atualização
		$data = array("usu_codigo" => $para);
		$where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];

		Zend_Registry::get("logger")->log("atualizando usuarios em " . $this->_name, Zend_Log::INFO);

		return $this->update($data, $where);
	}

}
