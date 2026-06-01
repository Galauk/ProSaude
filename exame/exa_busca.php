<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	if ($_GET) {
		$cod = trim($_GET["cod"]);
	} else {
		echo "Requisição inválida";
		exit;
	}
	$uni_codigo = $_GET["uni_codigo"];
	$sql = "SELECT distinct(g.proc_codigo),
				   TRANSLATE(p.proc_nome, 'ZZZ-', '') as newprocnome 
		      FROM grade_exame AS g 
		      LEFT join procedimento AS p 
		        ON p.proc_codigo = g.proc_codigo 
		      JOIN grade_exame_unidade as geu
		        ON geu.proc_codigo = p.proc_codigo
		     WHERE g.med_codigo = '$cod' 
		       AND g.graex_data >= CURRENT_DATE
		       AND geu.uni_codigo = $uni_codigo
		     ORDER BY TRANSLATE(p.proc_nome, 'ZZZ-', '')";

	$query = pg_query($sql);
	while($row = pg_fetch_array($query)) {
		echo "$row[proc_codigo]-$row[newprocnome];";
	}
?>