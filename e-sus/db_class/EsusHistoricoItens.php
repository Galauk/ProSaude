<?php
include_once $_SESSION['root'].$_SESSION['modulo']."global.php";
//include_once "../../global.php";
class EsusHistoricoItens {
	
    public function registratHistoricoItens($eeh_codigo,$uuid_ficha,$tfe_codigo){
            $sqlInsert = "INSERT INTO esus_exportacao_historico_itens (tfe_codigo,uuid_ficha,eehi_data_exportacao,eeh_codigo) VALUES ($tfe_codigo,'$uuid_ficha',NOW(),$eeh_codigo)";
            pg_query($sqlInsert) or die($sqlInsert.  pg_last_error());
            return true;

    }

    public function endHistorico($idHistorico){

    }
}



