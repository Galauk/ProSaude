<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$ponteiro = fopen ("tb_procedimento.txt", "r");
$verificaLinhas = "select * from tb_procedimento";
$qryLinhas = pg_query($verificaLinhas);
$numLinhas = pg_num_rows($qryLinhas);
if ($numLinhas == 0){
	while (!feof ($ponteiro)) {
	  $linha = fgets($ponteiro, 4096);
	  $co_procedimento = substr($linha, 0, 10);  
	  $no_procedimento = substr($linha, 10, 250);
	  $tp_complexibilidade = substr($linha, 260, 1);
	  $tp_sexo = substr($linha,261,1);
	  $qt_maxima_execucao = substr($linha,262,4);
	  $qt_dias_permanencia =  substr($linha,266,4);
	  $qt_pontos = substr($linha,270,4);
	  $vl_idade_minima = substr($linha,274,4);
	  $vl_idade_maxima = substr($linha,278,4);
	  $vl_sh = substr($linha,282,10);
	  $vl_sa = substr($linha,292,10);
	  $vl_sp = substr($linha,302,10);
	  $co_financiamento = substr($linha,312,2);
	  $co_rubrica = substr($linha,314,6);
	  $dt_competencia = substr($linha,320,6);
	  
	   $stmt = "INSERT INTO tb_procedimento ( 
							co_procedimento, 
							no_procedimento, 
							tp_complexibilidade, 
							tp_sexo, 
							qt_maxima_execucao, 
							qt_dias_permanencia, 
							qt_pontos, 
							vl_idade_minima, 
							vl_idade_maxima, 
							vl_sh, 
							vl_sa, 
							vl_sp, 
							co_financiamento, 
							co_rubrica, 
							dt_competencia
				 ) VALUES ( 
							'$co_procedimento', 
							'$no_procedimento', 
							'$tp_complexibilidade', 
							'$tp_sexo', 
							".floatval($qt_maxima_execucao).", 
							".floatval($qt_dias_permanencia).", 
							".floatval($qt_pontos).", 
							".floatval($vl_idade_minima).", 
							".floatval($vl_idade_maxima).", 
							".floatval($vl_sh).", 
							".floatval($vl_sa).", 
							".floatval($vl_sp).", 
							'$co_financiamento', 
							'$co_rubrica', 
							'$dt_competencia')";
	    $qry = pg_query($stmt) or die (pg_last_error());
	  
	}
echo "<script>
			alert('Os Dados Foram Importados Com Sucesso!');
	  </script>";
}else{
	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro);
		if (strlen($linha) > 10){ //linha válida nunca será menor que 10 (caso tenha lixo na linha, para não dar erro foi feita essa verificação
			$co_procedimento = substr($linha, 0, 10);  
			$no_procedimento = substr($linha, 10, 250);
			$tp_complexibilidade = substr($linha, 260, 1);
			$tp_sexo = substr($linha,261,1);
			$qt_maxima_execucao = substr($linha,262,4);
			$qt_dias_permanencia =  substr($linha,266,4);
			$qt_pontos = substr($linha,270,4);
			$vl_idade_minima = substr($linha,274,4);
			$vl_idade_maxima = substr($linha,278,4);
			$vl_sh = substr($linha,282,10);
			$vl_sa = substr($linha,292,10);
			$vl_sp = substr($linha,302,10);
			$co_financiamento = substr($linha,312,2);
			$co_rubrica = substr($linha,314,6);
			$dt_competencia = substr($linha,320,6);
		  
			$stmtUp = "UPDATE tb_procedimento SET 
							  co_procedimento = '$co_procedimento', 
							  no_procedimento = '$no_procedimento', 
							  tp_complexibilidade = '$tp_complexibilidade', 
							  tp_sexo = '$tp_sexo', 
							  qt_maxima_execucao = '$qt_maxima_execucao', 
							  qt_dias_permanencia = '$qt_dias_permanencia', 
							  qt_pontos = '$qt_pontos', 
							  vl_idade_minima = $vl_idade_minima, 
							  vl_idade_maxima = $vl_idade_maxima, 
							  vl_sh = $vl_sh, 
							  vl_sa = $vl_sa, 
							  vl_sp = $vl_sp, 
							  co_financiamento = '$co_financiamento', 
							  co_rubrica = '$co_rubrica', 
							  dt_competencia = '$dt_competencia'
						WHERE no_procedimento = '$no_procedimento'";
			$qryUp = pg_query($stmtUp) or die ($stmtUp);
			$affected = pg_affected_rows($qryUp);
			if($affected == 0){
				$stmt = "INSERT INTO tb_procedimento ( 
							co_procedimento, 
							no_procedimento, 
							tp_complexibilidade, 
							tp_sexo, 
							qt_maxima_execucao, 
							qt_dias_permanencia, 
							qt_pontos, 
							vl_idade_minima, 
							vl_idade_maxima, 
							vl_sh, 
							vl_sa, 
							vl_sp, 
							co_financiamento, 
							co_rubrica, 
							dt_competencia
				 ) VALUES ( 
							'$co_procedimento', 
							'$no_procedimento', 
							'$tp_complexibilidade', 
							'$tp_sexo', 
							".floatval($qt_maxima_execucao).", 
							".floatval($qt_dias_permanencia).", 
							".floatval($qt_pontos).", 
							".floatval($vl_idade_minima).", 
							".floatval($vl_idade_maxima).", 
							".floatval($vl_sh).", 
							".floatval($vl_sa).", 
							".floatval($vl_sp).", 
							'$co_financiamento', 
							'$co_rubrica', 
							'$dt_competencia')";
	   		 $qry = pg_query($stmt) or die (pg_last_error());	
			}
		}
	}
	echo "<script>
			alert('Os Dados Foram Alterados Com Sucesso!');
	  </script>";
}
fclose ($ponteiro);
?> 