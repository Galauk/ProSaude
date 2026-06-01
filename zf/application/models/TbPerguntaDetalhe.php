<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbPerguntaDetalhe extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_pergunta_detalhe';
    protected $_primary = 'co_pergunta_detalhe';
    
    public function getPerguntaDetalhe($pergunta=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tpe"=>"tb_pergunta_detalhe"),array("co_pergunta_detalhe","ds_pergunta_detalhe"))
                    ->where("co_pergunta=$pergunta");
        return $this->fetchAll($sql);
    }
    
}
