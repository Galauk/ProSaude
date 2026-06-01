<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbAdministracaoProduto extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_administracao_produto';
    protected $_primary = 'adm_codigo';

    
    public function getTodasAdministracoes() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbAdmProd" => "tb_administracao_produto"),array("adm_codigo","adm_nome","adm_sigla","adm_tipo"))
                ->order(array("adm_codigo"));
        return $this->fetchAll($sql);
    }
    
    public function getAdministracao($adm_codgo = false){
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbAdmProd" => "tb_administracao_produto"),array("adm_sigla","adm_nome"))
                ->where("adm_codigo = $adm_codgo");
        return $this->fetchRow($sql);
    }
    
}