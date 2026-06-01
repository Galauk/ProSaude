<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Bairro extends Elotech_Db_Table_Abstract {

    protected $_name = 'bairro';
    protected $_primary = 'bai_codigo';
    protected $_sequence = 'bairro_bai_codigo_seq';

    public function salvar(array $data) {
        try{
            //echo "<pre>".print_r($data,1);die();
            return parent::salvar($data);
    
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    public function getBairros(){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("bai"=>"bairro"))
                      ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo",array("cid_nome"))
                      ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                      ->joinLeft(array("cid2"=>"cidade"),"cid2.cid_codigo=dis.cid_codigo",array("cid_distrito"=>"cid_nome"))
                      ->order("bai_codigo DESC")
                      ->limit(15);
        return $this->fetchAll($where);
    }
    
    public function getBairro($bai_codigo){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("bai"=>"bairro"))
                      ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo",array("cid_nome"))
                      ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                      ->joinLeft(array("cid2"=>"cidade"),"cid2.cid_codigo=dis.cid_codigo",array("cid_distrito"=>"cid_nome","cid_codigo_distrito"=>"cid_codigo"))
                      ->where("bai_codigo=$bai_codigo")
                      ->order("bai_codigo DESC");
        //die($where);
        return $this->fetchRow($where);
    }
    
    public function excluir($bai_codigo=FALSE) {
            $item = $this->fetchRow("bai_codigo=$bai_codigo");
            if ($item) {
                    $item->delete();
            }
    }
    
    public function verificaVinculosRua($bai_codigo = false){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from("rua","count(*) as qtde")
                      ->where("bai_codigo = $bai_codigo");
        return $this->fetchRow($where);
    }
    
    public function pesquisar($dados=NULL){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("bai"=>"bairro"))
                      ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo",array("cid_nome"))
                      ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                      ->joinLeft(array("cid2"=>"cidade"),"cid2.cid_codigo=dis.cid_codigo",array("cid_distrito"=>"cid_nome"))
                      ->order("bai_codigo DESC");
        
        if (is_string($dados)){
            $where->where("bai_nome ilike '%$dados%' or cid.cid_nome ilike '%$dados%' or cid2.cid_nome ilike '%$dados%' or dis_nome ilike '%$dados%' ");
        }

        //die($where);
        return $this->fetchAll($where);
    }
	
}
