<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PMAAtributos extends Elotech_Db_Table_Abstract {

    protected $_name = 'pma2_atributos';
	protected $_primary = 'pmaa_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Não é possível criar novos atributos para o PMA2", 1000);
    }
	
	/**
	 * Retonar um array com todas os atributos do PMA2 e as respectivas chaves primárias
	 * @return array['código do atributo']=chave primária 
	 */
	public function getChaveAtributos(){
		$tudo = $this->fetchAll(NULL, "pmaa_codigo");
		$retorno = array();
		foreach($tudo as $item)
			$retorno[ $item->pmaa_chave ] = $item->pmaa_codigo;
		
		return $retorno;
	}

}
