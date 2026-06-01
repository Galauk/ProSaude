<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$qtde = $_GET[qtde];
	$sal_lote = $_GET['sal_lote'];
	$set_codigo = $_GET['set_codigo'];
	$lote = explode(':',$sal_lote);
	if(empty($sal_lote))
	{
		$select = "SELECT sum(sal_qtde) from saldo  where pro_codigo = $pro_codigo AND set_codigo = $set_codigo";
	}else{
		$select = "SELECT sal_qtde from saldo where pro_codigo = $pro_codigo AND sal_lote = '$lote[0]' AND set_codigo = $set_codigo";
	}
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	if($linha[0] > 0)
	{
		echo "S"."###".$linha[0];
	} else {
		echo "N";
	}
	
?>