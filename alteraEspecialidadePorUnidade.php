<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    $sql = "SELECT 
				esp.esp_codigo, 
				esp.esp_nome 
			FROM 
				especialidade AS esp
			INNER JOIN 
				medico_especialidade AS mes ON mes.esp_codigo=esp.esp_codigo
			INNER JOIN unidade uni
			   ON uni.uni_codigo = mes.uni_codigo
			WHERE 
				(uni.uni_codigo =$uni_codigo)
			and med_codigo = $usr_codigo
			and mes_ativo <> 'I'";
				
	//echo $sql;
	$exec_sql = pg_query($sql);
	
	$option = "";
		if(pg_num_rows($exec_sql) > 1)
			$option .= "<option value=''>--SELECIONE--</option>";
	while($row_dados=pg_fetch_array($exec_sql)) {
		if ($esp_codigo_logado==$row_dados["esp_codigo"]) {
			$option .= "<option value='".$row_dados["esp_codigo"]."' selected>".$row_dados["esp_nome"]."</option>";
		} else {
			$option .= "<option value='".$row_dados["esp_codigo"]."'>".$row_dados["esp_nome"]."</option>";
		}
	}
	
	echo $option;

?>