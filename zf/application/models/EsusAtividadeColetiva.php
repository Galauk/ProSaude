<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusAtividadeColetiva extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_atividade_coletiva';
    protected $_primary = 'eav_codigo';
    
    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("esv"=>"esus_atividade_coletiva"))
                    ->join(array("tbfac"=>"tb_cds_ficha_ativ_col"),
                        "esv.co_cds_ficha_ativ_col = tbfac.co_cds_ficha_ativ_col",array(""))
                    ->join(array("usr"=>"usuarios"),"tbfac.usr_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("uni"=>"unidade"),"tbfac.uni_codigo=uni.uni_codigo",array("uni_desc"))
                    ->where("uuid_ficha = ?",$uuid);
                    die($sql);
                    // echo "<pre>";print_r($sql);die();wha
        return $this->fetchAll($sql);
    }

    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function getQuantidadeFichaExpAtivColetiva($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("eav" => "esus_atividade_coletiva"), array("count(*) total"))
        ->join(array("uni" => "unidade"),"eav_uni_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
        ->group(array("uni.uni_codigo"));
        if($data_ini){
            $where->where("eav_dt_atividade >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eav_dt_atividade <= ?",$data_fim);
        }
        if($unidade){
            $where->where("uni_codigo = ?",$unidade);
        }
        return $this->fetchAll($where);
    }
    
    public function getQuantidadeFichaExpAtivColetivaPmaq($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("eav" => "esus_atividade_coletiva"), array("count(*) total"))
        ->joinLeft(array("ue" => "tb_equipe"),"eav.eav_responsavel_ine = ue.nu_ine ", "");
        if($data_ini){
            $where->where("eav_dt_atividade >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eav_dt_atividade <= ?",$data_fim);
        }
        if($ine){
            $where->where("nu_ine = ?",$unidade);
        }
        return $this->fetchRow($where);
    }
    
}
