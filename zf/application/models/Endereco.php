<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Endereco extends Elotech_Db_Table_Abstract {

	protected $_name = 'tb_endereco';
	protected $_primary = 'co_seq_endereco';
	protected $_dependentTables = array();

        public function buscar($rua){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("tbe"=>"tb_endereco"),array("ds_logradouro"))
                        ->join(array("tbb"=>"tb_bairro"),"tbe.co_seq_bairro=tbb.co_bairro",array("co_bairro","no_bairro"))
                        ->join(array("tbl"=>"tb_localidade"),"tbb.co_localidade=tbl.co_localidade",array("co_localidade","no_localidade"))
                        ->join(array("tbuf"=>"tb_uf"),"tbl.co_uf=tbuf.co_uf",array("co_uf","no_uf"))
                        ->where("tbe.ds_logradouro ILIKE '%".$rua."%'");
            $all = $this->fetchAll($sql);
            
            $out = array();
            foreach ($all as $dados) {
                $out [] = array(
                    "id" => $dados->ds_logradouro,
                    "label" => $dados->ds_logradouro,
                    "data" => array(
                        "bairro" => $dados->no_bairro,
                        "bairro_codigo" => $dados->co_bairro,
                        "uf" => $dados->no_uf,
                        "uf_codigo" => $dados->co_uf,
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
