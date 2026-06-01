<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_CategoriaConveniosProcedimentos extends Elotech_Db_Table_Abstract {

    protected $_name = 'categoria_convenios_procedimentos';
    protected $_primary = 'catcp_codigo';
    
    public function salvar(array $data) {
	try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Erro ao vincular Categoria com Procedimento: ".$exc->getMessage());
        }
    }
    
    public function listaDados(){
        $sql = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("catcp"=>"categoria_convenios_procedimentos"),array("catcp_codigo"))
                      ->join(array("catc"=>"categoria_convenios"),"catcp.catc_codigo=catc.catc_codigo",array("catc_nome","catc_codigo"))
                      ->join(array("proc"=>"procedimento"),"catcp.proc_codigo=proc.proc_codigo",array("proc_codigo_sus","proc_nome"));
        /*if($catc_codigo) {
            $sql->where("catc.catc_codigo =?",$catc_codigo);
        } else {
            if ($this->getIDCategoriaDeConvenio()->catc_codigo)
                $sql->where("catc.catc_codigo =?",$this->getIDCategoriaDeConvenio()->catc_codigo);
        }*/ 
        $sql->order(array("catcp.catcp_codigo DESC"))
            ->limit(15);
        return $this->fetchAll($sql);
    }
    
    public function listaDadosEdicao($catcp_codigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("catcp"=>"categoria_convenios_procedimentos"),array("catcp_codigo"))
                    ->join(array("catc"=>"categoria_convenios"),"catcp.catc_codigo=catc.catc_codigo",array("catc_nome","catc_codigo"))
                    ->join(array("proc"=>"procedimento"),"catcp.proc_codigo=proc.proc_codigo",array("proc_codigo_sus","proc_nome","proc_codigo"))
                    ->where("catcp_codigo=?",$catcp_codigo);
        return $this->fetchRow($sql);
    }
    
    public function excluir($catcp_codigo=FALSE){
        $item = $this->fetchRow("catcp_codigo = $catcp_codigo"); 
        if ($item)
            $item->delete();
        return true;
    }
    
    public function getIDCategoriaDeConvenio(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("catc"=>"categoria_convenios"),array("catc_codigo"))
                    ->join(array("catcp"=>"categoria_convenios_procedimentos"),"catc.catc_codigo=catcp.catc_codigo")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function buscaDadosConfigGrupoDeExames($tipoBusca=FALSE,$busca=FALSE){
       $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("catcp"=>"categoria_convenios_procedimentos"),array("catcp_codigo"))
                    ->join(array("catc"=>"categoria_convenios"),"catcp.catc_codigo=catc.catc_codigo",array("catc_nome","catc_codigo"))
                    ->join(array("proc"=>"procedimento"),"catcp.proc_codigo=proc.proc_codigo",array("proc_codigo_sus","proc_nome","proc_codigo"));
        if ($tipoBusca == "P")
            $sql->where("(proc.proc_codigo_sus = '$busca' OR proc.proc_nome ilike '%$busca%')");
        if ($tipoBusca == "C")
            $sql->where("catc.catc_nome ilike '%$busca%'");
        if ($busca == "")
           if ($this->getIDCategoriaDeConvenio()->catc_codigo)
                $sql->where("catc.catc_codigo =?",$this->getIDCategoriaDeConvenio()->catc_codigo);
        $sql->order(array("catc.catc_codigo DESC"));
        //die($sql);
        return $this->fetchAll($sql);
    }
    
}
