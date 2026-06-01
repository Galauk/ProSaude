<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_EstratificacaoLista extends Elotech_Db_Table_Abstract {

    protected $_name = 'estratificacao_lista';
	protected $_primary = 'id_estlista';

    public function salvar(array $data) {
        
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar a ficha de estratificação ".$exc->getMessage());
        }
    }
    
    public function pegaEspecialidades(){
        $sql = $this->getDefaultAdapter()->query(
            " SELECT * FROM especialidade WHERE esp_encaminhamento = true
            "
        )->fetchAll();
        return $sql;
    }

    public function buscaEspec($term){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT * FROM especialidade where esp_encaminhamento = true and esp_nome ilike '%$term%'
            "
        )->fetchAll();

        $out = array();

        foreach ($sql as $usu) {
            $out [] = array(
                    "id" => $usu[esp_codigo],
                    "label" => trim($usu[esp_nome]),
                    "data" => $usu
            );
        }
        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => $usu
            );
        }
        return $out;
    }

    public function carregaMonitoramento($codigoLista){
        
        $recebeCodigoLista = $codigoLista;
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT moni_baixo.desc_monitoramento baixo, moni_medio.desc_monitoramento medio, moni_alto.desc_monitoramento alto from estratificacao_lista as lista
                inner join tb_monitoramento as moni_baixo
                    on moni_baixo.id_monitoramento = lista.est_monitoramento_baixo
            
                inner join tb_monitoramento as moni_medio
                    on moni_medio.id_monitoramento = lista.est_monitoramento_medio
            
                inner join tb_monitoramento as moni_alto
                    on moni_alto.id_monitoramento = lista.est_monitoramento_alto
            
                where lista.id_estlista = $recebeCodigoLista
            ")->fetchAll();
        
        return $sql;

    }
}
