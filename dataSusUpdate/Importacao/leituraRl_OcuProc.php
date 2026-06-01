<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$ponteiro = fopen ("rl_procedimento_ocupacao.txt", "r");

	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		$co_procedimento = substr($linha,0,10);
		$co_ocupacao = substr($linha,10,6);
		$dt_competencia = "sub";
		
		$stmt = "UPDATE rl_procedimento_ocupacao SET 
						co_procedimento = '$co_procedimento', 
						co_ocupacao = '$co_ocupacao',
						dt_competencia = '$dt_competencia'
						WHERE co_ocupacao = '$co_ocupacao'" ;
	    $qry = pg_query($stmt) or die (pg_last_error());
		$affected = pg_affected_rows($qry);
		if($affected == 0){
			$inserir = "INSERT INTO tb_ocupacao ( 
									co_ocupacao, 
									no_ocupacao
     					 ) VALUES ( 
									'$co_ocupacao', 
									'$no_ocupacao' ) ";
			$qryInseri = pg_query($inserir) or die (pg_last_error());
		}

	}

echo "
	  <script>
		window.location = 'procedimentoTabelaSistema.php';
	  </script>";

fclose ($ponteiro);



?>