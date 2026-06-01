<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_HorusCadastro extends Elotech_Db_Table_Abstract {
    
    protected $_name = "horus_cadastro";
    protected $_primary = "hor_cad_codigo";
    
    public function listaUsuariosHorus(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_cad" => "horus_cadastro"),array("hor_cad_codigo","hor_cad_login","hor_cad_senha","hor_cad_dtcadastro","hor_cad_ambiente","hor_cad_ativo"))
                    ->order("hor_cad_ativo DESC");
        return $this->fetchAll($sql);
    }
    
    public function getDadosUsuarioHorus($codCad){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_cad" => "horus_cadastro"),array("hor_cad_codigo","hor_cad_login","hor_cad_senha","hor_cad_dtcadastro","hor_cad_ambiente","hor_cad_ativo"));
            if($codCad){
                $sql->where("hor_cad_codigo =?",$codCad);
            }
        return $this->fetchRow($sql);
    }
    
    public function getDadosUsuarioAtivoHorus(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_cad" => "horus_cadastro"),array("hor_cad_codigo","hor_cad_login","hor_cad_senha","hor_cad_dtcadastro","hor_cad_ambiente","hor_cad_ativo"))
                    ->where("hor_cad_ativo = 'T'");
        return $this->fetchRow($sql);
    }
    
    public function getQtdUsuariosHorusAtivos(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_cad"=>"horus_cadastro"),array("count(hor_cad_codigo) AS qtd_usuativos"))
                    ->where("hor_cad_ativo = 'T'");
        return $this->fetchRow($sql);
    }
    
    public function excluiUsuarioHorus($codUsu){
        $item = $this->fetchRow("hor_cad_codigo=$codUsu");
        if ($item) {
            $item->delete();
            return true;
        }
    }
    
    public function salvar($data) {
        parent::salvar($data);
    }
    
}

?>
