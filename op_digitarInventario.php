<?php

	include 'global.php';	
	$inv_codigo = $_GET["inv_codigo"];
	
	// tudo
	$sql = "SELECT pro_codigo,
	               pro_nome,
	               invp_quantidade,
	               invp_lote,
	               invp_validade
	          FROM inventario_produto AS invp
	          JOIN produto AS pro
	            ON pro.pro_codigo=invp.pro_codigo
	         WHERE inv_codigo=$inv_codigo";