<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoDeExame extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipodeexame';// nome da tabela do banco
    protected $_primary = 'txa_codigo'; // pk da tabela

    public function salvarTipoDeExame($data){
        try { 
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao realizar a configuração! Erro: ".$exc->getMessage());
        }
    }
    
    public function listaTiposDeExames() {
        return $this->fetchAll();
    }
    
    public function listaDadosConfiguracoesDeExames($cteCodigo=FALSE){
        $sql =  $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txa"=>"tipodeexame"),array("txa_codigo","cte_codigo"))
                    ->join(array("proc"=>"procedimento"),"txa.proc_codigo=proc.proc_codigo",array("proc_nome","proc_codigo_sus"))
                    ->join(array("cte"=>"categoriadeexames"),"txa.cte_codigo=cte.cte_codigo",array("cte_cargo"))
                    ->joinLeft(array("tco"=>"tipo_categoria_ordem"),"txa.txa_codigo=tco.txa_codigo",array(""));
            if ($cteCodigo) {            
                $sql->where("txa.cte_codigo =?",$cteCodigo);
            }else{
                $sql->where("txa.cte_codigo =?",$this->getIDCategoriaDeExame()->cte_codigo);
            }  
        $sql->order(array("tco.tco_ordem ASC"));    
            //->limit(15);
        return $this->fetchAll($sql);
    }
    
    public function getIDCategoriaDeExame(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txa"=>"tipodeexame"),array("cte_codigo"))
                    ->join(array("cte"=>"categoriadeexames"),"txa.cte_codigo=cte.cte_codigo")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function buscaDadosConfiguracoesDeExames($tipoBusca=FALSE,$busca=FALSE){
        $sql =  $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txa"=>"tipodeexame"),array("txa_codigo","cte_codigo"))
                    ->join(array("proc"=>"procedimento"),"txa.proc_codigo=proc.proc_codigo",array("proc_nome","proc_codigo_sus"))
                    ->join(array("cte"=>"categoriadeexames"),"txa.cte_codigo=cte.cte_codigo",array("cte_cargo"))
                    ->joinLeft(array("tco"=>"tipo_categoria_ordem"),"txa.txa_codigo=tco.txa_codigo",array(""));
        if ($tipoBusca == "P")
            $sql->where("(proc.proc_codigo_sus = '$busca' OR proc.proc_nome ilike '%$busca%')");
        if ($tipoBusca == "C")
            $sql->where("cte.cte_cargo ilike '%$busca%'");
        if ($busca == "")
            $sql->where("txa.cte_codigo =?",$this->getIDCategoriaDeExame()->cte_codigo);
        $sql->order(array("tco.tco_ordem ASC"));
        return $this->fetchAll($sql);
    }
    
    public function  listaDadosEdicaoConfiguracoesDeExames($txaCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("txa"=>"tipodeexame"),array("txa_codigo","proc_codigo","cte_codigo","tma_codigo","tpm_codigo"))
                    ->join(array("proc"=>"procedimento"),"txa.proc_codigo=proc.proc_codigo",array("proc_nome"))
                    ->where("txa_codigo =?",$txaCodigo);
        return $this->fetchAll($sql);
    }
    
    public function excluirConfiguracoesDeExamesAction($txaCodigo=FALSE){
        $item = $this->fetchRow("txa_codigo = $txaCodigo"); 
        if ($item)
            $item->delete();
        return true;
    }
    
    public function atualizaOrdemConfiguracoesDeExamesAction() {
        
    }    

}
