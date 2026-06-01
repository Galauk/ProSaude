<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico WHERE med_codigo='$med_codigo'";
	$manut_row = pg_fetch_array(pg_query($stmt_lab));
	// o select verifica se a data entrada está num intervalo válido
	// pelo menos 30 dias maior que o maior entrado
		$stmt = "SELECT 
			to_char(gex_periodo, 'dd/mm/yyyy') as max, 
			TO_CHAR(gex_periodo+29,'dd/mm/YYYY') as prox_max, gex_periodo, gex_codigo
			FROM grade_exame_mensal AS m
			WHERE med_codigo = {$med_codigo} 
			order by gex_periodo desc";
	$per_row = pg_query($stmt);
	
	while($linha = pg_fetch_array($per_row))
	{
		echo "$linha[0]-$linha[1]-$linha[3]; ";
	}
?>