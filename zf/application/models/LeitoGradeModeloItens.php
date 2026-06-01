<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_LeitoGradeModeloItens extends Elotech_Db_Table_Abstract {

	protected $_name = 'leito_itens_grade_modelo';
	protected $_primary = 'ligm_codigo';
	protected $_referenceMap = array(
		'Modelo' => array(
			'columns' => 'lgm_codigo',
			'refTableClass' => 'Application_Model_LeitoGradeModelo',
			'refColumns' => 'lgm_codigo'
		)
	);

	public function salvar(array $data) {
		
		$this->filterDigits(array("ligm_quantidade","pro_codigo"), $data);
		$this->notEmpty(array("pro_codigo"), $data);
		$this->maiorQueZero(array("ligm_quantidade"), $data);
		
		return parent::salvar($data);
	}

	public function getModelo($lgm_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ligm" => "leito_itens_grade_modelo"), "ligm_quantidade")
				->join(array("pro" => "produto"), "pro.pro_codigo=ligm.pro_codigo", array("pro_nome","pro_codigo"))
				->where("lgm_codigo=?", $lgm_codigo)
				->order("pro_nome");
		
		return $this->fetchAll($where);
	}

	public function getGridResource($page = 1, $limit = FALSE, $sidx = NULL, $sord = "ASC", $where = NULL) {
		$this->setFields(array("ligm_codigo","pro_codigo","pro_nome","ligm_quantidade"));
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ligm"=>"leito_itens_grade_modelo"),array("ligm_codigo","ligm_quantidade"))
				->join(array("pro"=>"produto"),"pro.pro_codigo=ligm.pro_codigo",array("pro_codigo","pro_nome"))
				->where("lgm_codigo=$where");
		
		return parent::getGridResource($page, $limit, $sidx, $sord, $where);
	}
}
