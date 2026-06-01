<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusConsumoAlimentar extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_consumo_alimentar';
    protected $_primary = 'eca_codigo';
    
    public function getDadosPorUuid($uuid=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eca"=>"esus_consumo_alimentar"))
                    ->join(array("tca"=>"tb_cds_consumo_alimentar"),"tca.co_seq_cds_consumo_alimentar = eca.co_cds_consumo_alimentar",array(""))
                    ->join(array("ate"=>"atendimento"),"ate.ate_codigo = tca.ate_codigo",array(""))
                    ->join(array("usr"=>"usuarios"),"usr.usr_codigo = ate.med_codigo",array("usr_nome"))
                    ->join(array("uni"=>"unidade"),"uni.uni_codigo = ate.uni_codigo",array("uni_desc"))
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

    public function excluir($codConsumo){
        $item = $this->fetchAll("co_cds_consumo_alimentar=$codConsumo");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }

}
