<?php
	
	require_once 'global.php';
	header('Content-Type: text/html; charset=ISO-8859-1');

	
	$procedimento = $_GET['procedimento'];

	$sql = " SELECT c.cd10_codigo,
	                c.cd10_codigo_cid || ' - ' || c.cd10_descricao AS cd10_descricao
			   FROM cid10 AS c
			   JOIN rl_procedimento_cid AS rl
			     ON rl.co_cid=c.cd10_codigo_cid
			   JOIN procedimento AS p
			     ON p.proc_codigo_sus=rl.co_procedimento
			    AND p.proc_codigo=$procedimento
			  ORDER BY c.cd10_descricao";
	
	$query = pg_query($sql);
	
	while($r = pg_fetch_array($query))
		printf("<option value=\"%s\">%s</option>", $r['cd10_codigo'], $r['cd10_descricao']);	
	