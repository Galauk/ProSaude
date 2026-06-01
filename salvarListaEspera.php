<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$usu_codigo = $_GET['usu_codigo'];
	$esp_codigo = $_GET['esp_codigo'];
	$med_codigo = $_GET['med_codigo'];
	$uni_codigo = $_GET['uni_codigo'];
	$id_login   = $_GET['id_login'];
	$agt_codigo = $_GET['agt_codigo'];
	
	$sql = "SELECT *
			  FROM lista_espera
			 WHERE usu_codigo = $usu_codigo
			   AND esp_codigo = $esp_codigo
			   AND lie_data_age IS null";
	$exec_sql = pg_query($sql);
	$linha = pg_fetch_array($exec_sql);
	
	if(pg_num_rows($exec_sql) == 0){
		$sql = "INSERT INTO lista_espera
					(usu_codigo, 
					 med_codigo, 
					 esp_codigo, 
					 uni_codigo, 
					 lie_data_cad, 
					 usr_codigo_cad, 
					 agt_codigo)
				VALUES
					($usu_codigo, 
				  ".(empty($med_codigo) ? 'null' : $med_codigo).", 
					 $esp_codigo, 
					 $uni_codigo, 
					 current_timestamp, 
					 $id_login, 
					 $agt_codigo)";
		$controle = "salvo";
	} else {
		$sql = "UPDATE lista_espera 
				   SET med_codigo = ".(empty($med_codigo) ? 'null' : $med_codigo).",
					   esp_codigo = $esp_codigo,
					   lie_data_alt = current_timestamp,
					   usr_codigo_alt = $id_login,
					   agt_codigo = $agt_codigo
				 WHERE lie_codigo = '$linha[lie_codigo]'";
		$controle = "alterado";
	}
	
	$exec_sql = pg_query($sql);
	
	if($exec_sql){
		if(pg_affected_rows($exec_sql) > 0){
			echo "Registro $controle com sucesso!";
		}
		exit;
	}
	echo "Erro ao ".($controle == "salvo" ? "salvar" : "alterar")." registro!";
	
?>