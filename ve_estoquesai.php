<?php
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$sql = "SELECT sum(sal_qtde) as soma
			  FROM saldo
			 WHERE set_codigo = $str
			   AND pro_codigo = $prod
			   AND sal_qtde > 0";
	$exec_sql = pg_query($sql);
	$result = pg_fetch_array($exec_sql);

	$sqlsai = "SELECT sum(sal_qtde) as soma
				 FROM saldo
				WHERE set_codigo = $strt
				  AND pro_codigo = $prod
				  AND sal_qtde > 0";
	$exec_sql_sai = pg_query($sqlsai);
	$resultsai = pg_fetch_array($exec_sql_sai);

	echo "
		<table width='100%' border='0'>
			<tr>
				<td width='30%' style='color:blue'>
					<b>Estoque Atual Entrada:</b>
				</td>
				<td width='10%' style='color:red'>
					<b>$result[0]</b>
				</td>
				<td width='30%' style='color:blue'>
					<b>Estoque Atual Sa&iacute;da:</b>
				</td>
				<td width='15%' style='color:red'>
					<b>$resultsai[0]</b>
				</td>
			</tr>
		</table>";
	 
	unset($produto);
	unset($setor);
	pg_close();

?>
