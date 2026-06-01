<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$sql = pg_query("SELECT * FROM usuario WHERE usu_codigo = ".$_REQUEST['usu_codigo']."");

$vet=pg_fetch_array($sql);
$arq_nome=$vet["usu_foto_nome"];

$dd = $_SESSION['root'] . $_SESSION['modulo'] . "photo_booth_FOTOS_SWF/";
$dir = $dd."Temp/";
$file = $dir.$arq_nome;
die("select lo_export(usu_foto, '".$dir.$arq_nome."') from usuario WHERE usu_codigo = ".$_REQUEST['usu_codigo']."");
$sql = pg_query("select lo_export(usu_foto, '".$dir.$arq_nome."') from usuario WHERE usu_codigo = ".$_REQUEST['usu_codigo']."") or die(pg_last_error());

	header("Content-type: image/jpg");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Description: PHP Generated Data");
	
readfile($dir.$arq_nome);
unlink($dir.$arq_nome);

?>
