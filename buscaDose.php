<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	if ($_GET) {
		$cod = trim($_GET["codProduto"]);
	} else {
		echo "Requisiчуo invсlida";
		exit;
	}
	$sql = "SELECT a.pro_fracionado,
				   *
			  FROM produto a
			 WHERE a.pro_codigo = $cod
			   AND a.pro_fracionado = 'S'
			 ORDER BY pro_nome";
	echo $sql;

	$query = pg_query($sql);
	$row = pg_fetch_array($query);
	echo $row[pro_fracionado];

?>