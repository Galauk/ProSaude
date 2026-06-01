<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$usr_codigo = $_GET["usr_codigo"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];

if($usr_codigo > 0){
	$andUsr = " AND usr.usr_codigo = $usr_codigo";
}

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
	$sql = "SELECT usr.usr_nome,
	     		   COUNT(DISTINCT(age.age_data)) AS total
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			 WHERE age.age_falta_medico IS NOT NULL
			 	   $andUsr
			 	   $andUni
			       $andData
		  GROUP BY usr.usr_nome
		  ORDER BY usr_nome";
} else {
	$sql = "SELECT DISTINCT(age.age_data),
	               usr.usr_nome,	               
				   TO_CHAR(age.age_data,'DD/MM/YYYY') as age_data
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			 WHERE age.age_falta_medico IS NOT NULL
			 	   $andUsr
			 	   $andUni
			       $andData
		  ORDER BY usr_nome,
		           age.age_data";
	
}

cabecario_rel("Relatório de Médicos faltosos Por Período",$data_inicial,$data_final);

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
	
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Médico</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} else {
		echo "  <tr>
				  <th>Médico</th>
				  <th>Data</th>
				</tr>";
		while($r = pg_fetch_array($query)){
			
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['age_data']}</td>";
			echo "</tr>";
		}	
	}
	
	echo "</table>";
	
}
	
rodape_rel();