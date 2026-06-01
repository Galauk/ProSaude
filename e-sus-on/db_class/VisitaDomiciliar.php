<?php
//namespace esus\banco_cidadao;
include_once $_SESSION['root']."WebSocialComum/global.php";

class VisitaDomiciliar {
	
	public function getDadosVisitaDomiciliar(){
		$sql = "SELECT * FROM esus_visita_domiciliar WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_fetch_all($query);
	}
	
	public function numDadosVisitaDomiciliar(){
		$sql = "SELECT * FROM esus_visita_domiciliar WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_num_rows($query);
	}
	
	public function getCodigosVisita($codVisita){
		$sql = "SELECT co_cds_visita_dom_motivo FROM rl_cds_visita_dom_motivo WHERE co_cds_visita_domiciliar = '".$codVisita."'";
		$query = pg_query($sql);
		return pg_fetch_all($query);	
	}
	
	public function getDadosOriginadoraRemetente(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql);
		return pg_fetch_array($query);	
	}
	
	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE esus_visita_domiciliar SET uuid_ficha = '".$uuid."' WHERE ate_codigo = '".$codigo."'";
		$query = pg_query($sql);
	}
	
}

?>
