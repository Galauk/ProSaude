<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Fabricante extends Elotech_Db_Table_Abstract {

	protected $_name = 'fabricante';
	protected $_primary = 'fab_codigo';
	//protected $_sequence = "seq_for_codigo";
	/**
	 * Buscar os fornecedores
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("fab" => "fabricante"), array("fab_codigo", "fab_descricao"))
				->where("retira_acentos(fab_descricao) ilike retira_acentos('%$term%')")
				->order("fab_descricao");

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $for) {
			$out [] = array(
				"id" => $for->fab_codigo,
				"label" => trim($for->fab_descricao),
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
		$fab_codigo = parent::salvar($data);
	}
        
        public function getFabricantes(){
            $where = $this->select(FALSE)
                           ->setIntegrityCheck(FALSE)
                            ->from("fabricante");		
           
            //$where->limit(15);
            return $this->fetchAll($where);
        }
        
        public function excluir($fab_codigo) {
            $item = $this->fetchRow("fab_codigo=$fab_codigo");
            if ($item)
                $item->delete();

            return true;
        }
        
        public function getFabricante($fab_codigo){
            if(!$fab_codigo)
                RETURN FALSE;
            
            $where = $this->select()
                          ->setIntegrityCheck(FALSE)
                          ->from("fabricante")
                          ->where("fab_codigo=$fab_codigo");
            
            return $this->fetchRow($where);
        }
        
        public function pesquisar($dados=FALSE, $limit=FALSE) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("fab"=>"fabricante"));		
            if (is_string($dados))
                    $where->where("fab_descricao ilike '%$dados%' or fab_cnpj ilike '%$dados%' or fab_endereco ilike '%$dados%'");
            
            if(is_numeric($dados) || is_float($dados)){
                $where->where("fab_codigo = $dados");
            }
            if ($limit) {
                    $where->limit(15);
            }
            //die($where);
            return $this->fetchAll($where);
    }
        
}
