<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$select = "SELECT distinct(procedimento.proc_codigo), 
					  procedimento.proc_codigo,  
					  TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome
				 FROM grade_exame, 
				 	  procedimento
				WHERE grade_exame.proc_codigo = procedimento.proc_codigo
				  AND grade_exame.med_codigo = '$med_codigo'
				ORDER BY TRANSLATE(proc_nome, 'ZZZ-', '')";
	$sql = pg_query($select);
	while($linha = pg_fetch_array($sql)){
		echo "$linha[1]-$linha[2];";
	}
?>
