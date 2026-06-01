<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	/*echo "<pre>";
		print_r($_REQUEST);
	echo "</pre>";*/
	$sql = "SELECT to_char(b.gra_data,'DD/MM/YYYY') as gra_data, 
				   b.gra_hora_ini,
				   coalesce((SELECT a.qtde 
				   			   FROM view_qtde_grade as a 
				   			  WHERE a.med_codigo = $med_codigo 
				   			    AND a.uni_codigo = $uni_codigo 
				   			    AND a.esp_codigo = $esp_codigo 
				   			    AND a.gra_data >= b.gra_data 
				   			    AND a.gra_hora_ini = b.gra_hora_ini 
				   			  ORDER BY gra_data limit 1),0) -
				   coalesce((SELECT qtde 
				   			   FROM view_qtde_medico as c 
				   			  WHERE c.med_codigo = $med_codigo 
				   			    AND c.uni_codigo = $uni_codigo 
				   			    AND	c.esp_codigo = $esp_codigo 
				   			    AND c.age_data = b.gra_data 
				   			    AND c.age_hora = b.gra_hora_ini),0) as qtdegeral
			  FROM view_qtde_grade as b
			 WHERE b.med_codigo = $med_codigo
			   AND b.age_tipo = '$age_tipo'
			   AND b.uni_codigo = $uni_codigo
			   AND b.esp_codigo = $esp_codigo
			   AND b.gra_data >= current_date
			   AND coalesce((SELECT a.qtde 
			   				   FROM view_qtde_grade as a 
			   				  WHERE a.med_codigo = $med_codigo 
			   				    AND a.uni_codigo = $uni_codigo 
			   				    AND a.esp_codigo = $esp_codigo 
			   				    AND a.gra_data >= b.gra_data 
			   				  ORDER BY gra_data limit 1),0) - 
			   	   coalesce((SELECT qtde 
			   	   			   FROM view_qtde_medico as c 
			   	   			  WHERE c.med_codigo = $med_codigo 
			   	   			    AND c.uni_codigo = $uni_codigo 
			   	   			    AND c.esp_codigo = $esp_codigo
			   	   			    AND c.age_data = b.gra_data 
			   	   			    AND c.age_hora = b.gra_hora_ini),0) > 0 
							  ORDER BY b.gra_data, b.gra_hora_ini";
	$exec_sql = pg_query($sql);
	//echo pg_last_error($db);
	//echo $sql;
	while($linha = pg_fetch_array($exec_sql))
	{
		echo $linha[0]." - ".$linha[1]." - ".$linha[2].";";
	}
?>
