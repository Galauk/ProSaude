<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_AgendamentoExameLista extends Elotech_Db_Table_Abstract {

    protected $_name = 'agendamento_exame_lista';
	protected $_primary = 'agexl_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }

}
