<?php
	error_reporting(E_ALL);
	include "./global.php";
	include_once $_SESSION["root"].$_SESSION["comum"].'/library/php/funcoes.db.php'; // getConfig;

	$senha = md5($_POST['novaSenha']);
	$usr_login = $_POST['usr_login'];
	$novaData = date('Y-m-d', strtotime('today + 60 days'));
	$sql = "UPDATE usuarios SET usr_senha = '$senha', usr_data_validade_senha = '$novaData' WHERE usr_login = '$usr_login'";
	// die($sql);
	
	$resultado = pg_query($sql) or die(pg_last_error());

	if ($resultado) {
		echo json_encode(array("URL"=>"auth.php"));
	} else {
		echo json_encode("false");
	}
	// echo "chegou aqui";
	
?>