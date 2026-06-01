<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Ocupacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_ocupacao';
    protected $_primary = 'co_ocupacao';
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    /**
	 * Buscar os Ocupacao
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE) {
		if ($term){
			$busca = "no_ocupacao ilike retira_acentos('%$term%')";
		}

		$all = $this->fetchAll($busca, "no_ocupacao");

		$out = array();
		foreach ($all as $ocupacao) {
			$out [] = array(
				"id" => $ocupacao->co_ocupacao,
				"label" => trim($ocupacao->no_ocupacao),
				"data" => array("no_ocupacao" => $ocupacao->no_ocupacao,"co_ocupacao" => $ocupacao->co_ocupacao)
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("co_ocupacao" => "")
			);
		}

		return $out;
	}

	public function validaOcupacao($ocu){
		$sql = $this->select("co_ocupacao")->setIntegrityCheck(false)->where("co_ocupacao = ?", $ocu);

		$row = $this->fetchRow($sql)->co_ocupacao;
		
		$result = $row == NULL ? "010000" : $row;
		
		return $result;
	}
}