<?php	

	/**
	 * Este arquivo analiza as inconsistencias de todos os itens de um BPA,
	 * filtrando por unidade e mes de referência.
	 */

	require_once '../global.php';
	require_once 'bpa_functions.php';
	setError(1);
	
	$uni_get = $_GET['uni_codigo'];
	$unidade = explode("|",$uni_get);
	
	if($unidade[1] == 1){
		$uni_codigo = $unidade[0];
		$med_codigo = null;
		$where = "AND uni_codigo=$uni_codigo";
	}else if($unidade[1] == 0){
		$uni_codigo = null;
		$med_codigo = $unidade[0];
		$where = "AND bpa.med_codigo=$med_codigo";
	}
	
 	$data_inicial = $_GET['data_inicial'];
 	$data_final = $_GET['data_final'];
	list($mes,$ano) = explode("/",$_GET['mes_ref']);
		
	$sql   = "  SELECT bpa.* ,
				       CASE WHEN rl.co_registro = 2 THEN 'I'
				       ELSE 'C'
				       END AS bpa_tipo
				  FROM bpa 
				  JOIN procedimento proc
				    ON proc.proc_codigo=bpa.proc_codigo
				  JOIN rl_procedimento_registro AS rl
				    ON rl.co_procedimento=proc.proc_codigo_sus    
				   AND rl.co_registro IN (1,2) 
	             WHERE bpa_data BETWEEN '$data_inicial' AND '$data_final'
	              $where
	               AND bpa_ativo='t'
	             ORDER BY bpa_data, bpa_codigo;";

	$query = pg_query($sql);
	//die($sql);
	// limpar todas as inconsistencias já existentes
	$sub_sql = "UPDATE bpa 
	               SET bpa_status_inconsistencia = NULL 
	             WHERE bpa_data BETWEEN '$data_inicial' AND '$data_final'
	               $where
	               AND bpa_ativo='t';";
	$sub_query = pg_query($sub_sql);
	fdebug("Atualizou os BPA para inconsistenias: NULL - ".pg_affected_rows($sub_query));
	
	$sub_sql = "DELETE FROM rl_bpa_inconsistencia 
	             WHERE bpa_codigo IN (
					  SELECT bpa_codigo 
			            FROM bpa 
			           WHERE bpa_data BETWEEN '$data_inicial' AND '$data_final'
	               	    $where
	               		 AND bpa_ativo='t'
	             );";
	$sub_query = pg_query($sub_sql);
	fdebug("Deletou todas as inconsistencias: ".pg_affected_rows($sub_query));
	
	// para cada registro do bpa selecionado
	while($r = pg_fetch_array($query)){
	
		$erros = 0;
		validacoes($r);
		
		//echo $erros."<br/>";
		// Fim das validações
		// Houve erros?
		$status_inconsistencia = $erros?"t":"f";
		$sub_sql = "UPDATE bpa SET bpa_status_inconsistencia='$status_inconsistencia' WHERE bpa_codigo={$r['bpa_codigo']};";
		//fdebug($sub_sql);
		pg_query($sub_sql);
	}
	
	// envia para o grid que lista as inconsistências:
	header("location: bpa2.php?uni_codigo={$uni_get}&mes_ref={$_GET['mes_ref']}&data_inicial={$_GET['data_inicial']}&data_final={$_GET['data_final']}");
	//echo "<a href=\"bpa2.php?uni_codigo={$_GET['uni_codigo']}&mes_ref={$_GET['mes_ref']}&data_inicial={$_GET['data_inicial']}&data_final={$_GET['data_final']}\">continuar</a>";
	exit;
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	