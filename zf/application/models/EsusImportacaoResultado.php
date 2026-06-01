<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusImportacaoResultado extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_importacao_resultado';
    protected $_primary = 'eir_codigo';

    
    public function salvarTeste($data){
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao importar resultado: ".$exc->getMessage());
        }
    }
    
    public function salvar($data){
        //echo "<pre>" . print_r($data, 1);
        //die();
        //Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            foreach($data as $valor){
                $mensagem = explode(":", $valor[mensagem]);
                if (count($this->verificaEsus($valor[nome],$mensagem[1])->toArray())==0) {
                    $array_salvar = array();
                    $array_salvar = array("eir_status" => ($valor[cidadaoId] != "null" ? "t" : "f"),
                                          "eir_cod_cidadao_esus" => ($valor[cidadaoId] == "null" ? "" : $valor[cidadaoId]),
                                          "eir_cns" => $valor[cns],
                                          "eir_cpf" => $valor[cpf],
                                          "eir_nome" => $valor[nome],
                                          //"eir_data_nascimento" => $valor[dtNascimento],
                                          "eir_nome_mae" => $valor[nomeMae],
                                          "eir_mensagem" => $mensagem[1]);
                    echo "<pre>" . print_r($array_salvar, 1);
                    parent::salvar($array_salvar);
                    
                }
            }
            //Zend_Db_Table::getDefaultAdapter()->commit();
            return true;
        } catch (Exception $exc) {
            
            //die();
            die($exc->getMessage());
            //Zend_Db_Table::getDefaultAdapter()->rollBack();
            return $exc->getMessage();
        }
        //Zend_Db_Table::getDefaultAdapter()->commit();
        return true;
    }  
    
    public function verificaEsus($eir_nome=FALSE,$eir_msg=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eir"=>"esus_importacao_resultado"))
                    ->where("eir_nome =?",$eir_nome);
        if ($eir_msg)
           $sql->where("eir_mensagem =?",$eir_msg);
        
        return $this->fetchAll($sql);
    }    
    
    public function listar($status=FALSE) {
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eir"=>"esus_importacao_resultado"))
                    ->where("eir_status_correcao IS NULL");
        if ($status) {
            $sql->where("eir_status =?",$status);
        }
        
        $sql->order("eir_codigo DESC");    
        //die($sql);    
        return $this->fetchAll($sql); 
    }
    
    public function getDadosResultadoCodigo($eir_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eir"=>"esus_importacao_resultado"))
                    ->where("eir_codigo =?",$eir_codigo)
                    ->order("eir_codigo DESC");
        return $this->fetchRow($sql);
    }
    
    public function atualizaStatusImp($dados,$eir_codigo){
        $where['eir_codigo =?'] = $eir_codigo;
        return $this->update($dados, $where);
    }
}
