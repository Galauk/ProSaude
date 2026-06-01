<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$select_produto = "SELECT pro_codigo
						 FROM programa_produto
						WHERE pro_codigo = {$_GET[pro_codigo]}";
				
	$exec_select_produto = pg_query($select_produto);
	//select para verificar se o produto esta em algum programa
	if(pg_num_rows($exec_select_produto) > 0)
	{
		
		$select_programa_usuario = "select a.prgp_codigo, b.prgp_codigo, b.pro_codigo
														from cota_paciente a, programa_produto b
														where b.pro_codigo = {$_GET[pro_codigo]}
														and a.prgp_codigo = b.prgp_codigo
														and usu_codigo = {$_GET[usu_codigo]}";
		
		$exec_select_programa_usuario = pg_query($select_programa_usuario);
		//select para verificar se o paciente e o produto está no mesmo programa
		if(pg_num_rows($exec_select_programa_usuario) > 0)
		{
			
			$select = "select b.ctp_quantidade, d2.ite_quantidade, 
							case
								when (b.ctp_periodo = 'SEMANAL') then (current_date - 7)
								when (b.ctp_periodo = 'MENSAL') then (current_date - 30)
								when (b.ctp_periodo = 'BIMESTRAL') then (current_date - 60)
								when (b.ctp_periodo = 'TRIMESTRAL') then (current_date - 90)
								when (b.ctp_periodo = 'SEMESTRAL') then (current_date - 180)
								when (b.ctp_periodo = 'ANUAL') then (current_date - 365)
							end as data
							from
							programa_produto a, cota_paciente b, produto c,
							(
								select distinct max(a.mov_data) as data, b.ite_quantidade
								from movimento a, itens_movimento b
								where a.mov_codigo = b.mov_codigo
								and a.mov_data >= (
								select
								case
									when (b.ctp_periodo = 'SEMANAL') then (current_date - 7)
									when (b.ctp_periodo = 'MENSAL') then (current_date - 30)
									when (b.ctp_periodo = 'BIMESTRAL') then (current_date - 60)
									when (b.ctp_periodo = 'TRIMESTRAL') then (current_date - 90)
									when (b.ctp_periodo = 'SEMESTRAL') then (current_date - 180)
									when (b.ctp_periodo = 'ANUAL') then (current_date - 365)
								end as data
								from
								programa_produto a, cota_paciente b, produto c
								where a.prgp_codigo = b.prgp_codigo
								and a.pro_codigo = c.pro_codigo
								and b.usu_codigo = {$_GET[usu_codigo]}
								and a.pro_codigo = {$_GET[pro_codigo]}
							) and a.mov_data <= current_date
								and a.usu_codigo = {$_GET[usu_codigo]}
								and b.pro_codigo = {$_GET[pro_codigo]}
								group by ite_quantidade
							) as d2
							where a.prgp_codigo = b.prgp_codigo
							and a.pro_codigo = c.pro_codigo
							and b.usu_codigo = {$_GET[usu_codigo]}
							and a.pro_codigo = {$_GET[pro_codigo]}
							and d2.data between (data) and (current_date)";
							
			$exec_select = pg_query($select);
			
			if(pg_num_rows($exec_select) > 0)
			{
				$linha = pg_fetch_array($exec_select);
				
				echo $linha[0]."#".$linha[1];
			} else {
				$select = "select a.ctp_quantidade, a.ctp_periodo, b.pro_nome, c.prg_nome
					from cota_paciente a, produto b, programa_atendimento c,
					programa_produto d
					where d.pro_codigo = {$_GET[pro_codigo]}
					and d.prg_codigo = c.prg_codigo
					and a.prgp_codigo = d.prgp_codigo
					and usu_codigo = {$_GET[usu_codigo]}";
				$exec_select = pg_query($select);
				
				if(pg_num_rows($exec_select) > 0)
				{
					$linha = pg_fetch_array($exec_select);
					echo $linha[0]."#";
				} else {
					echo "c";
				}
			}
			
		} else {
			//echo "Paciente nao faz parte de nenhum programa";
			echo "b";
		}
		
	} else {
		//echo "Produto nao faz parte de nenhum programa";
		echo "a";
	}
	
?>