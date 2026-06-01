<?php

	require_once '../global.php';
	//setError(1);
	
	$agei_codigo = $_POST['agei_codigo'];
	$tipo = $_POST['tipo'];
	$to = $_POST['to'];
	if($to==1){
		$sql_data = "SELECT * FROM agenda_itens WHERE agei_codigo = $agei_codigo";
		$qry_data = pg_query($sql_data);
		$reg_data = pg_fetch_array($qry_data);
		$sql = "INSERT INTO COLETA(agei_codigo,col_data_coleta)VALUES($agei_codigo,'$reg_data[agei_data]')";
	
	}else{
		$sql = "DELETE FROM COLETA WHERE agei_codigo = $agei_codigo";
	}	
	
//	fdebug($sql);
	$query = pg_query($sql);
	die($_POST['to']);