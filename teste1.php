<?php
  session_start();
        require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$s= "select * from produto";
$q = pg_query($s);
//echo $s;
while($r=pg_fetch_array($q)){
 $i = "insert into produto_setor (set_codigo, pro_codigo)VALUES(11,$r[pro_codigo])";
echo $i.";<br>";
// $ia = pg_query($i);
}
?>
