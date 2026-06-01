<?php
	$io_codigo = $_REQUEST['io_codigo']; 
	include_once "../../global.php";
	$sql = "SELECT (SELECT count(io_codigo)
					  FROM atendimento_internacao 
					 WHERE io_codigo = '$io_codigo') qtde,
				    ate_data,
				    usr_nome,
				    io_codigo,
				    ate.ate_codigo,
				    ate.ate_hora
			  FROM atendimento_internacao ati
			  JOIN atendimento ate
			    ON ati.ate_codigo = ate.ate_codigo
			  JOIN usuarios usr
			    on ate.med_codigo = usr.usr_codigo
			 WHERE io_codigo = '$io_codigo'
			 GROUP BY ate_data,
			 		  usr_nome,
			 		  io_codigo,
			 		  ate.ate_codigo,
			 		  ate.ate_hora
			 order by ate_hora DESC";
	
	$query = pg_query($sql); 
	
	
?>

{
    "timeline":
    {
        "headline":"UPA",
        "type":"default",
		"text":"UPA",
        "date": [           
           <?php 
           //	while ($r=pg_fetch_array($query)){
           	for ($i = 1; $i<=$r=pg_fetch_array($query); $i++){           		
           		$data = str_replace("-", ",", $r[ate_data]);
           	echo" { \"startDate\":\"$data\",
                \"headline\":\"$r[usr_nome]<br> Hora:$r[ate_hora]\",
                \"text\":\"teste\"}";
           		if($i < $r[qtde]){
           			echo ",";
           		}
           		   		
           	}
           ?>
        
        ]
    }
}