<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$ponteiro = fopen ("tb_procedimento.txt", "r");

	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro);
		if (strlen($linha) > 10){ //linha válida nunca será menor que 10 (caso tenha lixo na linha, para não dar erro foi feita essa verificação
			$co_procedimento = substr($linha, 0, 10);  
			$no_procedimento = substr($linha, 10, 250);
			$tp_complexidade = substr($linha, 260, 1);
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
		  
			$stmtUp = "UPDATE procedimento SET 
							  proc_codigo_sus = '$co_procedimento', 
							  proc_nome = '$no_procedimento', 
							  proc_complexidade = '$tp_complexidade', 
							  proc_sexo_novo = '$tp_sexo', 
							  proc_qtdemaxima = '$qt_maxima_execucao', 
							  proc_qtdedias = '$qt_dias_permanencia', 
							  proc_qtdepontos = '$qt_pontos', 
							  proc_idade_minima = $vl_idade_minima, 
							  proc_idade_maxima = $vl_idade_maxima, 
							  proc_vlsh = $vl_sh, 
							  proc_vlsa = $vl_sa, 
							  proc_vlsp = $vl_sp, 
							  proc_cofinan = '$co_financiamento', 
							  proc_corubrica = '$co_rubrica', 
							  proc_dtcompetencia = '$dt_competencia'
						WHERE proc_nome = '$no_procedimento'";
			//$qryUp = pg_query($stmtUp) or die (pg_last_error());
			echo $stmtUp."<br/>";
										

			$affected = pg_affected_rows($qryUp);
			if($affected == 0){
				$stmt = "INSERT INTO procedimento ( 
							proc_codigo_sus, 
							proc_nome, 
							proc_complexidade, 
							proc_sexo_novo, 
							proc_qtdemaxima, 
							proc_qtdedias, 
							proc_qtdepontos, 
							proc_idade_minima, 
							proc_idade_maxima, 
							proc_vlsh, 
							proc_vlsa, 
							proc_vlsp, 
							proc_cofinan, 
							proc_corubrica, 
							proc_dtcompetencia
				 ) VALUES ( 
							'$co_procedimento', 
							'$no_procedimento', 
							'$tp_complexidade', 
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
			//echo $stmt;					
	   		$qry = pg_query($stmt) or die (pg_last_error());	
			 echo $stmt."<br/>";
			}

		}
}

echo "
	  <script>
		window.location = '../leituraOcupacao.php';
	  </script>";
fclose ($ponteiro);
?> 