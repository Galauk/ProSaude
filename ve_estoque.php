<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$prod = $_GET['prod'];
$str = $_GET['str'];
$lote = 'SEM_LOTE';
	   
$sql = "SELECT sal_qtde,
			   qtde_dispensado($prod) as dispensado
		  FROM saldo
		 WHERE pro_codigo = $prod
		   AND sal_lote = '$lote'
		   AND set_codigo = $str";
$exec_sql = pg_query($sql);
if (pg_num_rows($exec_sql) == 0){
	$estoque = 0;
	$dispensado = 0;
	echo "1";
	exit;
}else{
	$result = pg_fetch_array($exec_sql);
	$estoque = $result['sal_qtde'];
	$dispensado = $result['dispensado'];
}
$total = $estoque - $dispensado;
echo "Estoque: " . formata_valor0($estoque) . " Dispensado: " . formata_valor0($dispensado) . "  Liquido: " . formata_valor0($total);

?>
