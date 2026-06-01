<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$med_codigo = $_POST[med_codigo];
	$flag_medico = $_POST[flag_medico];
	
	$select = "SELECT * 
				 FROM movimento
				WHERE mov_data = '{$_POST[mov_data]}'
				  AND mov_tipo = '{$_POST[mov_tipo]}'
				  AND usu_codigo = '{$_POST[usu_codigo]}'
				  AND set_saida = '{$_POST[set_saida]}'
				  AND mov_data_inclusao = CURRENT_DATE
				  AND mov_saida = '{$_POST[mov_saida]}'";

	/*echo $select;
	exit;*/

	$exec_select = pg_query($select);
	$dados = pg_fetch_array($exec_select);
	$mov_codigo = $dados[mov_codigo];
	/*echo "<pre>";
		echo $select;
	echo "</pre>";*/
	//echo pg_last_error($db);
	if(pg_num_rows($exec_select) == 0)
	{
		/*$sql = "insert into movimento
					(mov_data, mov_tipo, usu_codigo, set_saida, usr_codigo, mov_data_inclusao, mov_saida, ate_codigo, mov_num_receita, mov_dt_nota)
					values
					('{$_POST[mov_data]}', '{$_POST[mov_tipo]}', '{$_POST[usu_codigo]}', '{$_POST[set_saida]}', '{$_POST[usr_codigo]}', current_date, '{$_POST[mov_saida]}', ".(empty($_POST[ate_codigo]) ? 'null' : $_POST[ate_codigo]).", '{$_POST[mov_num_receita]}', '{$_POST[mov_data]}')";*/
					
		$sql = "INSERT INTO movimento (mov_data, 
									   mov_tipo, 
									   usu_codigo, 
									   set_saida, 
									   usr_codigo, 
									   mov_data_inclusao, 
									   mov_saida, 
									   age_codigo,
									   ".($flag_medico == 'true' ? 'med_codigo_externo, ' : 'med_codigo_interno, ')." 
									   mov_dt_nota)
								VALUES ('{$_POST[mov_data]}', 
										'{$_POST[mov_tipo]}', 
										'{$_POST[usu_codigo]}', 
										'{$_POST[set_saida]}', 
										'{$_POST[usr_codigo]}', 
										CURRENT_DATE, 
										'{$_POST[mov_saida]}', 
										".(empty($_POST[ate_codigo]) ? 'null' : $_POST[ate_codigo]).",
										'{$med_codigo}',
										'{$_POST[mov_data]}')";
		
		$exec_sql = pg_query($sql);
		
		/*echo "<pre>";
			echo $sql;
		echo "</pre>";*/
		
		//echo pg_last_error($db);
		$select = "SELECT * 
					 FROM movimento
					WHERE mov_data = '{$_POST[mov_data]}'
					  AND mov_tipo = '{$_POST[mov_tipo]}'
					  AND usu_codigo = '{$_POST[usu_codigo]}'
					  AND set_saida = '{$_POST[set_saida]}'
					  AND mov_data_inclusao = CURRENT_DATE
					  AND mov_saida = '{$_POST[mov_saida]}'";
		$exec_select = pg_query($select);
		
		
		/*echo "<pre>";
			echo $select;
		echo "</pre>";*/
		
		$linha = pg_fetch_array($exec_select);
		
		echo $linha[mov_codigo];
		
		$update = "UPDATE movimento 
					  SET mov_nr_nota = $linha[mov_codigo] 
					WHERE mov_codigo = $linha[mov_codigo]";
		
		$exec_update = pg_query($update);
		
		/*echo "<pre>";
			echo $update;
		echo "</pre>";*/
		
		//echo pg_last_error($db);
	}
	else{
		echo $mov_codigo;	
	}
?>