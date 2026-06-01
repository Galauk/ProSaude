<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UsuarioAcompanhante extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuario_acompanhante';
    protected $_primary = 'acom_codigo';   

    /**
     * Persiste um item (insert ou update)
     * @param array $data array de chave=>valor, cada chave corresponde a um atributo
     * @return int primary key do item (nextVal para insert) 
     */
    public function salvar(array $data) {      
       $this->notEmpty(array("usu_codigo","viausu_codigo"), $data);           
        return parent::salvar($data);
    }
   
/**
     * Retorna um acompanhantes para saber se é insert ou update
     * @param int $viausu_codigo int $usu_codigo
     * @return Zend_Db_Table_Row_Abstract 
     */
   public function getAcompanhanteUpdate($usu_codigo,$viausu_codigo) {
      $where = $this->select(FALSE)
              ->setIntegrityCheck(FALSE)
              ->from(array("acom"=>"usuario_acompanhante"),array(""))
              ->join(array("usu"=>"usuario"),"usu.usu_codigo = acom.usu_codigo" ,array("usu.usu_codigo as usu_codigo_","usu.usu_nome as usu_nome_"))
              ->join(array("vu"=>"viagem_usuario"), "vu.viausu_codigo = acom.viausu_codigo","")
              ->where("vu.viausu_codigo=?",$viausu_codigo);
           //die($where);
      return $this->fetchAll($where);
                
               
   }
    public function getAcompanhantes($viausu_codigo) {
      $where = $this->select(FALSE)
              ->setIntegrityCheck(FALSE)
              ->from(array("acom"=>"usuario_acompanhante"),array("acom_codigo"))
              ->join(array("usu"=>"usuario"),"usu.usu_codigo = acom.usu_codigo" ,array("usu.usu_codigo as usu_codigo_","usu.usu_nome as usu_nome_"))
              ->join(array("vu"=>"viagem_usuario"), "vu.viausu_codigo = acom.viausu_codigo","")
              ->where("vu.viausu_codigo=?",$viausu_codigo);
           //die($where);
      return $this->fetchAll($where);
                
               
   }      
    /**
     * Exclui um Acompanhante	
     * @param int $vei_codigo Código da veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function excluir($viausu_codigo=FALSE) {
        $item = $this->fetchAll("viausu_codigo=$viausu_codigo");
          
        if ($item) {
        foreach ($item as $i)
            $i->delete();
        }
    }
    
  
}

