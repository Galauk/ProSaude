<?php	

	/**
	 * Este arquivo analiza as inconsistencias de um único item do BPA
	 */

	require_once '../global.php';
	require_once 'bpa_functions.php';
	
	if(!isset($bpa_codigo))
		$bpa_codigo = $_GET['bpa_codigo'];
	
	if(!isset($bpa_codigo))
		die("Informe código do BPA");
		
		
	$sql = "SELECT *,
	               TO_CHAR(bpa_data,'YYYY-MM') AS mes_ref 
	          FROM bpa 
	         WHERE bpa_codigo=$bpa_codigo";
	
	$query = pg_query($sql);	
	
	// limpar todas as inconsistencias já existentes
	$sub_sql = "UPDATE bpa 
	               SET bpa_status_inconsistencia = NULL 
	             WHERE bpa_codigo=$bpa_codigo";
	
	$sub_query = pg_query($sub_sql);
	fdebug("Atualizou o BPA para inconsistenias: NULL");
	
	$sub_sql = "DELETE FROM rl_bpa_inconsistencia 
	             WHERE bpa_codigo = $bpa_codigo";
	$sub_query = pg_query($sub_sql);
	fdebug("Deletou todas as inconsistências: ".pg_affected_rows($sub_query));
	
	$r = pg_fetch_array($query);
	list($ano,$mes) = explode("-",$r['mes_ref']);

	$erros = 0;
	validacoes();
	
	// Houve erros?
	$status_inconsistencia = $erros?"t":"f";
	$sub_sql = "UPDATE bpa SET bpa_status_inconsistencia='$status_inconsistencia' WHERE bpa_codigo={$r['bpa_codigo']};";
	//fdebug($sub_sql);
	pg_query($sub_sql);
	
	