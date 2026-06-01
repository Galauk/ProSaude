<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

 $ex = explode("/",$data);
 $newdate = $ex[2]."-".$ex[1]."-".$ex[0];
 $sql = pg_query("select *from feriado where fer_data = '$newdate'");

$res=pg_fetch_array($sql);
$linhas=pg_num_rows($sql);

if (date("w", mktime("0", "0", "0", $ex[1], $ex[0], $ex[2])) == "6") {
 echo "0"."|"."Sabado";
exit;
} 
if (date("w", mktime("0", "0", "0", $ex[1], $ex[0], $ex[2])) == "0") {
 echo "1"."|"."Domingo";
exit;
} 
if($linhas>="1") {
 echo "2"."|"."Feriado: ".$res[fer_nome];
exit;
} 
if($linhas<="0") {
 echo "<div id='horario'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazer_agenda_on.jpg align=absmiddle> $dia_da_semana</div>";
exit;
}
?> 
