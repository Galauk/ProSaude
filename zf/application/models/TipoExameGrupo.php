<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoExameGrupo extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipoexame_grupo';// nome da tabela do banco
    protected $_primary = 'txg_codigo'; // pk da tabela

    public function salvarConfigGrupoDeExames($data=FALSE){
        try { 
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao realizar a configuração! Erro: ".$exc->getMessage());
        }
    }
    
    public function getOrdemConfigGrupoDeExames($gruexCodigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txg"=>"tipoexame_grupo"),array("tcg_ordem"))
                    ->join(array("gruex"=>"grupoexame"),"txg.gruex_codigo=gruex.gruex_codigo",array(""))
                    ->where("txg.gruex_codigo =?",$gruexCodigo)
                    ->order("txg.tcg_ordem DESC")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function listaDadosConfigGrupoDeExames($gruexCodigo=FALSE){
        $sql =  $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txg"=>"tipoexame_grupo"),array("txg_codigo"))
                    ->join(array("gruex"=>"grupoexame"),"txg.gruex_codigo=gruex.gruex_codigo",array("gruex_descricao","gruex_codigo"))
                    ->join(array("proc"=>"procedimento"),"txg.proc_codigo=proc.proc_codigo",array("proc_nome","proc_codigo_sus"));
            if ($gruexCodigo) {
                $sql->where("txg.gruex_codigo =?",$gruexCodigo);
            }else{
                if ($this->getIDGrupoDeExame()->gruex_codigo) 
                    $sql->where("txg.gruex_codigo =?",$this->getIDGrupoDeExame()->gruex_codigo);
            }
            $sql->order(array("txg.tcg_ordem ASC"))    
                ->limit(15);
              //  die($sql);
        return $this->fetchAll($sql);
    }
    
    public function getIDGrupoDeExame(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txg"=>"tipoexame_grupo"),array("gruex_codigo"))
                    ->join(array("gruex"=>"grupoexame"),"txg.gruex_codigo=gruex.gruex_codigo")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function atualizaOrdemConfigGrupoDeExames($ordemCont=FALSE,$item=FALSE){
        $where['txg_codigo = ?'] = $item;
        $dados = array("tcg_ordem"=>$ordemCont);
        return $this->update($dados, $where);
    }
    
    public function excluirConfigGrupoDeExamesAction($txgCodigo=FALSE){
        $item = $this->fetchRow("txg_codigo = $txgCodigo"); 
        if ($item)
            $item->delete();
        return true;
    }
    
    public function  listaDadosEdicaoConfigGrupoDeExames($txgCodigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txg"=>"tipoexame_grupo"),array("txg_codigo","proc_codigo","gruex_codigo"))
                    ->join(array("proc"=>"procedimento"),"txg.proc_codigo=proc.proc_codigo",array("proc_nome"))
                    ->where("txg_codigo =?",$txgCodigo);
        return $this->fetchAll($sql);
    }
    
    public function buscaDadosConfigGrupoDeExames($tipoBusca=FALSE,$busca=FALSE){
        $sql =  $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txg"=>"tipoexame_grupo"),array("txg_codigo","tcg_ordem"))
                    ->join(array("proc"=>"procedimento"),"txg.proc_codigo=proc.proc_codigo",array("proc_nome","proc_codigo_sus"))
                    ->join(array("gruex"=>"grupoexame"),"txg.gruex_codigo=gruex.gruex_codigo",array("gruex_descricao","gruex_codigo"));
        if ($tipoBusca == "P")
            $sql->where("(proc.proc_codigo_sus = '$busca' OR proc.proc_nome ilike '%$busca%')");
        if ($tipoBusca == "G")
            $sql->where("gruex.gruex_descricao ilike '%$busca%'");
        if ($busca == "")
            $sql->where("txg.gruex_codigo =?",$this->getIDGrupoDeExame()->gruex_codigo);
        $sql->order(array("txg.tcg_ordem ASC"));
        //die($sql);
        return $this->fetchAll($sql);
    }
    
    
    /*public function listaTiposDeExames() {
        return $this->fetchAll();
    }
    
    public function getIDCategoriaDeExame(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txa"=>"tipodeexame"),array("cte_codigo"))
                    ->join(array("cte"=>"categoriadeexames"),"txa.cte_codigo=cte.cte_codigo")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    */    

}
