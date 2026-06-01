<?php
include_once $_SESSION['root']."WebSocialComum/global.php";

class EsusHistorico {
    public function initHistorico(){
        
            $idHistorico = pg_fetch_array(pg_query("select nextval ('esus_exportacao_historico_eeh_codigo_seq')"))['nextval'];
            $sqlInsert = "INSERT INTO esus_exportacao_historico (eeh_codigo,eeh_data_inicial) VALUES ($idHistorico,NOW())";
            pg_query($sqlInsert);
            return $idHistorico;
        
    }
    
    public function endHistorico($idHistorico){
        $sqlUpdate = "UPDATE esus_exportacao_historico SET eeh_data_final = now() where eeh_codigo = $idHistorico";
        pg_query($sqlUpdate);
        return true;
    }
}



