<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_GrupoExame extends Elotech_Db_Table_Abstract {

    protected $_name = 'grupoexame';
    protected $_primary = 'gruex_codigo';
    //protected $_sequence = 'seq_dom_codigo';
    //protected $_dependentTables = array();

    public function salvar(array $data) {
	try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Erro ao cadastrar grupo: ", $exc->getMessage());
        }
    }
    
    public function getGrupos($prenatal = FALSE){
      $where = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("gruex" => "grupoexame"))
            ->order("gruex_descricao");
        if($prenatal) {
          $where->where("pre_natal = 'S'");
        }
        return $this->fetchAll($where);
    }
    
    public function getProcedimentosPorGrupo($gruex_codigo=FALSE){
        if(empty($gruex_codigo))
            return false;
        
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("gruex"=>"grupoexame"),"")
                      ->join(array("txg"=>"tipoexame_grupo"),"txg.gruex_codigo=gruex.gruex_codigo","")
                      ->join(array("proc"=>"procedimento"),"proc.proc_codigo=txg.proc_codigo","proc_codigo");
        return $this->fetchAll($where);
    }
    
    public function getGruposPorNome($grupoDescricao=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("gruex"=>"grupoexame"),array("gruex_codigo","gruex_descricao"))
                    ->where("gruex_descricao =?",$grupoDescricao);
        return $this->fetchRow($sql);
    }


    public function getGruposPorId($gruex_codigo = FALSE)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("gruex" => "grupoexame"), array("gruex_codigo", "gruex_descricao", "pre_natal"))
            ->where("gruex_codigo = $gruex_codigo");
        return $this->fetchRow($sql);
    }
    public function excluir($id)
    {
        $registro = $this->fetchRow("gruex_codigo = $id");
        if ($registro) {
            $registro->delete();
            return true;
        }
    }


}
