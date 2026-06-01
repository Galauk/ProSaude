<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_FichaEstratificacaoRefGrupo extends Elotech_Db_Table_Abstract {

    protected $_name = 'ficha_estratificacao_ref_grupo';
	protected $_primary = 'referencia_grupo_codigo';

    public function salvar(array $data) {
        
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar a ficha de estratificação ".$exc->getMessage());
        }
    }
    

}
