<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoConsulta extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipo_consulta';// nome da tabela do banco
    protected $_primary = 'tp_cod'; // pk da tabela

    public function getDados(){
        return $this->fetchAll();
    }
    

}
