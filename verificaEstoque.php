<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$set_codigo = $_GET['set_codigo'];
	$quantidade = $_GET['ite_quantidade'];
	$pro_codigo = $_GET['pro_codigo'];
	
	$sql = "SELECT sum(sal_qtde) as soma
			  FROM saldo
			 WHERE set_codigo = $set_codigo
			   AND pro_codigo = $pro_codigo
			   AND sal_qtde > 0";
	
	$query = pg_query($sql);
	$res = pg_fetch_array($query);
	$qtdeEstoque = $res['soma'];
	
	if($quantidade >= $qtdeEstoque){
		$cont = 1;
	}else{
		$cont = 2;
	}
	echo $cont;
?>