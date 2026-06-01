<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$ponteiro = fopen ("tb_grupo.txt", "r");

	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		
		$co_grupo = substr($linha,0,2);
		$no_grupo = substr($linha,2,100);
		$dt_competencia = substr($linha,102,6);
		
		$stmt = "UPDATE tb_grupo SET 
						co_grupo = '$co_grupo', 
						no_grupo = '$no_grupo',
						dt_competencia = '$dt_competencia'
						WHERE no_grupo = '$no_grupo'" ;
		
	    $qry = pg_query($stmt) or die (pg_last_error());
		$affected = pg_affected_rows($qry);
		if($affected == 0){
			$inserir = "INSERT INTO tb_grupo ( 
									co_grupo, 
									no_grupo,
									dt_competencia
     					 ) VALUES ( 
									'$co_grupo', 
									'$no_grupo',
									'$dt_competencia' ) ";

			$qryInseri = pg_query($inserir) or die (pg_last_error());
		}

	}

echo "
	  <script>
		alert(chegou aki fechou a cacheta!)
	  </script>";

fclose ($ponteiro);



?>