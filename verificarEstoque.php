<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$id_login = $_GET['id_login'];
	$pro_codigo = $_GET['pro_codigo'];
	
	$selecionaSetor = "SELECT set_codigo
					     FROM usuarios
					    WHERE usr_codigo = $id_login";
	$exec = pg_query($selecionaSetor);
	$dado = pg_fetch_array($exec);
	$set_codigo = $dado['set_codigo'];
	
	$select = "SELECT sum(s.sal_qtde) as estoque
				 FROM saldo s
				WHERE s.pro_codigo = $pro_codigo
				  AND s.set_codigo = $set_codigo";
	$exec = pg_query($select);
	$dado = pg_fetch_array($exec);
	$estoque = $dado['estoque'];
	
	echo $estoque;
?>