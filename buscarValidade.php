<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$select2 = "select pro_validade from produto where pro_codigo = $_GET[pro_codigo]";
	$exec_select2 = pg_query($select2);
	$linha2 = pg_fetch_array($exec_select2);
	echo $linha2[0];
?>