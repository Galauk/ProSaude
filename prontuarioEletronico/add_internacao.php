<?php 
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$age = pg_fetch_array(pg_query("select *from agendamento where age_codigo = '".$_REQUEST['age_codigo']."'"));
	$io = pg_fetch_array(pg_query("select nextval('social.internacao_observacao_io_codigo_seq')+1 as io_codigo"));
	if(empty($_REQUEST['io_codigo'])) {
			pg_query("insert into internacao_observacao (qua_codigo,med_codigo,uni_codigo,age_codigo,io_codigo,io_status,io_observacao,io_data_cadastro) values ('".$_REQUEST['qua_codigo']."','".$age[med_codigo]."','".$age[uni_codigo]."','".$age[age_codigo]."','".$io['io_codigo']."','I','".strtoupper($_REQUEST['io_observacao'])."',NOW())") or die(pg_last_error());
		$iocod = $io[io_codigo];
	} else {
		pg_query("update internacao_observacao set qua_codigo='".$_REQUEST['qua_codigo']."',io_observacao = '".$_REQUEST['io_observacao']."' where io_codigo = '".$_REQUEST['io_codigo']."'") or die(pg_last_error());
		$iocod = $_REQUEST['io_codigo'];
	}
	
	$response = array("io_codigo" => $iocod); 
echo json_encode($response); 
?>

