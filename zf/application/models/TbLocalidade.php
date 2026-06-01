<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbLocalidade extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_localidade';
    protected $_primary = 'co_localidade';
    
    public function buscar($busca){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbl"=>"tb_localidade"),array("co_localidade","no_localidade"))
                    ->where("no_localidade ilike '%".$busca."%'");
        $all = $this->fetchAll($sql);
        
        $out = array();
        foreach ($all as $dados) {
            $out [] = array(
                "id" => $dados->co_localidade,
                "label" => $dados->no_localidade,
                "data" => array(
                    "cidade" => $dados->no_localidade,
                    "cidade_codigo" => $dados->co_localidade
                )
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("categoria" => "Nenhum item encontrado")
            );
        }
        return $out;
    }
    
   
    
    
    
}
