<?php
	
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$cid_codigo				= $_GET["cid_codigo"];
	$cadastra_rua			= $_GET["cadastra_rua"];
	$dom_codigo 			= $_GET["dom_codigo"];
	$dom_data_cadastro 		= date('d/m/Y');
	$rua_nome 				= $_GET["rua_nome"];
	$rua_cep 				= $_GET["rua_cep"];
	$rua_codigo 			= $_GET["rua_codigo"];
	$rua_bairro 			= $_GET["rua_bairro"];
	$dom_numero 			= $_GET["dom_numero"];
	$dom_segmento 			= $_GET["dom_segmento"];
	$dom_complemento 		= $_GET["dom_complemento"];
	$co_tipo_domicilio 		= $_GET["co_tipo_domicilio"];
	$co_tipo_logradouro 	= $_GET["co_tipo_logradouro"];
	$usu_nome 				= $_GET["usu_nome"];
	$dom_telefone 			= $_GET["dom_telefone"];
	$usu_codigo_responsavel = $_GET["usu_codigo_responsavel"];
	
	if($cadastra_rua == 'S'){
		$nextVal = "select nextval('seq_rua_codigo') as rua_codigo";
		$queryNext = pg_query($nextVal);
		$res = pg_fetch_array($queryNext);
		$rua_codigo = $res["rua_codigo"];
		$insertRua = "INSERT INTO rua (rua_codigo,
									   rua_nome,
									   cid_codigo,
									   co_tipo_logradouro,
									   rua_cep,
									   rua_bairro)
								VALUES($rua_codigo,
									   '$rua_nome',
									   '$cid_codigo',
									   '$co_tipo_logradouro',
									   '$rua_cep',
									   '$rua_bairro')";
		pg_query($insertRua) or die(pg_last_error());
	}
	
	$sqlDp = "INSERT INTO domicilio 
						(dom_codigo, 
						dom_data_cadastro, 
						rua_codigo, 
						dom_numero, 
						dom_segmento, 
						dom_complemento, 
						co_tipo_domicilio,
						dom_telefone,
						usu_codigo_responsavel)
					VALUES
						($dom_codigo,
						'$dom_data_cadastro', 
						$rua_codigo, 
						'$dom_numero', 
						".($dom_segmento == "" ? "null" : "'$dom_segmento'").",
						".($dom_complemento == "" ? "null" : "'$dom_complemento'").", 
						".($co_tipo_domicilio == "" ? "'X'" : "'$co_tipo_domicilio'").",
						".($dom_telefone == "" ? "null" : "'$dom_telefone'").",
						".($usu_codigo_responsavel == "" ? "null" : "'$usu_codigo_responsavel'").")";
	$queryDp = pg_query($sqlDp);
	die($sqlDp);
?>