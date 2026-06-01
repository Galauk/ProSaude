<?php
	
	/**
	 * Faz o agendamento, com o tipo='S' (recepcionado) e item='AL' (agenda livre)
	 * - tbm pode ser usado para cancelar o agendamento
	 */

	require '../global.php';
	require_once COMUM . "/library/php/funcoes.db.php";
	
	$usu_codigo = $_POST['usu_codigo'];
	$usr_codigo = $_POST['usr_codigo'];
	$esp_codigo = $_POST['esp_codigo'];
	$age_codigo = $_POST['age_codigo']; // p/ cancelar
	$motivo = $_POST['motivo'];			// p/ cancelar
	//die($usu_codigo."-".$usr_codigo."-".$esp_codigo."-".$age_codigo."-".$motivo);
	// cancelar?
	
	if(!empty($age_codigo) && !empty($motivo)){
		$retorno = array('success'=>false);
		if($motivo == "C"){ // cancelamento simples
			$sql = "DELETE FROM agendamento WHERE age_codigo=$age_codigo";
		}
		if($motivo == "P"){ // paciente ausentou-se
			$sql = "UPDATE agendamento SET age_atendido='F' WHERE age_codigo=$age_codigo"; // F-Faltoso			
		}
		if($motivo == "M"){ // falta médica
			$sql = "UPDATE agendamento SET age_atendido='M' WHERE age_codigo=$age_codigo";
		}
		pg_query("DELETE FROM chamada WHERE age_codigo = '$age_codigo'");
		if(pg_query($sql)){
			$retorno['success'] = true;
		}
		die(json_encode($retorno)); 
	}
	
	if(empty($usu_codigo) || empty($usr_codigo) || empty($esp_codigo))
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
	if($age_codigo == ""){
		$sql = "SELECT age_codigo 
		          FROM agendamento
		         WHERE usu_codigo=$usu_codigo
		           AND age_data='$age_data'
		           AND age_atendido IN ('S','N','P','E')
		           AND age_tipo = 'ES'
				   AND uni_codigo = $uni_codigo;"; // 'S-Recepcionado; A-Atendido; N-Agendado; T-Transferido; F-Faltoso; P-Pré-consulta; E-Em Atendimento'
		$query = pg_query($sql);
		
		if(pg_num_rows($query))
			exit("0"); // há um agendamento para hoje* 
		
		$next_age_codigo = "select nextval('seq_age_codigo') as age_codigo";
		$query_age_codigo = pg_query($next_age_codigo);
		$reg_next_age = pg_fetch_array($query_age_codigo);
		
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
	            			age_timestamp,
							tat_codigo)
	    			 VALUES ($reg_next_age[age_codigo],
	    			 		'$age_data',
	    			 		$usr_codigo,
	    			 		'$age_hora',
	    			 		$usu_codigo,
	    			 		'$age_tipo',
	    			 		'S', -- recepcionado
	    			 		'$usu_nome',
	    			 		$uni_codigo,
	    			 		'$age_item',
	    			 		$esp_codigo,
	    			 		$usr_codigo_cad,
	    			 		'$dt_cadastro',
	    			 		'$dt_cadastro',
	    			 		'$age_emergencia',
	    			 		'$dt_cadastro',
							5);";
	    			 		
	    			 		$age_codigo = $reg_next_age[age_codigo];
	    			 		
	    
	}else{
		$sql = "UPDATE agendamento 
				   SET esp_codigo = $esp_codigo,
				   	   usu_codigo=$usu_codigo,
				   	   med_codigo=$usr_codigo 
		   	     WHERE age_codigo = $age_codigo";
	}
    $query = pg_query($sql) or die($sql.pg_last_error());
    exit($usr_codigo."-".$esp_codigo."-".$age_codigo);
	    
	
