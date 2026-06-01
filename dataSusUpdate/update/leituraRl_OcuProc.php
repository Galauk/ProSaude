<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$ponteiro = fopen ("rl_procedimento_ocupacao.txt", "r");

	$cont = 0;
	while (!feof ($ponteiro)) {
		$cont++;
		$linha = fgets($ponteiro, 4096);
		if (strlen($linha) > 10){ //linha válida nunca será menor que 10 (caso tenha lixo na linha, para não dar erro foi feita essa verificação
			$co_procedimento = substr($linha,0,10);
			$co_ocupacao = substr($linha,10,6);
			$dt_competencia = substr($linha,16,6);;
			
			$stmt = "UPDATE rl_procedimento_ocupacao 
						SET co_procedimento = '$co_procedimento', 
							co_ocupacao = '$co_ocupacao',
							dt_competencia = '$dt_competencia'
					  WHERE co_ocupacao = '$co_ocupacao'
					    AND co_procedimento = '$co_procedimento'" ;
		    $qry = pg_query($stmt) or die (pg_last_error());
			$affected = pg_affected_rows($qry);

			if($affected == 0){
				$inserir = "INSERT INTO rl_procedimento_ocupacao ( 
										co_ocupacao,
										co_procedimento, 
										dt_competencia
	     					 ) VALUES ( 
										'$co_ocupacao', 
										'$co_procedimento', 
										'$dt_competencia' ) ";
				$qryInseri = pg_query($inserir) or die (pg_last_error());
			}
			//echo $qry;
		}
	}
	
	echo "
	  <script>
		window.location = 'leituraProcedimento.php';
	  </script>";

fclose ($ponteiro);



?>