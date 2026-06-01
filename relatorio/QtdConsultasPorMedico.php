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
$med_codigo = $_GET["med_codigo"];
$tp_rel = $_GET["tp_rel"];

cabecario_rel("Quantidade de Consultas por Medico",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age.age_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= age.age_data";
}


$sql = "select usr_nome,count(age.age_codigo) as total from agendamento as age join usuarios as usr on usr.usr_codigo = age.med_codigo
where 1=1 		 	   $andData
	           $andUnidade group by med_codigo,usr_nome order by usr_nome";
	           
$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Medico</th>
				  <th>Quantidade</th>
				</tr>";
				
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
			$tt += $r[total];
		}
			echo "<tr>";
			echo "  <td align='right'><b>Total Geral:</b></td>";
			echo "  <td class=\"d\">$tt</td>";
			echo "</tr>";
	} 
	
	echo "</table>";
}
rodape_rel();