<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$ponteiro = fopen ("rl_procedimento_ocupacao.txt", "r");

$verificaLinhas = "select * from rl_procedimento_ocupacao";
$qryLinhas = pg_query($verificaLinhas);
$numLinhas = pg_num_rows($qryLinhas);
if ($numLinhas == 0){
	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		$co_procedimento = substr($linha,0,10);
		$co_ocupacao = substr($linha,10,6);
		$dt_competencia = substr($linha,16,6);
		
		$stmt = "INSERT INTO rl_procedimento_ocupacao ( 
							 co_procedimento, 
							 co_ocupacao, 
							 dt_competencia
				  ) VALUES ( 
							'$co_procedimento', 
							'$co_ocupacao', 
				   			'$dt_competencia' )";
							
		$qry = pg_query($stmt) or die (pg_last_error());
	}
}else{
	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		$co_procedimento = substr($linha,0,10);
		$co_ocupacao = substr($linha,10,6);
		$dt_competencia = substr($linha,16,6);
		 $stmt = "UPDATE rl_procedimento_ocupacao SET 
						 co_procedimento = '$co_procedimento', 
						 co_ocupacao = '$co_ocupacao', 
						 dt_competencia = '$dt_competencia'
						 WHERE co_procedimento = $co_procedimento";				
		 $qry = pg_query($stmt) or die (pg_last_error());
		 $affected = pg_affected_rows($qryUp);
			if($affected == 0){
				$stmt = "INSERT INTO rl_procedimento_ocupacao ( 
							 co_procedimento, 
							 co_ocupacao, 
							 dt_competencia
				  ) VALUES ( 
							'$co_procedimento', 
							'$co_ocupacao', 
				   			'$dt_competencia' )";
							
				$qry = pg_query($stmt) or die (pg_last_error());
			}
	}
}

?>