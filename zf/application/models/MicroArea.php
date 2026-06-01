<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_MicroArea extends Elotech_Db_Table_Abstract {

    protected $_name = 'microarea';
    protected $_primary = 'mic_codigo';
    protected $_sequence = 'seq_mic_codigo';

    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function getMicroAreas(){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("mic"=>"microarea"))
            ->join("area", "area.area_codigo=mic.area_codigo","area_desc")
            ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=mic.mic_responsavel","usr_nome")
            ->order("mic_codigo DESC");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getMicroArea($mic_codigo){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("mic"=>"microarea"))
            ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=mic.mic_responsavel",array("usr_nome","usr_codigo"))
            ->join("area","area.area_codigo=mic.area_codigo")
            ->where("mic_codigo=$mic_codigo");
        //die($where);
        return $this->fetchRow($where);
    }
    
    public function pesquisar($dados=NULL){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("mic"=>"microarea"))
            ->join("area","area.area_codigo=mic.area_codigo")
            ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=mic.mic_responsavel","usr_nome");
        
        if (is_string($dados)){
            $where->where("area_desc ilike '%$dados%' or usr_nome ilike '%$dados%' or mic_descricao ilike '%$dados%' ");
        } else if (is_int($dados)){
            $where->where("");
        }

        if ($limit){
            $where->limit(15);
        }
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function excluir($area_codigo=FALSE) {
        $item = $this->fetchRow("area_codigo=$area_codigo");
        if ($item) {
            $item->delete();
        }
    }

    public function getMicroAreasAtivas($co_equipe = NULL){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("mic"=>"microarea"))
            ->join(array("teq"=>"tb_equipe"),"mic.co_seq_equipe = teq.co_seq_equipe")
            ->order("mic.mic_descricao asc");
        if ($co_equipe){
            $where->where("teq.nu_ine = '$co_equipe' and mic.ativo = 't'");
        }else{
            $where->where("mic.ativo = 't'");
        }
        // die($where);
        return $this->fetchAll($where);
    }

    public function getMicroAreasAtivasPorNuIne($nu_ine = NULL){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("mic"=>"microarea"), array("mic_codigo", "mic_descricao"))
            ->join(array("teq"=>"tb_equipe"),"mic.co_seq_equipe=teq.co_seq_equipe", array())
            ->join(array("usr"=>"usuarios"),"mic.mic_responsavel=usr.usr_codigo", "usr_nome")
            ->where("mic.ativo = 'true'");
        
        if($nu_ine){
            $where->where("teq.nu_ine = '$nu_ine'");
        }
        $where->order("mic.mic_descricao asc");
        // die($where);
        return $this->fetchAll($where);
    }

    public function getMicroAreasAtivasPorUnidade($uni_codigo = NULL){
        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("teq"=>"tb_equipe"))
            ->join(array("mic"=>"microarea"),"mic.co_seq_equipe=teq.co_seq_equipe")
            ->where("teq.uni_codigo = $uni_codigo and mic.ativo = true")
            ->order("mic.mic_descricao");
        //die($where);
        return $this->fetchAll($where);
    }

    public function verificaSeJaExiste($co_seq_equipe, $mic_descricao){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("mic"=>"microarea"), array("mic_codigo"))
                      ->where("co_seq_equipe=$co_seq_equipe and mic_descricao = '$mic_descricao' and ativo = true");
        return $this->fetchAll($where)->toArray();
    }
    
    public function inativarMicroArea($mic_codigo){
        $data = array("ativo" => "false");
        $where = $this->select()->where("mic_codigo = $mic_codigo")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];

        // Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
        return $this->update($data, $where);
    }
    
    public function buscarResponsavel($mic_codigo){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("mic"=>"microarea"), array("mic_responsavel"))
                      ->where("mic_codigo=$mic_codigo");

        return $this->fetchRow($where)->toArray();
    }
}
