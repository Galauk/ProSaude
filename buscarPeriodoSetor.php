<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$dat = date("d/m/Y");
	
	if($dat < $_GET[data])
	{
		echo "not";
		exit();
	}
	
	$select = "select max(setp_codigo), set_codigo, setp_data_inicial, setp_data_final
					from setor_periodo
					where set_codigo = {$_GET[set_codigo]}
					and '{$_GET[data]}' between setp_data_inicial and setp_data_final
					group by set_codigo, setp_data_inicial, setp_data_final";
					
	$exec_select = pg_query($select);
	
	if(pg_num_rows($exec_select))
	{
		echo "yes";
	} else {
		echo "not";
	}
	
?>