<?php

	require_once '../../WebSocialComum/global.php';
	//setError(1);
	$age_codigo = $_GET["age_codigo"];
	$coni_codigo = $_GET["coni_codigo"];
	$id_login = $_GET["id_login"];
	$proc_nome = $_GET["proc_nome"];
	$age_data = $_GET["age_data"];
	
	$sqlSeq = " select nextval('agenda_itens_agei_codigo_seq')";
	$querySeq = pg_query($sqlSeq);
	$reg = pg_fetch_array($querySeq);
	$seq_agei = $reg["nextval"];
	$insert = "insert into agenda_itens(agei_codigo,
										age_codigo,
										coni_codigo,
										agei_data,
										usr_codigo,
										agei_status)
								 VALUES ($seq_agei,
										 $age_codigo,
									     $coni_codigo,
										 '$age_data',
										 $id_login,
										 'R')";
	 
	
	if(pg_query($insert)){
	    echo json_encode(array("age_codigo"=>$age_codigo,
							   "coni_codigo"=>$coni_codigo,
							   "age_data" => date("Y-m-d"),
							   "proc_nome" => $proc_nome,
							   "agei_codigo" => $seq_agei));
	}else{
		echo 2;
	}
?>