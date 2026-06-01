<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_MovimentoBkp extends Elotech_Db_Table_Abstract {
    
    protected $_name = "movimento_bkp";
    protected $_primary = "mov_codigo";
    //protected $_sequence = false;
    
    /*public function salvar($data) {
        try {
            parent::salvar($data);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar: ".$ex->getMessage());
        }
    }*/
    
    public function salvarPorSetor($setores=FALSE, $data=FALSE) {
        // Se data vier preenchida faz consula por data
        if($data)
            $sqlData = "AND mov_data <= '$data'";
        
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("INSERT INTO social.movimento_bkp (
                            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo, mov_desconto, mov_observacao, cond_codigo, ate_codigo, set_entrada,
                            set_saida, mov_nr_nota, mov_dt_nota, usr_codigo, mov_ip, mov_total_nota, mov_data_inclusao, mov_entrada, mov_saida,  
                            mov_acrescimo, mov_tipo_acrescimo, inv_codigo, mov_num_receita, gru_codigo, age_codigo, mov_requisitante, req_codigo,
                            usr_codigo_responsavel, eve_codigo, med_codigo_interno, med_codigo_externo
                           )
                           SELECT
                            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo, mov_desconto, mov_observacao, cond_codigo, ate_codigo, set_entrada,
                            set_saida, mov_nr_nota, mov_dt_nota, usr_codigo, mov_ip, mov_total_nota, mov_data_inclusao, mov_entrada, mov_saida,  
                            mov_acrescimo, mov_tipo_acrescimo, inv_codigo, mov_num_receita, gru_codigo, age_codigo, mov_requisitante, req_codigo,
                            usr_codigo_responsavel, eve_codigo, med_codigo_interno, med_codigo_externo
                           FROM
                            social.movimento
                           WHERE
                             (set_entrada IN ($setores) OR
                              set_saida IN ($setores))
                              $sqlData")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar movimento: ".$ex->getMessage());
        }
    }
    
    public function salvar() {
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("INSERT INTO movimento_bkp (
                            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo, mov_desconto, mov_observacao, cond_codigo, ate_codigo, set_entrada,
                            set_saida, mov_nr_nota, mov_dt_nota, usr_codigo, mov_ip, mov_total_nota, mov_data_inclusao, mov_entrada, mov_saida,  
                            mov_acrescimo, mov_tipo_acrescimo, inv_codigo, mov_num_receita, gru_codigo, age_codigo, mov_requisitante, req_codigo,
                            usr_codigo_responsavel, eve_codigo, med_codigo_interno, med_codigo_externo
                           )
                           SELECT
                            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo, mov_desconto, mov_observacao, cond_codigo, ate_codigo, set_entrada,
                            set_saida, mov_nr_nota, mov_dt_nota, usr_codigo, mov_ip, mov_total_nota, mov_data_inclusao, mov_entrada, mov_saida,  
                            mov_acrescimo, mov_tipo_acrescimo, inv_codigo, mov_num_receita, gru_codigo, age_codigo, mov_requisitante, req_codigo,
                            usr_codigo_responsavel, eve_codigo, med_codigo_interno, med_codigo_externo
                           FROM
                            movimento")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception($ex->getMessage());
        }
    }
    
    public function atualizarUsu($de, $para){
            $de = (array)$de;

            $data = array("usu_codigo" => $para);
            $where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

            return $this->update($data, $where);
    }
    
}

?>

