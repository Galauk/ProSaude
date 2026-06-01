<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$codigo_ficha_familia_del = $_GET['codigo_ficha_familia'];	
$tudo = "select * from psf where codigo_ficha_familia = $codigo_ficha_familia";
$qryTudo = pg_query($tudo);
$linhaTudo = pg_fetch_array($qryTudo);
$codigo_familia = $linhaTudo['codigo_fam'];


$sqlValida = "select * from integrantes_familia where codigo_fam = $codigo_familia";
$qry = pg_query($sqlValida);
$numLinhas = pg_num_rows($qry);
//echo $numLinhas;
if($numLinhas == '')
{
	$sqlDel = "delete from psf where codigo_fam = $codigo_familia";
	$qryDel = pg_query($sqlDel);
}else{
	echo "aaaaa";	
}
?>