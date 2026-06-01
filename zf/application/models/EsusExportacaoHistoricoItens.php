<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusExportacaoHistoricoItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_exportacao_historico_itens';
    protected $_primary = 'eehi_codigo';
    //protected $_sequence = 'esus_atendimento_individual_eai_codigo_seq';

    public function ultimasExportacoes(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("eeh"=>"esus_exportacao_historico"),array("eeh_codigo","eeh.eeh_data_inicial","hora_inicial"=>"to_char(eeh.eeh_data_inicial,'HH24:MI:SS')","eeh.eeh_data_final","hora_final"=>"to_char(eeh.eeh_data_final,'HH24:MI:SS')"))
                    ->join(array("eehi"=>"esus_exportacao_historico_itens"),"eehi.eeh_codigo=eeh.eeh_codigo","");
        
        return $this->fetchAll($sql);
    }
    
    public function todasExportacoesDoTipoFicha($tipoFicha){
        
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("eehi"=>"esus_exportacao_historico_itens"),array("eehi.tfe_codigo","eehi.uuid_ficha","eehi.eeh_codigo"))        
                    ->where("eehi.eeh_codigo = ?", $tipoFicha);
        
        return $this->fetchAll($sql);        
        
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function deletarTodosItensDoHistorico($eeh_codigo){
        
        $item = $this->fetchAll("eeh_codigo = $eeh_codigo");
            if ($item)
                foreach($item as $value) {
                    $value->delete();
                }
            return true;
        
    }
    
}
