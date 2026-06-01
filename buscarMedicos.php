<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$sql = "select a.med_codigo, a.med_nome
					from medico a, especialidade b, medico_especialidade c
					where a.med_codigo = c.med_codigo
					and b.esp_codigo = c.esp_codigo
					and b.esp_codigo = {$_GET[esp_codigo]}
					order by a.med_nome";
					
	$exec_sql = pg_query($sql);

	while($linha = pg_fetch_array($exec_sql))
	{
		echo "$linha[0]-$linha[1];";
	}
	
?>
