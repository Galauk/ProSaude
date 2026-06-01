<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusAtendimentoIndividual extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_atendimento_individual';
    protected $_primary = 'eai_codigo';
    protected $_sequence = 'esus_atendimento_individual_eai_codigo_seq';

    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("esv"=>"esus_atendimento_individual"))
                    ->join(array("tla"=>"tb_local_atend"),"esv.co_local_atend=tla.co_local_atend",array("no_local_atend","co_local_atend"))
                    ->where("uuid_ficha = ?",$uuid);
                    // die($sql);
        return $this->fetchAll($sql);
    }
    
    public function getDadosPorId($id){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("esv"=>"esus_atendimento_individual"))
                    ->join(array("tla"=>"tb_local_atend"),"esv.co_local_atend=tla.co_local_atend",array("no_local_atend","co_local_atend"))
                    ->where("eai_codigo = ?",$id);
        return $this->fetchRow($sql);
    }
    
    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao atualizar dados: ".$exc->getMessage());
        }
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function getQuantidadeFichaExpAtendIndivudual($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("eai" => "esus_atendimento_individual"), array("count(*) total"))
            ->join(array("uni" => "unidade"),"eai_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
            ->group(array("uni.uni_codigo"));
            if($data_ini){
                $where->where("eai_dtatendimento >= ?",$data_ini);
            }
            if($data_fim){
                $where->where("eai_dtatendimento <= ?",$data_fim);
            }
            if($unidade){
                $where->where("uni_codigo = ?",$unidade);
            }
            
         // die($where);
        return $this->fetchAll($where); 
    }
    
    public function getQuantidadeFichaExpAtendIndivudualPmaq($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("eai" => "esus_atendimento_individual"), array("count(*) as total"))
        ->join(array("ate" => "atendimento")," ate.ate_codigo = eai.ate_codigo", "")
        ->join(array("usr" => "usuarios"),"ate.med_codigo = usr.usr_codigo", "")
        ->joinLeft(array("ueq" => "usuarios_equipe"),"ueq.usr_codigo = usr.usr_codigo", "")
        ->joinLeft(array("ue" => "tb_equipe"),"ue.co_seq_equipe = ueq.co_equipe", "");
        if($data_ini){
            $where->where("eai_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eai_dtatendimento <= ?",$data_fim);
        }
        if($ine){
            $where->where("nu_ine = ?",$unidade);
        }
        
        //die($where);
        return $this->fetchRow($where);
    }
    public function getFichaPorData($data_ini=false,$data_fim=false){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("eai" => "esus_atendimento_individual"))
                        ->join(array("ate" => "atendimento"),"ate.ate_codigo = eai.ate_codigo", "")
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("cnes_cod_cns","usr_nome"))
                        ->join(array("usu"=>"usuario"),"usu.usu_codigo=ate.ate_codigo",array("usu_nome","usu_cartao_sus"));
        
        if($data_ini){
            $where->where("eci_usr_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eci_usr_dtatendimento <= ?",$data_fim);
        }
//       die($where);
        return $this->fetchAll($where);
    }
    
    
    
}
