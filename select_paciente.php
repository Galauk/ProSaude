<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

    //Header para evitar cahe
    header("Content-type: text/html; charset=iso-8859-1");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

 $sql = pg_query("select *from usuario where usu_codigo='$_GET[usu_codigo]'");
 $row = pg_fetch_array($sql);

if($t=="p") {
 echo "$row[usu_nome]";
}

if($t=="n") {
 echo "$row[usu_datanasc]";
}

if($t=="m") {
 echo "$row[usu_mae]";
}

if($t=="c") {
 echo "$row[usu_end_cidade]";
}
?>
