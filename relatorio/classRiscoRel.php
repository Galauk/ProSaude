<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	
set_time_limit(0);
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
cabecario_rel("Quantidade por Classificação de risco",$data_inicial,$data_final);
$uni_codigo = $_GET['uni_codigo'];
$uni_where = "";
if($uni_codigo){
	$uni_where = "AND uni_codigo = $uni_codigo";
}

$sql = "SELECT DISTINCT(TO_CHAR(pc_data,'DD/MM/YYYY')) AS data,
		       (SELECT COUNT(pc_codigo) 
				  FROM pre_consulta 
				 WHERE date_trunc('day',pc_data) = DATE_TRUNC('day',p.pc_data)
				   AND pc_clas_risco = 4) AS risco4,
			       (SELECT COUNT(pc_codigo) 
				  FROM pre_consulta 
				 WHERE DATE_TRUNC('day',pc_data) = DATE_TRUNC('day',p.pc_data)
				   AND pc_clas_risco = 3) AS risco3,
		       (SELECT COUNT(pc_codigo) 
				  FROM pre_consulta 
				 WHERE DATE_TRUNC('day',pc_data) = DATE_TRUNC('day',p.pc_data)
				   AND pc_clas_risco = 2) AS risco2,
		       (SELECT COUNT(pc_codigo) 
				  FROM pre_consulta 
				 WHERE DATE_TRUNC('day',pc_data) = DATE_TRUNC('day',p.pc_data)
				   AND pc_clas_risco = 1) as risco1,
				   to_char(pc_data,'DD/MM/YYYY') AS pc_data
		  FROM pre_consulta AS p
		  JOIN agendamento ag
	  		ON ag.age_codigo = p.age_codigo
		 WHERE DATE_TRUNC('day',pc_data) >= '$data_inicial'
		   AND DATE_TRUNC('day',pc_data) <= '$data_final'
		   $uni_where
		 ORDER by pc_data";

$query = pg_query($sql) or die($sql.pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo 
	"<table class=\"lista\">
		<tr>
			<th>
				DATA
			</th>
			<th>
				VERMELHO
			</th>
			<th>
				AMARELO
			</th>
			<th>
				VERDE
			</th>
			<th>
				AZUL
			</th>
		</tr>";
	
	while($reg = pg_fetch_array($query)){
		echo 
		"<tr>
			<td>
				$reg[data]
			</td>
			<td>
				$reg[risco1]
			</td>
			<td>
				$reg[risco2]
			</td>
			<td>
				$reg[risco3]
			</td>
			<td>
				$reg[risco4]
			</td>
		</tr>";
	}
	echo"	
	</table>";
}
rodape_rel();