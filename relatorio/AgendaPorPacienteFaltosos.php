<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$usu_codigo = $_GET["usu_codigo"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];

if($usu_codigo > 0){
	$andUsu = " AND usu.usu_codigo = $usu_codigo";
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
	$sql = "SELECT usu.usu_nome,
	     		   COUNT(DISTINCT(age.age_data)) AS total
			  FROM agendamento AS age
			  JOIN usuario AS usu
			    ON usu.usu_codigo = age.usu_codigo
			 WHERE age.age_atendido='F'
			 	   $andUsu
			 	   $andUni
			       $andData
		  GROUP BY usu.usu_nome
		  ORDER BY usu_nome";
} else {
	$sql = "SELECT DISTINCT(age.age_data),
	               usu.usu_nome,
				   age_horario,
				   usu.usu_fone,
				   TO_CHAR(age.age_data,'DD/MM/YYYY') as age_data
			  FROM agendamento AS age
			  JOIN usuario AS usu
			    ON usu.usu_codigo = age.usu_codigo
			 WHERE age.age_atendido='F'
			 	   $andUsr
			 	   $andUni
			       $andData
		  ORDER BY usu_nome,
		           age.age_data";
	
}
//echo $sql;

cabecario_rel("Relatório de Pacientes Faltosos Por Período",$data_inicial,$data_final);

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
	
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Paciente</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} else {
		echo "  <tr>
				  <th>Paciente</th>
				  <th>Fone</th>
				  <th>Data/Hora</th>
				</tr>";
		while($r = pg_fetch_array($query)){
			
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['age_data']} - ".substr($r['age_horario'],0,5)."</td>";
			echo "</tr>";
		}	
	}
	
	echo "</table>";
	
}
	
rodape_rel();