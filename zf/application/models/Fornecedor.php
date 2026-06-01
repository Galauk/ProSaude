<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Fornecedor extends Elotech_Db_Table_Abstract {

	protected $_name = 'fornecedor';
	protected $_primary = 'for_codigo';
	protected $_sequence = "seq_for_codigo";
	/**
	 * Buscar os fornecedores
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("for" => "fornecedor"), array("for_codigo", "for_nome"))
				->where("retira_acentos(for_nome) ilike retira_acentos('%$term%')")
				->order("for_nome");

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $for) {
			$out [] = array(
				"id" => $for->for_codigo,
				"label" => trim($for->for_nome),
				"data" => $for->toArray()
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}

		return $out;
	}
	
	// Método que salva os fornecedores em BD
	public function salvar(array $data) {
		$for_codigo = parent::salvar($data);
	}
        
        public function selectTag($id_select=FALSE,$for_codigo=FALSE) {
            if($id_select == FALSE)
                $id_select = "for_codigo";
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("f" => "fornecedor"), array("for_codigo", "for_nome"))
                            ->order("for_nome");
            return parent::selectTag($where, "for_nome", NULL, TRUE, TRUE, NULL, $id_select, TRUE,$for_codigo);
	}
}
