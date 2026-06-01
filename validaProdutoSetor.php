<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$codProduto = $_GET['pro_codigo'];
	$codSetor = $_GET['set_codigo'];
	
	$select = "SELECT *
				 FROM produto_setor
				WHERE pro_codigo = $codProduto
				  AND set_codigo = $codSetor";
	$exec = pg_query($select);
	echo pg_num_rows($exec);

?>