<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UsuariosEquipe extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuarios_equipe';
    protected $_primary = "ueq_codigo";
    

    public function salvar($dados) {
       //echo "<pre>".print_r($dados,1);
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Usuario equipe: ".$ex->getMessage());
        }
        return true;
    }
    
     public function excluirTodosCnes(){
         //die("?");
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("DELETE FROM usuarios_equipe WHERE usr_codigo IN (
                                            SELECT DISTINCT
                                                usr_codigo
                                            FROM
                                                usuarios
                                            WHERE
                                                 usr_mestre is null or usr_mestre = 'N')")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir item: ".$ex->getMessage());
        }
    }
    
}
