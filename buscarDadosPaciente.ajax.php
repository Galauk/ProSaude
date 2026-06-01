<?php
	
	/**
	 * Busca o paciente (usu) por qualquer atributo
	 */

	include_once "global.php";
	
	foreach($_GET as $chave => $valor)
		break;
	
	$query = pg_query("SELECT *,to_char(usu_datanasc,'dd/mm/YYYY') as usu_datanasc FROM usuario WHERE $chave='$valor'");
	
	$out = array();
	while ($r = pg_fetch_assoc($query)){
		$outLinha = array();
		foreach($r as $k => $v){
			$v = str_replace(array("\n","\r"), array("\\n",""), $v);
			$outLinha []= "\"$k\":\"$v\"";
		}
		
		$out []= "{".implode(",",$outLinha)."}";		
	}
	
	header('Content-type: application/json');
	echo "[".implode(",",$out)."]";