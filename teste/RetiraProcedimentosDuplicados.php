
<link href="../estilo.css" rel="stylesheet" type="text/css">
		<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
		<link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />

<? include '../global.php';
	$procedimentos = array();	

	$sql = "select distinct proc_codigo from grade_exame";
	$query = pg_query($sql);		
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: grade_exame <br> Codigo do exame:$r[proc_codigo]<br><br>"	;
	}
	
	$sql = "select distinct proc_codigo from procedimento_orientacoes";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: procedimento_orientacoes <br> Codigo do exame:$r[proc_codigo]<br><br>"	;	
	}
	
	$sql = "select distinct proc_codigo from procedimento_observacoes";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: procedimento_observacoes <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	
	$sql = "select distinct proc_codigo from resultadoexame";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: resultadoexame <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
		
	$sql = "select distinct proc_codigo from bpa";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: bpa <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}	
	
	$sql = "select distinct proc_codigo from tipodeexame";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: tipodeexame <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	
	$sql = "select distinct proc_codigo from convenio_itens";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: convenio_itens <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	
	$sql = "select distinct proc_codigo from agendamento_externo";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: agendamento_externo <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	$sql = "select distinct proc_codigo from procedimento_atendimento";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: procedimento_atendimento <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	$sql = "select distinct proc_codigo from exames_sisprenatal";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: exames_sisprenatal <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	
	$sql = "select distinct proc_codigo from evento_usuario_procedimento_aux";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: evento_usuario_procedimento_aux <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	$sql = "select distinct proc_codigo from procedimentos_sisprenatal";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		array_push($procedimentos, $r[proc_codigo]);
		echo "Tabela: procedimentos_sisprenatal <br> Codigo do exame:$r[proc_codigo]<br><br>"	;		
	}
	
	
	$procedimentosUnicos = array_unique($procedimentos);
	//echo "TOTAL:";
	//echo "<pre>".print_r($procedimentosUnicos,1);
	$proc = "";
	foreach ($procedimentosUnicos as $cod => $item){
		//echo $item.",";
		$chapa .= $item.",";
	}
	
	$procs = substr($chapa, 0, -1);
	//echo $procs;
	$sqlDelete = "DELETE FROM procedimento where proc_codigo not in ($procs)";
	$queryDelete = pg_query($sqlDelete) or die(pg_last_error());
	
	$sql="select distinct proc_codigo_sus, (select count(*) from procedimento p2 where p2.proc_codigo_sus = p.proc_codigo_sus ) as qtde from procedimento p where p.proc_codigo in($procs) and (select count(*) from procedimento p2 where p2.proc_codigo_sus = p.proc_codigo_sus ) > 1";
	$query = pg_query($sql);
	while($r = pg_fetch_array($query)){
		
		$selecionaProcedimentoCerto = "select * from procedimento where proc_codigo_sus = '$r[proc_codigo_sus]' order by proc_codigo limit 1";
		$queryProc = pg_query($selecionaProcedimentoCerto);
		$array_proc_codigo = pg_fetch_array($queryProc);
		$proc_codigo = $array_proc_codigo[proc_codigo];
		//echo "<pre>".print_r($proc_codigo,1);
		//echo $proc_codigo;
		$selecionaDuplicado = "select * from procedimento where proc_codigo_sus = '$r[proc_codigo_sus]' and proc_codigo != $proc_codigo order by proc_codigo";
		$queryDup = pg_query($selecionaDuplicado);
		while($rDupli = pg_fetch_array($queryDup)){
			$update = "UPDATE grade_exame set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: grade_exame";
			
			$update = "UPDATE procedimento_orientacoes set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: procedimento_orientacoes";
			
			$update = "UPDATE procedimento_observacoes set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: procedimento_observacoes";
			
			$update = "UPDATE resultadoexame set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: resultadoexame";
			
			$update = "UPDATE bpa set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: bpa";
			
			$update = "UPDATE tipodeexame set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: tipodeexame";
			
			$update = "UPDATE convenio_itens set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: convenio_itens";
			
			$update = "UPDATE agendamento_externo set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: agendamento_externo";
			
			$update = "UPDATE procedimento_atendimento set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: procedimento_atendimento";
			
			$update = "UPDATE exames_sisprenatal set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: exames_sisprenatal";
			
			$update = "UPDATE evento_usuario_procedimento_aux set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: evento_usuario_procedimento_aux";
			
			$update = "UPDATE procedimentos_sisprenatal set proc_codigo = $proc_codigo where proc_codigo = $rDupli[proc_codigo]";
			pg_query($update);
			echo "UPDATE DA TABELA: procedimentos_sisprenatal";
			
			$delete = "DELETE FROM procedimento where proc_codigo = $rDupli[proc_codigo]";
			pg_query($delete);
			
				
		}
		
		
	//echo $r[proc_codigo_sus] . "-" . $r[qtde]."<br>";		
	}
	
	?>

