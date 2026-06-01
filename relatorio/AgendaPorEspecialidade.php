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
	$andUni = "AND age.uni_codigo = $uni_codigo";
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
			  WHERE 1=1
			  	    $andEsp
			  	    $andData
			  	    $andUni
			  GROUP BY age.esp_codigo,
			           esp.esp_nome
			  ORDER BY esp.esp_nome";
} 
if ($tp_rel =='1') {
	$sql = " SELECT esp.esp_nome,
			        usu.usu_nome,
					usu.usu_fone,
			        TO_CHAR(age.age_data,'DD/MM/YYYY') AS age_data
				,calcula_idade(usu.usu_codigo) as idade
			   FROM agendamento AS age
			   JOIN especialidade AS esp
			     ON esp.esp_codigo=age.esp_codigo
			   JOIN usuario AS usu
			     ON usu.usu_codigo=age.usu_codigo
			  WHERE 1=1
			  	    $andEsp
			  	    $andData
			  	    $andUni
			  ORDER BY esp.esp_nome,
			           age.age_data";	
}

if ($tp_rel =='2') {
$sql = "
SELECT uni.uni_desc,esp.esp_nome,count(*) as total,calcula_idade(usu.usu_codigo) as idade 
FROM agendamento AS age 
JOIN especialidade AS esp ON esp.esp_codigo=age.esp_codigo 
JOIN usuario AS usu ON usu.usu_codigo=age.usu_codigo
JOIN unidade as uni on uni.uni_codigo=age.uni_codigo
 WHERE 1=1
			  	    $andEsp
			  	    $andData
			  	    $andUni
 group by uni.uni_desc,esp.esp_nome,calcula_idade(usu.usu_codigo)
order by uni_desc, esp_nome, idade";

}


cabecario_rel("Agenda por Especialidade",$data_inicial,$data_final,$linhaUnidade[uni_desc]);

if(isset($_GET['sql'])) echo $sql;

$query=pg_query($sql) or die($sql);
echo "<table class=\"lista\">";

if($tp_rel == 0){
	echo "  <tr>
			  <th>Especialidade</th>
			  <th>Quantidade</th>
			</tr>";
	
	while($r = pg_fetch_array($query)){
		echo "<tr>";
		echo "  <td>{$r['esp_nome']}</td>";
		echo "  <td>{$r['total']}</td>";
		echo "</tr>";
	}
} if($tp_rel == 1){

	while($r = pg_fetch_array($query)){
		if($r['esp_nome'] != $esp_nome){
			if($esp_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
			}
			
			$esp_nome = $r['esp_nome'];
			echo "<tr>
			  <th colspan=\"4\">$esp_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Paciente</th>
					  <th>Fone</th>
					  <th>Idade</th>
					  <th>Data</th>
					</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['usu_nome']}</td>";
		echo "  <td>{$r['usu_fone']}</td>";
		echo "  <td>{$r['idade']}</td>";
		echo "  <td>{$r['age_data']}</td>";
		echo "</tr>";
	}	
}

if($tp_rel == 2){
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
		      <th>Especialidade</td>
			  <th>Idade</th>
			  <th>Quantidade</th>
			</tr>";
}	
		echo "<tr>";
		echo "  <td>{$r['esp_nome']}</td>";
		echo "  <td>{$r['idade']}</td>";
		echo "  <td>{$r['total']}</td>";
		echo "</tr>";
	}
}



echo "</table>";
	
rodape_rel();
