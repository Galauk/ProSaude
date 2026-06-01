<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$ponteiro = fopen ("update/rl_procedimento_cid.txt", "r");

	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		if (strlen($linha) > 10){ //linha válida nunca será menor que 10 (caso tenha lixo na linha, para não dar erro foi feita essa verificação
			$co_procedimento = substr($linha,0,10);
			$co_cid = substr($linha,10,4);
			$st_principal = substr($linha,14,1);
			$dt_competencia = substr($linha,15,6);
			
			$stmt = "UPDATE rl_procedimento_cid SET 
							co_procedimento = $co_procedimento, 
							co_cid = '$co_cid', 
							st_principal = '$st_principal', 
							dt_competencia = '$dt_competencia'
					  WHERE co_procedimento = '$co_procedimento'
					    AND co_cid = '$co_cid'" ;
		    $qry = pg_query($stmt) or die (pg_last_error());
			$affected = pg_affected_rows($qry);
			if($affected == 0){
				$inserir = "INSERT 
							  INTO rl_procedimento_cid (co_procedimento,
							   							co_cid,
							   							st_principal,
							   							dt_competencia
								 			  ) VALUES ('$co_procedimento', 
														'$co_cid', 
														'$st_principal', 
														'$dt_competencia'
													   )";
				$qryInseri = pg_query($inserir);
			}
	
		}
	}
	echo "<script>
			alert('Os dados foram atualizados com sucesso!');
			window.location = 'importacaoSigtap.php';
	  </script>";
	
	fclose ($ponteiro);

?>