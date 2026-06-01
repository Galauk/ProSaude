<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$usr_codigo = $_GET[usr_codigo];

$sqlDelete = "DELETE 
				FROM medico_especialidade 
			   WHERE med_codigo = $usr_codigo";
if($queryDelete = pg_query($sqlDelete)){
	$erro = "";
}else{
	echo "erro ao desvincular";
}
