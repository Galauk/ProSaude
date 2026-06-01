<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];


if($uni_codigo > 0){
	$andUni = " AND age.uni_codigo = $uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age.age_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= age.age_data";
}



if($tp_rel == 0){
	$sql = "SELECT DISTINCT pc_clas_risco, 
       				   CASE pc_clas_risco 
							WHEN '1' THEN 'IMEDIATO' 
							WHEN '2' THEN '20 MINUTOS' 
							WHEN '3' THEN '60 MINUTOS' 
							WHEN '4' THEN '4 HORAS' 
       				    END pc_clas_risco_nome ,
					(SELECT COUNT(*) FROM atendimento ate 
					  JOIN agendamento age
					    ON age.age_codigo = ate.age_codigo 
					  JOIN pre_consulta pre2 
					    ON pre2.age_codigo = age.age_codigo 
					 WHERE pre2.pc_clas_risco = pre.pc_clas_risco 
					    $andUni
       	   		        $andData  
					   ) total,
              		 ROUND((SELECT SUM(
  (SELECT EXTRACT(HOURS FROM (to_timestamp(ate_data || ' ' || ate_hora, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP))))*60 + 
  (SELECT SUM((SELECT EXTRACT(MINUTE FROM (to_timestamp(ate_data || ' ' || ate_hora, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP)))))
FROM atendimento ate 
				  	  JOIN agendamento age 
			            ON age.age_codigo = ate.age_codigo 
			          JOIN pre_consulta pre2 
			            ON pre2.age_codigo = age.age_codigo 
			         WHERE pre2.pc_clas_risco = pre.pc_clas_risco 
		              $andUni
       	   		      $andData 
			            ) / (SELECT COUNT(*) FROM atendimento ate 
				      JOIN agendamento age 
			            ON age.age_codigo = ate.age_codigo 
			          JOIN pre_consulta pre3 
			            ON pre3.age_codigo = age.age_codigo 
			         WHERE pre3.pc_clas_risco = pre.pc_clas_risco 
			            $andUni
       			   		$andData 
			            )) media 
       		  FROM atendimento ate 
       		  JOIN agendamento age 
       		    ON age.age_codigo = ate.age_codigo 
       		  JOIN pre_consulta pre 
       		    ON pre.age_codigo = age.age_codigo 
       		 WHERE 1=1 
       		   $andData ORDER BY pc_clas_risco";
			//die($sql);
			//ECHO $sql; exit;
		
} else {
	$sql = "SELECT TO_CHAR(age_data,'dd/mm/yyyy') as data,
				   age_paciente,
       			   (SELECT EXTRACT(HOURS FROM (to_timestamp(ate_data || ' ' || ate_hora, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP)))as horas ,
       			   (SELECT EXTRACT(MINUTE FROM (to_timestamp(ate_data || ' ' || ate_hora, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP)))as minutos,
       			   pc_clas_risco,
				   CASE pc_clas_risco
				   		WHEN '1' THEN 'IMEDIATO'
					   	WHEN '2' THEN '20 MINUTOS'
					   	WHEN '3' THEN '60 MINUTOS'
					   	WHEN '4' THEN '4 HORAS'
				   END pc_clas_risco_nome				  
			  FROM atendimento ate 
			  JOIN agendamento age 
			    ON age.age_codigo = ate.age_codigo
			  LEFT JOIN pre_consulta pre
			    ON pre.age_codigo = age.age_codigo 
			 WHERE 1=1			 	   
    			   $andUni
       			   $andData
 			ORDER BY pre.pc_clas_risco,
 					 age_data,
 					 minutos";
       			  // ECHO $sql; exit;
}

cabecario_rel("Tempo esperado para atendimento",$data_inicial,$data_final);

if(isset($_GET['sql'])) die($sql);
$query=pg_query($sql);

//echo "<pre>".print_r($sql,1);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Classificação</th>				  
				  <th>Total de consultas</th>
				  <th>Média das consultas</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			if($r['pc_clas_risco'] == 1){
				 $cor = "red";
			}elseif ($r['pc_clas_risco'] == 2){
				$cor = "GoldenRod";
			}elseif ($r['pc_clas_risco'] == 3){
				 $cor = "green";
			}elseif ($r['pc_clas_risco'] == 4){
				$cor = "blue";
			}else{
			    $cor = "";
            }
			echo "  <tr style=color:$cor;>";		
			echo "  <td>{$r['pc_clas_risco_nome']}</td>";
			echo "  <td class=\"d\" >{$r['total']}</td>";
			echo "  <td class=\"d\">".mascaraMinutos($r['media'])."</td>";
			echo "</tr>";
			
		}
	} else {
		echo "  <tr>
				  <th>Data</th>
				  <th>Paciente</th>
				  <th>Tempo</th>						  
				</tr>";
		while($r = pg_fetch_array($query)){
		if($r['pc_clas_risco'] == 1){
				 $cor = "red";
			}elseif ($r['pc_clas_risco'] == 2){
				$cor = "GoldenRod";
			}elseif ($r['pc_clas_risco'] == 3){
				 $cor = "green";
			}elseif ($r['pc_clas_risco'] == 4){
				$cor = "blue";
			}else{
			    $cor = "";
            }
		
			if($r['pc_clas_risco'] != $risco){
				echo "<tr>
					  	<th colspan=4 style=color:$cor>{$r['pc_clas_risco_nome']}</th>	  
					  </tr>";
				echo "<tr>";
			}
			echo "<tr>";
			echo "  <td>{$r['data']}</td>";
			echo "  <td>{$r['age_paciente']}</td>";
			echo "  <td>".mascaraHoraEMinutos($r['horas'],$r['minutos'])."</td>";		
			echo "</tr>";
			$risco = $r['pc_clas_risco'];
		}	
	}
	
	echo "</table>";
}
	
rodape_rel();