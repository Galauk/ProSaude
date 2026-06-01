<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$usu_codigo = $_GET['usu_codigo'];
	$codigo_fam = $_GET['codigo_fam'];
	
	$sqlDel = "delete from integrantes_familia where usu_codigo = $usu_codigo and codigo_fam = $codigo_fam";
	$qry = pg_query($sqlDel);
	
	
?>