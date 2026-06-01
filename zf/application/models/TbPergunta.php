<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbPergunta extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_pergunta';
    protected $_primary = 'co_seq_pergunta';
    
    
    
    public function getPerguntasPorContexto($contexto=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tpe"=>"tb_pergunta"),array("co_seq_pergunta","ds_pergunta","tp_pergunta","co_pergunta_pai"));
        
        if($contexto)
            $sql->where("co_contexto_pergunta=$contexto");
        
        return $this->fetchAll($sql);
    }
    
}
