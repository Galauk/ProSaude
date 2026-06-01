<?php

	require_once 'global.php';
	
	$pc_codigo = $_REQUEST['pc_codigo'];
	$query = pg_query("SELECT * FROM pre_consulta WHERE pc_codigo=$pc_codigo");
	$result = pg_fetch_assoc($query);
	
	// json_encode?
	header('Content-type: application/json');
	
	$out = array();
	foreach($result as $k => $v){
		$v = str_replace(array("\n","\r"), array("\\n",""), $v);
		$out []= "\"$k\":\"$v\"";
	}
	
	echo "{".implode(",",$out)."}";
	
?>