<?php 
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

if($_REQUEST['inp_codigo']!='') {
	$sql = "DELETE from internacao_prescricao where inp_codigo = '".$_REQUEST['inp_codigo']."'";
}
	pg_query($sql) or die(pg_last_error());		
	$response = array("success" => true); 
echo json_encode($response); 
?>

