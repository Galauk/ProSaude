<?php 
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

if($_REQUEST['io_codigo']!='') {
	$fql = "DELETE from internacao_prescricao where io_codigo = '".$_REQUEST['io_codigo']."'";
	$sql = "DELETE from internacao_observacao where io_codigo = '".$_REQUEST['io_codigo']."'";
}
	pg_query($fql) or die(pg_last_error());		
	pg_query($sql) or die(pg_last_error());		
	$response = array("success" => true); 
echo json_encode($response); 
?>

