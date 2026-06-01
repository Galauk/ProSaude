<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_GrupoAtividadeParticipante extends Elotech_Db_Table_Abstract {

	protected $_name = 'grupo_atividade_participante';
	protected $_primary = 'gap_codigo';

	public function salvar(array $data) {
		try {
			$grupo = current($data);
			$this->deletarPorGrupo($grupo["gac_codigo"]);
			foreach ($data as $part){
				parent::salvar($part);
			}
		} catch (Exception $exc) {
			throw new Zend_Validate_Exception("Erro ao cadastrar participantes: ".$exc->getMessage(), $exc->getMessage());
		}
	}

	public function deletarPorGrupo($grupo=FALSE){
		$itens = $this->fetchAll("gac_codigo=$grupo");
		if (count($itens) > 0){
			foreach ($itens as $item){
				$item->delete();
			}
		}

		return true;
	}

	public function deletar($id=FALSE){
		$item = $this->fetchRow("gap_codigo=$id");
		if ($item)
			$item->delete();

		return true;
	}

        public function atualizarUsu($de, $para){
            $de = (array)$de;

            $data = array("usu_codigo" => $para);
            $where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

            return $this->update($data, $where);
    }	
	
}
