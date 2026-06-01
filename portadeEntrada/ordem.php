<?php
	
	/**
	 * Altera a ordem de atendimento da porta de entrada
	 */
	include_once '../global.php';
	
	$ordem = $_POST['ordem'];
	//echo "<pre>".print_r($ordem,1);
	$sql = 'UPDATE agendamento SET age_ordem=%d WHERE age_codigo=%d;';
	$menor = count($ordem) * -1;
	foreach($ordem as $age_codigo){
		$query = pg_query(sprintf($sql,$menor,$age_codigo));
		$menor++;
	}
	
	echo 1;