<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$sql = "SELECT DISTINCT gra_hora_ini 
			  FROM grade_medico 
			 WHERE esp_codigo = $esp_codigo 
			   AND med_codigo = $med_codigo 
			   AND uni_codigo = $uni_codigo 
			   AND gra_data = '". ($data ? $data : date("d/m/Y"))."'  
			 ORDER BY gra_hora_ini";

	$exec_sql = pg_query($sql);
	echo pg_last_error($db);
	while($linha = pg_fetch_array($exec_sql))
	{
		echo $linha[0]."-";
	}
?>