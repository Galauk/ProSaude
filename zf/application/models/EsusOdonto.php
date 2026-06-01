<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusOdonto extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_odonto';
    protected $_primary = 'eo_codigo';

    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eo"=>"esus_odonto"),array("eo_codigo","odo_pcon_codigo","eo_profissional_cns","eo_dtnascimento","eo_num_cartao_sus","eo_sexo"))
                    ->join(array("tla"=>"tb_local_atend"),"eo.co_local_atend=tla.co_local_atend",array("no_local_atend","co_local_atend"))
                    ->where("uuid = ?",$uuid);
        return $this->fetchAll($sql);
    }

    public function getDadosPorId($id){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eo"=>"esus_odonto"),array("eo_codigo","odo_pcon_codigo","eo_profissional_cns","eo_dtnascimento","eo_num_cartao_sus","eo_sexo"))
                    ->join(array("tla"=>"tb_local_atend"),"eo.co_local_atend=tla.co_local_atend",array("no_local_atend","co_local_atend"))
                    ->where("eo_codigo = ?",$id);
        return $this->fetchRow($sql);
    }

    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar dados: ".$exc->getMessage());
        }
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid" => "");
            $where = $this->select()->where("uuid = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function getQuantidadeFichaOdonto($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("eo" => "esus_odonto"), array("count(*) total"))
        ->join(array("uni" => "unidade"),"eo.eo_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
        ->group(array("uni.uni_codigo"));
        if($data_ini){
            $where->where("eo_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eo_dtatendimento <= ?",$data_fim);
        }
        if($unidade){
            $where->where("uni_codigo = ?",$unidade);
        }
        return $this->fetchAll($where);
    }
    
    public function getQuantidadeFichaOdontoPmaq($data_ini=false,$data_fim=false,$ine=false){
        $procedimento = array( '0101020015', '0101020031' , '0101020040' , '0307020010' , '0301050023' ,
                               '0307020029', '0414020120' , '0414020138' , '0301010153' , '0307020070' , 
                               '0307030016', '0307030024' , '0307010023' , '0307010031' , '0307010040' , 
                               '0101020090', '0414020383');
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("odo" => "esus_odonto"), array("count(*) total"))
        ->join(array("odopc" => "odonto_procedimentos_controle"),"odo.odo_pcon_codigo = odopc.odo_pcon_codigo", "")
        ->join(array("atend" => "atendimento"),"odopc.ate_codigo = atend.ate_codigo", "")
        ->join(array("pa" => "procedimento_atendimento"),"atend.ate_codigo = pa.ate_codigo", "")
        ->join(array("pr" => "procedimento"),"pa.proc_codigo = pr.proc_codigo", "")
        ->join(array("usuarios" => "usuarios"),"atend.med_codigo = usuarios.usr_codigo", "")
        ->joinLeft(array("ueq" => "usuarios_equipe"),"ueq.usr_codigo = usuarios.usr_codigo", "")
        ->joinLeft(array("ue" => "tb_equipe"),"ue.co_seq_equipe = ueq.co_equipe", "")
        ->where("pr.proc_codigo_sus IN (?)", $procedimento);
        if($data_ini){
            $where->where("eo_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("eo_dtatendimento <= ?",$data_fim);
        }
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
        //die($where);
        return $this->fetchRow($where);
    }
    
    
    
    
}
