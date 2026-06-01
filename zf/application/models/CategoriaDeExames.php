<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_CategoriaDeExames extends Elotech_Db_Table_Abstract {

    protected $_name = 'categoriadeexames';// nome da tabela do banco
    protected $_primary = 'cte_codigo'; // pk da tabela

    public function listaCategoriaDeExames(){
        return $this->fetchAll();
    }

    public function getCategoriaPorExames($proc_codigos=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("cat"=>"categoriadeexames"))
                      ->join(array("txa"=>"tipodeexame"),"txa.cte_codigo=cat.cte_codigo")
                      ->where("proc_codigo in ($proc_codigos)");

        return $this->fetchAll($where);
    }

    public function getCategoriaPorProcedimentos($proc_codigos){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("cat"=>"categoriadeexames"),array("cte_codigo", "cte_cargo"))
                      ->join(array("txa"=>"tipodeexame"),"txa.cte_codigo=cat.cte_codigo","")
                      ->where("proc_codigo in ($proc_codigos)");
        return $this->fetchAll($where);

    }

   public function getProcedimentosPorCategoria($proc_codigos){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("cat"=>"categoriadeexames"),array("cte_codigo", "cte_cargo"))
                      ->join(array("txa"=>"tipodeexame"),"txa.cte_codigo=cat.cte_codigo","")
                      ->join(array("proc"=>"procedimento"),"proc.proc_codigo=txa.proc_codigo",array("proc_codigo","proc_nome"))
                      ->joinLeft(array("tco"=>"tipo_categoria_ordem"),"txa.txa_codigo=tco.txa_codigo","")
                      ->where("txa.proc_codigo in ($proc_codigos)")
                      ->order("tco.tco_ordem ASC");
        return $this->fetchAll($where);
    }

    public function getMapaDeTrabalho($cte_codigo=FALSE,$age_codigo=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("c"=>"coleta"),"col_data_coleta")
                      ->join(array("ai"=>"agenda_itens"),"ai.agei_codigo=c.agei_codigo",array("agei_codigo"))
                      ->join(array("ci"=>"convenio_itens"),"ci.coni_codigo=ai.coni_codigo","")
                      ->join(array("a"=>"agenda"),"a.age_codigo=ai.age_codigo","")
                      ->join(array("p"=>"procedimento"),"p.proc_codigo=ci.proc_codigo",array("proc_nome","proc_codigo"))
                      ->join(array("txa"=>"tipodeexame"),"txa.proc_codigo=p.proc_codigo","txa_codigo")
                      ->where("a.age_codigo=$age_codigo")
                      ->order("proc_nome");
        if($cte_codigo)
            $where->where("txa.cte_codigo=$cte_codigo");

        return $this->fetchAll($where);

    }

    public function getCaregorias($age_codigo=FALSE,$cte_codigo=FALSE){
       $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("c"=>"coleta"),"col_data_coleta")
                      ->join(array("ai"=>"agenda_itens"),"ai.agei_codigo=c.agei_codigo","")
                      ->join(array("a"=>"agenda"),"a.age_codigo=ai.age_codigo","")
                      ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=a.usr_codigo_medico","usr_nome")
                      ->joinLeft(array("med"=>"medico"),"med.med_codigo=a.med_codigo","med_nome")
                      ->join(array("u"=>"usuario"),"u.usu_codigo=a.usu_codigo",array("usu_nome","usu_codigo","usu_datanasc","usu_sexo"))
                      ->join(array("ci"=>"convenio_itens"),"ci.coni_codigo=ai.coni_codigo","")
                      ->join(array("p"=>"procedimento"),"p.proc_codigo=ci.proc_codigo","")
                      ->join(array("txa"=>"tipodeexame"),"txa.proc_codigo=p.proc_codigo","")
                      ->join(array("cat"=>"categoriadeexames"),"cat.cte_codigo=txa.cte_codigo",array("cte_cargo","cte_codigo"))
                      ->where("a.age_codigo=$age_codigo")
                      ->order("cte_cargo");

       if($cte_codigo)
            $where->where("cat.cte_codigo=$cte_codigo");
          //die($where);
        return $this->fetchAll($where);
    }

}
