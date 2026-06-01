<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCdsDomicilioResposta extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_domicilio_resposta';
    protected $_primary = 'co_seq_cds_domicilio_resposta';
    
    public function salvar(array $data) {
        $this->emptyToUnset($data);
        $rua_codigo = parent::salvar($data);
        return $rua_codigo; 
    }
    
    public function deletaTodosPorDomicilio($dom_codigo){
        //die($dom_codigo);
        return $this->delete("co_cds_cad_domiciliar = $dom_codigo");
    }
    
    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("dr"=>"tb_cds_domicilio_resposta"),array('co_cds_cad_domiciliar AS dom_codigo'))
                    ->join(array("dom"=>"domicilio"),"dr.co_cds_cad_domiciliar=dom.dom_codigo",array("dom_numero","dom_complemento"))
                    ->joinLeft(array('usu'=>'usuario'),'dom.usu_codigo_responsavel=usu.usu_codigo',array('usu_nome'))
                    ->join(array('rua'=>'rua'),'dom.rua_codigo=rua.rua_codigo',array('rua_nome','rua_cep'))
                    ->where("uuid_ficha = ?",$uuid);
    //    die($sql);
        return $this->fetchAll($sql);
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
}
