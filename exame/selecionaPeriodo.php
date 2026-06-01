<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$h_med_codigo = $_GET['med_codigo'];

$sql = "select to_char(gex_periodo, 'DD/MM/YYYY') as gex_period,gex_codigo from grade_exame_mensal where med_codigo = $h_med_codigo order by gex_periodo desc";
$qry = pg_query($sql);
$result = 0;
echo"<option>::.. Periodo ..::</option>";
while($result = pg_fetch_array($qry)){
	echo"<option value='$result[gex_codigo]'>$result[gex_period]</option>";	
}
?>