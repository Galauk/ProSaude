<?php
include "../global.php";
//include "..";
 //echo SAUDE;
 
 
 $fileName = $_REQUEST["fileName"];
 $caminho = str_replace('\\', '/',SAUDE);
 $abs =  $caminho."raiox/server/php/files/$fileName";
 //echo $abs."<br>";
 //echo $var."<br>";

$sql = "select lo_export(rai_img2,'$abs') from raiox WHERE rai_img_nome = '$fileName'"; 
$query = pg_query($sql)or die($sql.pg_last_error());;

	header("Content-type: image/jpg");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Description: PHP Generated Data");
	
readfile($abs);
//unlink($abs);
?>

