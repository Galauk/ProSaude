<?php
	
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$delete = "DELETE FROM itens_movimento WHERE ite_codigo = {$_GET[ite_codigo]}";
	
	$exec_delete = pg_query($delete);
	
	if($exec_delete == true)
	{
		if(pg_affected_rows($exec_delete) > 0)
		{
			echo "deletado";
		} else {
			echo "erro";
		}
	} else {
		echo "erro";
	}
	
?>