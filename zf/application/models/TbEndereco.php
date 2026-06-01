<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbEndereco extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_endereco';
    protected $_primary = 'co_seq_endereco';
    
    public function salvar($data) {
        try {
            parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar domicilio: ".$exc->getMessage());
        }
    }
    
}
