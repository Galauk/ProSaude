<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$area_fam = $_GET['area_fam'];
$sql = "select * from microarea where area_codigo = $area_fam";
$query = pg_query($sql);
while($linha = pg_fetch_array($query))
{
	echo "$linha[1]-$linha[2];";
}
?>