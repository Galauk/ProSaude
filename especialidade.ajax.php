<?php

	require_once 'global.php';
	setError(1);
	
	$esp_codigo = $_POST['esp_codigo'];
	$tipo = ($_POST['tipo']=="pc")?"pre_consulta":"encaminhamento";
	
	if($_POST['tipo']=="pc"){
		$tipo = "pre_consulta";
	}
	if($_POST['tipo']=="enc"){
		$tipo = "encaminhamento";
	}else if ($_POST['tipo'] != "pc" && $_POST['tipo'] != "enc"){
		$tipo = "mais_agendamento";
	}
	
	$to = ($_POST['to'])?"true":"false";
	
	$sql = "UPDATE especialidade 
	           SET esp_{$tipo}={$to}
	         WHERE esp_codigo=$esp_codigo;";
	fdebug($sql);
	$query = pg_query($sql);
	die($_POST['to']);