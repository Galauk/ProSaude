<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusCadastroIndividual extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_cadastro_individual';
    protected $_primary = 'eci_codigo';

    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eci"=>"esus_cadastro_individual"),array("eci_codigo"))
                    ->join(array("usu"=>"usuario"),"eci.usu_codigo=usu.usu_codigo",array("usu_codigo","usu_nome","usu_datanasc","usu_mae"))
                    ->where("uuid_ficha = ?",$uuid);
        return $this->fetchAll($sql);
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
            
            return $this->update($data, $where);
        
    }

    public function getDadosPorUsuario($usu_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eci"=>"esus_cadastro_individual"),array("eci_codigo"))
                    ->join(array("usu"=>"usuario"),"eci.usu_codigo=usu.usu_codigo",array("usu_codigo","usu_nome","usu_datanasc","usu_mae"))
                    ->where("usu.usu_codigo = ?",$usu_codigo);
                    
        return $this->fetchAll($sql);
    }
    
    public function getQuantidadeFichaExpCadIndividual($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("eci" => "esus_cadastro_individual"), array("count(*) total"))
        ->join(array("uni" => "unidade"),"eci_usr_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
        ->group(array("uni.uni_codigo"));
        if($data_ini){
            $where->where("eci_usr_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eci_usr_dtatendimento <= ?",$data_fim);
        }
        if($unidade){
            $where->where("uni_codigo = ?",$unidade);
        }
        return $this->fetchAll($where);
    }
    
     public function getFichaPorData($data_ini=false,$data_fim=false){
        $where = $this->select(FALSE)->setIntegrityCheck(FALSE)
        ->from(array("eci" => "esus_cadastro_individual"))
        ->join(array("usu" => "usuario"),"usu.usu_codigo = eci.usu_codigo", array("extract(year from age(usu.usu_datanasc)) AS idade","usu_nome","TO_CHAR(usu_datanasc,'dd/mm/yyyy') as usu_datanasc","cd_nacionalidade","usu_mae","rac_codigo","usu_sexo"));
        
        if($data_ini){
            $where->where("eci_usr_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eci_usr_dtatendimento <= ?",$data_fim);
        }
//        die($where);
        return $this->fetchAll($where);
    }
    
}
