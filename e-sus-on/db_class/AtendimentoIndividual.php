<?php
//namespace esus\banco_cidadao;
include_once $_SESSION['root']."WebSocialComum/global.php";

class BancoAtendimentoIndividual {
	
	public function getDados(){
		$sql = "SELECT * FROM esus_atendimento_individual WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_fetch_all($query);
	}
	
	public function getQtdRegistros(){
		$sql = "SELECT * FROM esus_atendimento_individual WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		$numRegistro = pg_num_rows($query);
		return $numRegistro;
	}
	
	public function getCiaps($ateCodigo){
		$sql = "SELECT 
					tbc.co_ciap 
				FROM 
					rl_cds_atend_individual_ciap as rlai 
				INNER JOIN 
					tb_ciap AS tbc ON rlai.co_ciap=tbc.co_seq_ciap
				WHERE 
					rlai.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}
	
	public function getCondutas($ateCodigo){
		$sql = "SELECT 
					tbtc.co_cds_tipo_conduta 
				FROM 
					rl_cds_atend_individual_condut as rlac
				INNER JOIN 
					tb_cds_tipo_conduta AS tbtc ON rlac.tp_cds_conduta=tbtc.co_cds_tipo_conduta
				WHERE 
					rlac.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}
	
	public function getExames($ateCodigo){
		$sql = "SELECT 
					proc.proc_codigo_sus 
				FROM 
					requisicao_exames AS re
				INNER JOIN 
					procedimento AS proc ON re.proc_codigo=proc.proc_codigo
				WHERE
					re.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}
	
	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE esus_atendimento_individual SET uuid_ficha = '".$uuid."' WHERE ate_codigo = '".$codigo."'";
		$query = pg_query($sql);
	}
	
}
?>
