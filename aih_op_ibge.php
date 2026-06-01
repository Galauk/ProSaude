<?php
// operacao via ajax
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	
	//var_dump($_GET);
	
	if( $acao == 'atualiza_ibge' )
	{
		if( $aih == 'S' )
			$stmt = "UPDATE aih_paciente SET pac_ibge_codigo = '$numero_ibge' WHERE pac_codigo = $codigo";
		else
			$stmt = "UPDATE usuario SET usu_ibge_codigo = '$numero_ibge' WHERE usu_codigo = $codigo";
	
		//echo $stmt;
		db_query($stmt);
	}
?>