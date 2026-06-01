<?php

class Application_Model_GrupoDeDoencas extends Elotech_Db_Table_Abstract {
    protected $_name = 'grupo_doencas';
    protected $_primary = 'gd_codigo';
    
    // Lista os grupo de doenças com algum cid cadastrado
    public function listaGrupoDeDoencasCid(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("gd"=>"grupo_doencas"),array("gd_codigo","gd_descricao"))
                    ->join(array("gc"=>"grupos_cid"),"gd.gd_codigo=gc.gd_codigo","")
                    ->join(array("cd10"=>"cid10"),"gc.cd10_codigo=cd10.cd10_codigo","")
                    ->order("gd_descricao ASC");
        return $this->fetchAll($sql);
    }
}


?>
