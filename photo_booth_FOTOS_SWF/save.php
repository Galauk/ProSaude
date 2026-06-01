<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
	$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];
	$img = $_REQUEST["img"];
	$usu_codigo = $_REQUEST['usu_codigo'];
	$filename = "images/". mktime(). ".jpg";
	file_put_contents($filename, $jpg);

	$dir = $_SESSION['root'] . $_SESSION['modulo'] . "photo_booth_FOTOS_SWF";
	$file = $dir."/".$filename;
	
	$up = pg_query("update usuario set usu_foto = bytea_import('$file') , usu_foto_nome='".str_replace("images/","",$filename)."' where usu_codigo = '$usu_codigo'");
//	unlink($file);
} else{
	echo "Encoded JPEG information not received.";
}
?>