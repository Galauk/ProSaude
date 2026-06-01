<?php

	@header('Content-Type: text/html; charset=ISO-8859-1');	
	include_once "global.php";
	$usr_codigo = $_GET['usr_codigo'];
	//echo $_SESSION[logon][usr]->uni_codigo;
	$sqlEsp = "SELECT esp.esp_codigo,esp.esp_nome 
	             FROM medico_especialidade AS me
			     JOIN especialidade AS esp
			       ON esp.esp_codigo=me.esp_codigo
				 
			    WHERE me.med_codigo=$usr_codigo
				  AND mes_ativo = 'A'
				  and me.uni_codigo = ".$_SESSION[uni_codigo]."
			    ORDER BY esp.esp_nome";
	
	
	
	$option = array(
		"nome" => "especialidade",
		"valor" => NULL,
		"sql" => $sqlEsp,
		"disabledFirst" => true
	);
	
	$form = new classForm();
	
	$select = $form->inputSelect($option);
	echo $select;
	
