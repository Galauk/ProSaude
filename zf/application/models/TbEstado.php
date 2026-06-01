<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbEstado extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_uf';
    protected $_primary = 'co_uf';
    
    public function buscar($estado){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbuf"=>"tb_uf"),array("co_uf","no_uf"))
                    ->where("no_uf ilike '%".$estado."%'");
        $all = $this->fetchAll($sql);
        
        $out = array();
        foreach ($all as $dados) {
            $out [] = array(
                "id" => $dados->co_uf,
                "label" => $dados->no_uf,
                "data" => array(
                    "uf" => $dados->no_uf,
                    "uf_codigo" => $dados->co_uf
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
    
    public function getDadosEstado($busca){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbuf"=>"tb_uf"),array("co_uf","no_uf"))
                    ->where("no_uf = '".$busca."'")
                    ->orWhere("no_uf = '".strtoupper(trim($busca))."'")
                    ->orWhere("no_uf = '".strtolower(trim($busca))."'")
                    ->orWhere("sg_uf = '".strtoupper(trim($busca))."'")
                    ->orWhere("sg_uf = '".strtolower(trim($busca))."'");
        return $this->fetchRow($sql);
    }
    
}
