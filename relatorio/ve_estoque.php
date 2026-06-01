<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$sql = "select calcula_estoque('{$prod}','{$str}',date(now())) from produto where pro_codigo = '{$prod}'";
	$exec_sql = pg_query($sql);
	
	$result = pg_fetch_array($exec_sql);
	echo formata_valor0($result[0]);

?>
