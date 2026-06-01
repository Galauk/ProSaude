<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$medcod = $_GET["medcod"];
	$sql = "SELECT quota_qtde 
			  FROM medico 
			 WHERE med_codigo = $medcod";
	$query = pg_query($sql) or die($sql."<br>".pg_last_error());
	$linha = pg_fetch_array($query);
	echo $linha[quota_qtde];
	
?>