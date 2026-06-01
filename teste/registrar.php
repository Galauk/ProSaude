<?php
	
	/**
	 * Faz o agendamento, com o tipo='S' (recepcionado) e item='AL' (agenda livre)
	 * - tbm pode ser usado para cancelar o agendamento
	 */

	require '../global.php';
	require_once COMUM . "/library/php/funcoes.db.php";
	
	$pacientes = $_POST['pacientes'];
	$usr_codigo = $_POST['usr_codigo'];
	$esp_codigo = $_POST['esp_codigo'];
	
	
	
	
	
	

	//die($usu_codigo."-".$usr_codigo."-".$esp_codigo."-".$age_codigo."-".$motivo);
	// cancelar?
	//
//echo "<pre>".print_r($_REQUEST,1);exit;
	if(empty($pacientes) || empty($usr_codigo) || empty($esp_codigo))
		die("Dados necessários ausentes.");

		
		
	
	$query = pg_query("SELECT usu_nome FROM usuario WHERE usu_codigo=$usu_codigo");
	$usu_nome = pg_result($query,0);
	
	$age_data = date("d/m/Y");
	$age_hora = date("H:i");	
	$age_tipo = "ES"; 		// ou CB?
	$uni_codigo = getUnidadeByLogon();
	$age_item = "AL"; 		// agenda livre	
	$age_emergencia = 'N'; 	// ok
	$dt_cadastro = date("Y-m-d H:i:s");
	$usr_codigo_cad = $_SESSION['id_login']; 	// usr que colocou o paciente na fila
	
	// impedir agendamento duplicado
	$select = "Select * from usuario limit $pacientes";
	$q = pg_query($select);
	while ($r = pg_fetch_array($q)){
		$SEL = "SELECT NEXTVAL ('seq_age_codigo') as age_codigo";
		$que = pg_query($SEL);
		$res = pg_fetch_array($que);
		
		if($_POST[escolha] == 'S'){
			$status = 'P';
		}else{
			$status = 'S';
		}
		$age_codigo = $res[age_codigo];
		$sql = "INSERT INTO agendamento(
		age_codigo,
	            			age_data, 
	            			med_codigo, 
	            			age_horario, 
	            			usu_codigo, 
	            			age_tipo, 
	            			age_atendido, 
	            			age_paciente, 
	            			uni_codigo, 
	            			age_item, 
	            			esp_codigo, 
	            			usr_codigo_cad, 
	            			dt_cadastro, 
	            			age_data_atend,  
	            			age_emergencia, 
	            			age_timestamp
	            			)
	    			 VALUES ($age_codigo,
	    			 		'$age_data',
	    			 		$usr_codigo,
	    			 		'$age_hora',
	    			 		$r[usu_codigo],
	    			 		'$age_tipo',
	    			 		'$status',
	    			 		'$r[usu_nome]',
	    			 		$uni_codigo,
	    			 		'$age_item',
	    			 		$esp_codigo,
	    			 		$usr_codigo_cad,
	    			 		'$dt_cadastro',
	    			 		'$dt_cadastro',
	    			 		'$age_emergencia',
	    			 		'$dt_cadastro');";
	    			 		
	     $query = pg_query($sql) or die($sql.pg_last_error());
	//die($sql);
	if($_POST[escolha] == "S"){
		$insertPre = "INSERT 
	 	 INTO pre_consulta(
			age_codigo,
			pc_temperatura,
			pc_peso,
			pc_altura,
			pc_freq_cardiaca,
			pc_freq_respiratoria,
			pc_perimetro_cefalico,
			pc_dados,
			usr_codigo,
			pc_pressao_sistolica,
			pc_pressao_diastolica,
			pc_glicose,
			pc_saturacao,
			pc_clas_risco,
			pc_data,
			esp_codigo)
			VALUES(
			$age_codigo,
			$_POST[temperatura],
			$_POST[peso],
			$_POST[altura],
			$_POST[freq_cardiaca],
			 $_POST[freq_respiratoria],
			 $_POST[p_cefalico],
			 '$_POST[obs]',
			 $usr_codigo_cad,			 	   		 
	    $_POST[pressao_sistolica],
	    $_POST[pressao_diastolica], 
	    $_POST[glicose],
	    $_POST[pc_saturacao],
	    $_POST[pc_clas_risco],
	    'now()',
	    1149)";

	   // DIE($insertPre);
	    $query = pg_query($insertPre) or die($insertPre.pg_last_error());
    
	}
   
	}
    exit("1");
	    
	
