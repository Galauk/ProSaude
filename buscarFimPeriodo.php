<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$gex_codigo = $_GET['gex_codigo'];
	//echo $gex_codigo;
	$sql = "select TO_CHAR(gex_periodo,'dd/mm/YYYY') as max,	
				   TO_CHAR(gex_periodo+29,'dd/mm/YYYY') as prox_max 
			  from grade_exame_mensal 
			 where gex_codigo = $gex_codigo";
	$exe_sql = pg_query($sql);
	$res_exe= pg_fetch_array($exe_sql);
	
	$dia_fim = $res_exe['prox_max'];
	$dia_ini = $res_exe['max'];
	
	echo $dia_ini."-".$dia_fim;
?>