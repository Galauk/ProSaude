<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_GradeExameUnidade extends Elotech_Db_Table_Abstract {

    protected $_name = 'grade_exame_unidade';
	protected $_primary = 'graexuni_codigo';
	protected $_sequence = 'seq_graex_codigo';

	public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
}
