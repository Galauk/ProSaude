<?php
	$seleciona = "SELECT * FROM USUARIO";
	$qry = pg_query($seleciona);
	while($reg = pg_fetch_array($qry)){
		$upd = "UPDATE 
					   usuario 
				   SET usu_nome = 'ELOTECH PACIENTE.$reg[usu_codigo]',
					   usu_cpf = '999999999',
					   usu_rg = '99999999',
					   usu_cartao_p_sus = '555555555555',
					   usu_cartao_sus = '0000000000',
					   usu_codigo_sus = '00000000000',
					   usu_datanasc = '01/01/2001',
					   usu_mae = 'ELOTECH MAE',
					   usu_pai = 'ELOTECH PAI',
					   dom_codigo = null
				 WHERE usu_codigo = $reg[usu_codigo]";
		if($qryUpd = pg_query($upd)){
			echo "Fail jovem ;D".pg_last_error($qryUpd);			
		}else{
			echo "Deu certo";			
		}
	}
	
	$sqlMedico = "SELECT * FROM usuarios WHERE usr_codigo <> 324";
	$qryMedico = pg_query($sqlMedico);
	while($registros = pg_fetch_array($qryMedico)){
		$updMed = "UPDATE 
						  usuarios 
					  SET usr_nome = 'ELOTECH MEDICO $registros[usr_codigo]',
					  	  usr_num_conselho = '123'
					  	  usr_login = 'teste$registros[usr_codigo]'
					WHERE $registros[usr_codigo]";
		if($qryMed = pg_query($updMed)){
			echo "<font color='#F'>Fail jovem ;D".pg_last_error($qryMed);			
		}else{
			echo "Fuck Yeah";			
		}
	}
	
	$sqlAgendamento = "SELECT * FROM agendamento";
	$qryAgendamento = pg_query($sqlAgendamento);
	while($registrosAg = pg_fetch_array($qryAgendamento)){
		$updAg = "UPDATE 
						  agendamento 
					  SET age_paciente = 'ELOTECH Paciente $registrosAg[age_codigo]'
					WHERE $registrosAg[age_codigo]";
		if($qryAg = pg_query($updAg)){
			echo "<font color='#F'>Fail jovem ;D".pg_last_error($qryMed);			
		}else{
			echo "Deu certo";			
		}
	}
?>