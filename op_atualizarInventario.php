<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

	$inv_codigo  = $_GET["inv_codigo"];

	//verifica se o inventario esta dentro do periodo do setor
	$sql_set_periodo = pg_query("SELECT inv_codigo 
								   FROM inventario 
								   LEFT JOIN setor_periodo 
								     ON setor_periodo.set_codigo = inventario.set_codigo
								  WHERE inv_codigo = $inv_codigo
								    AND inv_data BETWEEN setp_data_inicial AND setp_data_final");
                
	if( pg_num_rows($sql_set_periodo) > 0 ){
		$resposta = attInventario($inv_codigo);
		echo "
		<SCRIPT LANGUAGE=\"JavaScript\">
			alert( \"$resposta\" );
			setTimeout(\"location='inventario.php?id_login=$id_login&acao=relatorio'\", 0);
		</SCRIPT>";
	}
	else{
		echo "
		<SCRIPT LANGUAGE=\"JavaScript\">
			alert( \"Nao foi possivel efetuar a movimentacao, data nao permitida\" );
			setTimeout(\"location='cadInventario.php?id_login=$id_login&acao=relatorio'\", 1000);
		</SCRIPT>";
	}
?>