<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusVisitaDomiciliar extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_visita_domiciliar';
    protected $_primary = 'esv_codigo';

	public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("esv"=>"esus_visita_domiciliar"),array("esv_codigo","ate_codigo","esv_ine","esv_profissional_cns","esv_usu_cns","esv_usu_datanasc","esv_usu_sexo"))
                    ->where("uuid_ficha = ?",$uuid);
        return $this->fetchAll($sql);
    }

    public function getDadosPorId($id){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("esv"=>"esus_visita_domiciliar"),array("esv_codigo","ate_codigo","esv_ine","esv_profissional_cns","esv_usu_cns","esv_usu_datanasc","esv_usu_sexo"))
                    ->where("esv_codigo = ?",$id);
        return $this->fetchRow($sql);
    }

    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar dados: ".$exc->getMessage());
        }
    }
    
    
    public function excluir($ate_codigo=FALSE){
            $item = $this->fetchRow("ate_codigo=$ate_codigo");
            try{
                if ($item) { 
                    $item->delete(); 
                }
            } catch (Exception $ex) {
                die($ex->getMessage());
                $ex->getMessage();
            }
            return true;
        }
    
        
        public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function getQuantidadeFichaVisitaDomiciliar($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("esv" => "esus_visita_domiciliar"), array("count(*) total"))
        ->join(array("uni" => "unidade"),"esv.esv_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
        ->group(array("uni.uni_codigo"));;
        if($data_ini){
            $where->where("esv_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("esv_dtatendimento <= ?",$data_fim);
        }
        if($unidade){
            $where->where("uni_codigo = ?",$unidade);
        }
        return $this->fetchAll($where);
    }
}
