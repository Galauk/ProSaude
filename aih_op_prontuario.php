<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	// operacao via ajax
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	
	//var_dump($_GET);
	
	if( $acao == 'atualiza_prontuario' )
	{
		if( $aih == 'S' )
			$stmt = "UPDATE aih_paciente SET pac_prontuario = '$prontuario' WHERE pac_codigo = $codigo";
		else
			$stmt = "UPDATE usuario SET usu_prontuario = '$prontuario' WHERE usu_codigo = $codigo";
	
		//echo $stmt;
		db_query($stmt);
	}
?>