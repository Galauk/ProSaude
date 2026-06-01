<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Syslog extends Elotech_Db_Table_Abstract {
    
    protected $_name = 'syslog';
    protected $_schema = 'aise';
    protected $_primary = 'idlog';
    protected $_sequence = 'aise.s90syslog';
    
    // Efetua a inserção de pessoa no banco do AISE
    public function salvar(array $data) {
        
        try {
            $pessoa = parent::salvar($data);
            //$a = 1;
        } catch (Exception $exc) {
            //throw new Zend_Validate_Exception($exc->getMessage());
            throw new Zend_Validate_Exception("Falha ao cadastrar log de pessoa!".$exc->getMessage()); 
        }
        return $pessoa;        
    }
    
}
