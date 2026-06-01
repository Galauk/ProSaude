<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ItensMovimentoBkp extends Elotech_Db_Table_Abstract {

    protected $_name = 'itens_movimento_bkp';
    protected $_primary = 'ite_codigo';
    //protected $_sequence = 'seq_ite_codigo';

    public function salvar(array $data) {
        try {
            parent::salvar($data);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar: ".$ex->getMessage());
        }
    }
    
    public function salvarItensPorSetor($setores=FALSE,$data=FALSE) {
        // Se data vier preenchida faz consula por data
        if($data)
            $sqlData = "AND mov_data <= '$data'";
        
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("INSERT INTO social.itens_movimento_bkp(
                        ite_codigo, mov_codigo, pro_codigo, ite_vlrdesc, ite_lote,
                        ite_validade, usr_codigo, ite_timestamp, ite_ip, ite_status,
                        ite_quantidade, ite_consolidado, ite_vlrunit, ite_qtde_solicitada,
                        ite_posologia, ite_detalhes_tratamento, ite_observacoes, ite_qtde_dia,
                        ite_vlrtotal, ite_lote_bkp, ite_validade_bkp, ite_dose,
                        ite_dose_lote, ite_duracao	 	
                      )
                      SELECT DISTINCT   
                        ite_codigo, ite.mov_codigo, pro_codigo, ite_vlrdesc, ite_lote,
                        ite_validade, ite.usr_codigo, ite_timestamp, ite_ip, ite_status,
                        ite_quantidade, ite_consolidado, ite_vlrunit, ite_qtde_solicitada,
                        ite_posologia, ite_detalhes_tratamento, ite_observacoes, ite_qtde_dia,
                        ite_vlrtotal, ite_lote_bkp, ite_validade_bkp, ite_dose,
                        ite_dose_lote, ite_duracao
                      FROM 
                        social.itens_movimento AS ite
                      INNER JOIN 
                        movimento AS mov ON ite.mov_codigo=mov.mov_codigo
                      WHERE
                        (mov.set_entrada IN ($setores) OR 
                         mov.set_saida IN ($setores))
                         $sqlData")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar itens movimento: ".$ex->getMessage());
        }
    }
    
    public function atualizarPro($de, $para){
            $de = (array)$de;

            $data = array("pro_codigo" => $para);
            $where = $this->select()->where("pro_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando produtos em ".$this->_name, Zend_Log::INFO);
            Zend_Registry::get("logger")->log("sql".$where, Zend_Log::INFO);

            return $this->update($data, $where);
    }

}
