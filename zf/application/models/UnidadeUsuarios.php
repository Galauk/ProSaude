<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UnidadeUsuarios extends Elotech_Db_Table_Abstract {

    protected $_name = 'unidade_usuarios';
    protected $_primary = 'unu_codigo';

    public function salvar(array $data) {
       
    try {
            $teste = $this->getCodigoPorUsuariosComUnidades($data);
            if(count($teste) > 0){
                return true;
            }else{
                return parent::salvar($data);
            }
            
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar vínculo de unidade: ".$exc->getMessage());
        }
    }
	
    public function getUnidadeUsuarios($usr_codigo=FALSE,$equipe_false=FALSE){
        if(!$usr_codigo){
            return false;
        }

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("unu"=>"unidade_usuarios"))
                ->join(array("uni"=>"unidade"),"uni.uni_codigo=unu.uni_codigo",array("uni_desc","uni_cnes","uni_codigo"))
                ->distinct()
                ->where("usr_codigo=$usr_codigo");
        
        if($equipe_false != 1){
            $where->join(array("te"=>"tb_equipe"),"te.uni_codigo=uni.uni_codigo","");
        }
        
        // die($where);

        return $this->fetchAll($where);
    }
    
    public function excluir($unu_codigo=FALSE) {
        $item = $this->fetchRow("unu_codigo=$unu_codigo");
        if ($item) {
            $item->delete();
        }
    }
    
    public function excluirTodos($usr_codigo){
        $this->delete("usr_codigo=$usr_codigo");
    }
    
    // public function getCodigoPorUsuariosComUnidades($dadosUniUsu=FALSE){
    //     $where = $this->select(FALSE)
    //             ->setIntegrityCheck(FALSE)
    //             ->from(array("unu"=>"unidade_usuarios"),array("unu_codigo"))
    //             ->join(array("uni"=>"unidade"),"uni.uni_codigo=unu.uni_codigo",array(""))
    //             ->where("unu.usr_codigo=".$dadosUniUsu['usr_codigo']."")
    //             ->where("unu.uni_codigo=".$dadosUniUsu['uni_codigo']->uni_codigo."");
    //     // die($where);
    //     return $this->fetchRow($where);
    // }
    
    public function getCodigoPorUsuariosComUnidades($dadosUniUsu=FALSE){
        $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("unu"=>"unidade_usuarios"),array("unu_codigo"))
                    ->join(array("uni"=>"unidade"),"uni.uni_codigo=unu.uni_codigo",array(""))
                    ->where("unu.usr_codigo=$dadosUniUsu[usr_codigo]")
                    ->where("unu.uni_codigo=$dadosUniUsu[uni_codigo]");
        //..echo $where."<br/>";die();
        return $this->fetchRow($where);
    }
    
    public function excluirTodosCnes(){
         try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("DELETE FROM unidade_usuarios WHERE usr_codigo IN
                (SELECT DISTINCT usr_codigo FROM usuarios WHERE usr_mestre IS NULL OR usr_mestre <> 'S')")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir item: ".$ex->getMessage());
        }
    }
    
    public function getUnidadesProfissional($usr_codigo=FALSE){
        if(!$usr_codigo){
           $tbUsr = new Application_Model_Usuarios();
           $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        }
           
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("unu"=>"unidade_usuarios"),"")
                ->join(array("uni"=>"unidade"),"uni.uni_codigo=unu.uni_codigo",array("uni_desc","uni_cnes","uni_codigo"))
                ->joinLeft(array("l" => "logon"),"l.id_login=$usr_codigo",array("uni_login"=>"uni_codigo"))
                ->distinct()
                ->order("uni.uni_desc")
                ->where("usr_codigo=$usr_codigo")
                ->where("cnes_ativo = 'A'");

        return $this->fetchAll($where);
    }

    public function getDados() {
        $dados = $this->fetchAll();

        return $dados;
    }

    public function getProfissionaisUnidade($cnes = false){
        if(!$cnes){
            return false;
        }

        $dados = $this->getDefaultAdapter()->query("
        select usu.usr_codigo, usu.usr_nome, uni.uni_codigo, uni.uni_desc from unidade_usuarios as uni_usu
        inner join usuarios as usu on usu.usr_codigo = uni_usu.usr_codigo
        inner join unidade as uni on uni.uni_codigo = uni_usu.uni_codigo
        where uni.uni_cnes = $cnes and usr_ativo = 'S' order by usr_nome")->fetchAll();

        return $dados;

    }

    public function getUsuariosUnidade($uniCod, $usr){
        if(!$uniCod){
            return false;
        }

        $dados = $this->getDefaultAdapter()->query("
        select usu.usr_codigo, usu.usr_nome, uni.uni_codigo, uni.uni_desc from unidade_usuarios as uni_usu
        inner join usuarios as usu on usu.usr_codigo = uni_usu.usr_codigo
        inner join unidade as uni on uni.uni_codigo = uni_usu.uni_codigo
        where uni.uni_codigo = $uniCod and usr_ativo = 'S' and usu.usr_codigo = $usr order by usr_nome")->fetchAll();


        return $dados;
    }
}