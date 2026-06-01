<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PMARelacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'pma2_relacao';
	protected $_primary = 'pmar_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		
		if(empty($data['pmar_valor_sistema']))
			$data['pmar_valor_sistema'] = 0;
		
		if(empty($data['pmar_valor_digitado']))
			$data['pmar_valor_digitado'] = 0;
		
		try{
        return parent::salvar($data);
		} catch(Exception $e){
			throw $e;
		}
    }

	public function delPmaRel($pma_codigo) {
		$tbPMARel = new Application_Model_PMARelacao();
		$dados = $tbPMARel->delete("pma_codigo=$pma_codigo");
	}	

}
