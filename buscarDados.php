<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$select = "SELECT DISTINCT TO_CHAR(grm_periodo,'DD/MM/YYYY') as grm_periodo, 
				  grm_periodo as grm_periodo2 
			 FROM grade_mensal 
			WHERE med_codigo='$med_codigo' 
			  AND esp_codigo='$esp_codigo' 
			  AND age_item='$age_item' 
			ORDER BY grm_periodo2 DESC
			LIMIT 12";

//echo $select;

$exec_select = pg_query($select);

while($linha = pg_fetch_array($exec_select))
{
	$select2 = "SELECT DISTINCT grm_periodo 
				  FROM grade_mensal 
				 WHERE esp_codigo = '$esp_codigo' 
				   AND med_codigo = '$med_codigo'
				   AND age_item = '$age_item' 
				   AND grm_periodo='$linha[grm_periodo]' 
				 ORDER BY grm_periodo DESC
				 LIMIT 12";
			
	$sql = pg_query( $select2 );
	
	while ($row = pg_fetch_row($sql))
	{
		/*$select = "select (('$row[0]'::date + interval '1 month') - interval '1 day')::date";
		$exec = pg_query($select);
		$periodo = pg_fetch_array($exec);
		$per = $periodo[0];*/
		//echo $select."#";
		$tmp = mktime("12", "0", "0", substr($row[0], 5, 2), substr($row[0], 8, 2), substr($row[0], 0, 4));
		$per = date("d/m/Y", $tmp + (date("t", $tmp) - 1) * 86400);
		$periodo[date("Y-m-d", $tmp)] = $per;
		
	}
	
	echo "$linha[0]-$per;";
}

