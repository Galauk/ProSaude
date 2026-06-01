<?php 
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$inp = pg_fetch_array(pg_query("select nextval('social.internacao_prescricao_inp_codigo_seq')+1 as inp_codigo"));

	pg_query("INSERT INTO social.internacao_prescricao(inp_codigo,
            data,age_codigo,io_codigo, pro_codigo, inp_qtde_dose, inp_velocidade, 
            adm_codigo, frq_codigo, inp_hrini, inp_observacao)
			VALUES ('".$inp['inp_codigo']."',NOW(),'".$_REQUEST['age_codigo']."','".$_REQUEST['io_codigo']."', '".$_REQUEST['pro_codigo']."', '".$_REQUEST['inp_qtde_dose']."', '".$_REQUEST['inp_velocidade']."', 
            '".$_REQUEST['adm_codigo']."', '".$_REQUEST['frq_codigo']."', '".$_REQUEST['inp_hrini']."', '".$_REQUEST['inp_observacao']."')") or die(pg_last_error());
			
	$response = array("inp_codigo" => $inp['inp_codigo']); 
echo json_encode($response); 			
			
?>

