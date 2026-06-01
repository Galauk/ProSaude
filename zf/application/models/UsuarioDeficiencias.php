<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_UsuarioDeficiencias extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuario_deficiencias';
    protected $_primary = 'usud_codigo';
    protected $_sequence = 'seq_usud_codigo';
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            die($ex->getMessage());
            throw new Zend_Validate_Exception("Falha ao salvar Usuário: ".$ex->getMessage());
        }
    }
    
    public function excluirPorUsuario($usuCod){
        $item = $this->fetchAll("usu_codigo=$usuCod");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }
    
    public function getDadosPorUsuario($usuCod=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usud"=>"usuario_deficiencias"),array("co_pergunta_detalhe"))
                    ->where("usu_codigo =?",$usuCod);
        return $this->fetchAll($sql);
    }

}
