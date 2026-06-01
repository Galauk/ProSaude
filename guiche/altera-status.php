<?

 include_once("db.inc.painel.php");

$sql = pg_query("UPDATE chamada SET cha_status = 'F' WHERE age_codigo = ".$_GET['age_codigo']) or die(pg_last_error());

print_r(pg_fetch_object($sql));

exit;