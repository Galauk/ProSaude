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

cabecario_rel("CISVIR - Agendamento por Unidade",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age.age_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= age.age_data";
}

if($tp_rel == 0){//verifica se é sintético para montar select e tela;
	$sql = " SELECT uni_desc,
	                COUNT(age.age_codigo) AS total
			   FROM agendamento AS age
			   JOIN unidade AS uni
			     ON uni.uni_codigo=age.uni_codigo
			  WHERE age.age_atendido='N'
			        $andUni
			        $andData
			  GROUP BY uni.uni_desc
			  ORDER BY uni.uni_desc";
} else {
	$sql = " SELECT uni_desc,
			        usr_nome,
			        usu_nome,
					usu_fone,
			        TO_CHAR(age.age_data,'DD/MM/YYYY') AS age_data
			   FROM agendamento AS age
			   JOIN unidade AS uni
			     ON uni.uni_codigo=age.uni_codigo
			   JOIN usuarios AS usr
			     ON usr.usr_codigo=age.med_codigo
			   JOIN usuario AS usu
			     ON usu.usu_codigo=age.usu_codigo
			  WHERE age.age_atendido='N'
			        $andUni
			        $andData
			  ORDER BY uni.uni_desc,
			           usr.usr_nome,
			           age.age_data,
			           usu.usu_nome";
}

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Unidade</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['uni_desc']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} else {
		while($r = pg_fetch_array($query)){
			if($r['uni_desc'] != $uni_desc){
				if($uni_desc){
					echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
				}
				
				$uni_desc = $r['uni_desc'];
				echo "<tr>
				  <th colspan=\"3\">$uni_desc</th>
				</tr>";
				echo "  <tr>
						  <th>Médico</th>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>Data</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['age_data']}</td>";
			echo "</tr>";
		}	
	}
	
	echo "</table>";
}
rodape_rel();