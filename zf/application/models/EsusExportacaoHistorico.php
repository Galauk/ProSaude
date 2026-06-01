<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusExportacaoHistorico extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_exportacao_historico';
    protected $_primary = 'eeh_codigo';
    //protected $_sequence = 'esus_atendimento_individual_eai_codigo_seq';

    public function ultimasExportacoes(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("eeh"=>"esus_exportacao_historico"),array("eeh_codigo","eeh.eeh_data_inicial","hora_inicial"=>"to_char(eeh.eeh_data_inicial,'HH24:MI:SS')","eeh.eeh_data_final","hora_final"=>"to_char(eeh.eeh_data_final,'HH24:MI:SS')"))
                    ->join(array("eehi"=>"esus_exportacao_historico_itens"),"eehi.eeh_codigo=eeh.eeh_codigo","");
        return $this->fetchAll($sql);
    }
    
    
}
