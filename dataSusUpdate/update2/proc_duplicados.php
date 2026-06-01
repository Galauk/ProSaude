<?php
	include "../../global.php";
	$sqlProcedimento = "SELECT proc_codigo,
							   proc_nome
						  FROM procedimento limit 40";
	$queryProcedimento = pg_query($sqlProcedimento) or die(pg_last_error());
	while($reg = pg_fetch_array($queryProcedimento)){
		$sqlFind = "SELECT * FROM procedimento WHERE proc_nome ilike '%$reg[proc_nome]%'";
		//echo $sqlFind."<br/>";
		$queryFind = pg_query($sqlFind) or die (pg_last_error());
		$numFind = pg_num_rows($queryFind);
		
		if($numFind > 1){
			echo $reg[proc_nome]."<br/>";
		}
		
	}
	