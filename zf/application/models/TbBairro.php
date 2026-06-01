<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbBairro extends Elotech_Db_Table_Abstract {

    protected $_name = 'bairro';
    protected $_primary = 'bai_codigo';
    
    public function buscar($busca=false,$rua_codigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("bai"=>"bairro"),array("bai_codigo","bai_nome"))
                    ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                    ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                    ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                    ->where("bai_nome ilike '%".$busca."%'");

        $all = $this->fetchAll($sql);
        $out = array();
        
        
        foreach ($all as $dados) {
            $out [] = array(
                "id" => $dados->bai_codigo,
                "label" => $dados->bai_nome,
                "data" => array(
                    "bai_nome" => $dados->bai_nome,
                    "bai_codigo" => $dados->bai_codigo,
                    "cid_nome" => ($dados->cid_nome ? ($dados->cid_nome) : ($dados->cid_nome_dis == "" || $dados->cid_nome_dis == null ? "Nenhum vinculo de cidade" : $dados->cid_nome_dis)),
                    "dis_nome" => ($dados->dis_nome ? $dados->dis_nome : "Não possui")
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
    
    public function getDadosBairro($busca){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("bai"=>"bairro"),array("bai_codigo","bai_nome"))
                    ->where("bai_codigo = '".$busca."'");
        return $this->fetchRow($sql);
    }
    
    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception
                ("Falha ao salvar bairro: ".$exc->getMessage());
        }
    }
}
