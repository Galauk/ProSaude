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
$esp_codigo = $_GET["esp_codigo"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];

if($esp_codigo > 0){
	$andEsp = " AND age.esp_codigo = $esp_codigo";
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
	$sql = " SELECT esp.esp_nome,
			        COUNT(age.age_codigo) AS total
			   FROM agendamento AS age
			   JOIN especialidade AS esp
			     ON esp.esp_codigo=age.esp_codigo
			   JOIN usuario AS usu
			     ON usu.usu_codigo=age.usu_codigo
			  WHERE age.age_atendido='A'
			  	    $andEsp
			  	    $andData
			  	    $andUni
			  GROUP BY age.esp_codigo,
			           esp.esp_nome
			  ORDER BY esp.esp_nome";
} else {
	$sql = " SELECT esp.esp_nome,
			        usu.usu_nome,
			        TO_CHAR(age.age_data,'DD/MM/YYYY') AS age_data
			   FROM agendamento AS age
			   JOIN especialidade AS esp
			     ON esp.esp_codigo=age.esp_codigo
			   JOIN usuario AS usu
			     ON usu.usu_codigo=age.usu_codigo
			  WHERE age.age_atendido='A'
			  	    $andEsp
			  	    $andData
			  	    $andUni
			  ORDER BY esp.esp_nome,
			           age.age_data";	
}


cabecario_rel("Atendimento por Especialidade",$data_inicial,$data_final,$linhaUnidade[uni_desc]);
	
$query=pg_query($sql) or die($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";

if($tp_rel == 0){
	echo "  <tr>
			  <th>Especialidade</th>
			  <th>Quantidade</th>
			</tr>";
	
	while($r = pg_fetch_array($query)){
		echo "<tr>";
		echo "  <td>{$r['esp_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
	}
} else {
	while($r = pg_fetch_array($query)){
		if($r['esp_nome'] != $esp_nome){
			if($esp_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$esp_nome = $r['esp_nome'];
			echo "<tr>
			  <th colspan=\"2\">$esp_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Paciente</th>
					  <th>Data</th>
					</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['usu_nome']}</td>";
		echo "  <td>{$r['age_data']}</td>";
		echo "</tr>";
	}	
}

echo "</table>";
	
rodape_rel();