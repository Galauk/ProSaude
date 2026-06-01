<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_LeitoGradeItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'leito_itens_grade';
	protected $_primary = 'lig_codigo';
    protected $_referenceMap = array(
        'Grade' => array(
            'columns' => 'lgra_codigo',
            'refTableClass' => 'Application_Model_LeitoGrade',
            'refColumns' => 'lgra_codigo'
        ),
        'Produto' => array(
            'columns' => 'pro_codigo',
            'refTableClass' => 'Application_Model_Produto',
            'refColumns' => 'pro_codigo'
        )
    );

    public function salvar(array $data) {
		$this->filterDigits(array("pro_codigo","lgra_codigo","lig_quantidade"), $data);
		$this->notEmpty(array("pro_codigo","lgra_codigo"), $data);
		$this->maiorQueZero(array("lig_quantidade"), $data);
		
        return parent::salvar($data);
    }
	
	public function getItens($lgra_codigo){
		return $this->fetchAll("lgra_codigo=$lgra_codigo");
	}

}
