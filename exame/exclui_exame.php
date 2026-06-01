<?php
	require_once '../global.php';
	//setError(1);
	$agei_codigo = $_POST["agei_codigo"];
	// Coletas realizadas, se foi são excluídas
	$sql_del_col = "DELETE FROM coleta WHERE agei_codigo = '".$agei_codigo."'";
	$query_del_col = pg_query($sql_del_col);
	// Resultado de exame, se tiver são excluidos
	$sql_del_res = "DELETE FROM resultadoexame WHERE agei_codigo = '".$agei_codigo."'";
	$query_del_res = pg_query($sql_del_res);
	// Item agendado, se tiver é excluído
	$sql_del_agei = "DELETE FROM agenda_itens WHERE agei_codigo = '".$agei_codigo."'";
	$query_del_agei = pg_query($sql_del_agei);
?>