<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Area extends Elotech_Db_Table_Abstract {

    protected $_name = 'area';
    protected $_primary = 'area_codigo';

    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
    public function selectTag($value=NULL, $first=NULL) {
            $where = $this->select()->order("area_desc");		
            return parent::selectTag($where, "area_desc", NULL, $first, TRUE, NULL, NULL, NULL, $value);
    }
    
    public function getAreas(){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from("area")
                      ->order("area_codigo DESC");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getArea($area_codigo){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from("area")
                      ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=area.area_responsavel",array("usr_nome","usr_codigo"))
                      ->where("area_codigo=$area_codigo")
                      ->order("area_codigo DESC");
        //die($where);
        return $this->fetchRow($where);
    }
    
    
    
    public function pesquisar($dados=NULL){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from("area")
                      ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=area.area_responsavel","usr_nome");
        
        if (is_string($dados)){
            $where->where("area_desc ilike '%$dados%' or usr_nome ilike '%$dados%' or area_obs ilike '%$dados%' ");
        }else if (is_int($dados)){
            $where->where("");
        }

        if ($limit) {
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
}
