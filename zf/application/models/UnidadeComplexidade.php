<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UnidadeComplexidade extends Elotech_Db_Table_Abstract {

    protected $_name = 'unidade_complexidade';
    protected $_primary = 'unc_codigo';
    

    public function salvar(array $dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: ".$ex->getMessage());
        }
        return true;
    }
    
    public function atualizaStatusGeral(){
        $where = $this->select()->getPart(Zend_Db_Table_Select::WHERE);
       // $where = $where[0];
        $data = array('unc_ativo'=> 'I');
        return $this->update($data, $where);
    }
    
    public function verificaSeJáExiste($co_complexidade=FALSE,$uni_codigo=FALSE){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("unc" => "unidade_complexidade"), array("qtd" => "count(*)", "unc_codigo"))
            ->where("co_complexidade = $co_complexidade")
            ->where("uni_codigo = $uni_codigo")
            ->group("unc_codigo");

        // die($where);
        return $this->fetchRow($where);
    }
}
