<?php
	session_start();
	@header("Content-Type: text/html; charset=ISO-8859-1",true);
	
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$sql = pg_query("SELECT medico_especialidade.esp_codigo, 
							esp_nome 
					   FROM medico_especialidade, 
							especialidade 
					  WHERE medico_especialidade.esp_codigo=especialidade.esp_codigo 
					    AND medico_especialidade.med_codigo='$med_codigo'");
	//echo pg_last_error($db);
	while($linha = pg_fetch_array($sql))
	{
		echo "$linha[0]-$linha[1];";
	}
?>