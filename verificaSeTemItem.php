<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$select = "select * from itens_movimento where mov_codigo=$_GET[mov_codigo]";
	$query = pg_query($select);
	if(pg_num_rows($query)> 0){
		$cont = 1;
	}else{
		$cont = 0;
	}
 
	echo $cont;
?>