<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$ponteiro = fopen ("tb_ocupacao.txt", "r");

	while (!feof ($ponteiro)) {
			
		$linha = fgets($ponteiro, 4096);
		if (strlen($linha) > 10){ //linha válida nunca será menor que 10 (caso tenha lixo na linha, para não dar erro foi feita essa verificação
			$co_ocupacao = substr($linha,0,6);
			$no_ocupacao = trim(substr($linha,6,150));
			
			$stmt = "UPDATE tb_ocupacao SET 
							co_ocupacao = '$co_ocupacao', 
							no_ocupacao = '$no_ocupacao'
					  WHERE no_ocupacao = '$no_ocupacao'" ;
		    $qry = pg_query($stmt) or die (pg_last_error());
			$affected = pg_affected_rows($qry);
			echo $stmt."<br/>";
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

	}


fclose ($ponteiro);



?>