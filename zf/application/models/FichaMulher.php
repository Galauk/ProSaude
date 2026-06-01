<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_FichaMulher extends Elotech_Db_Table_Abstract {

    protected $_name = 'ficha_mulher';
    protected $_primary = 'fim_codigo';

    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	

    public function excluir($fim_codigo=FALSE) {
            $item = $this->fetchRow("fim_codigo=$fim_codigo");
            if ($item) {
                    $item->delete();
            }
    }
    
    public function getDadosFicha($usuCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("fim"=>"ficha_mulher"))
                    ->where("fim.usu_codigo =?",$usuCodigo);
        return $this->fetchRow($sql);
    }
    
    public function atualizarUsu($de, $para){
            $de = (array)$de;

            $data = array("usu_codigo" => $para);
            $where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

            return $this->update($data, $where);
    }
}
