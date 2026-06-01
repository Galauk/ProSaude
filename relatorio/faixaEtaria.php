<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();
$tp_rel = $_GET["tp_rel"];

cabecario_rel("Faixa Etaria dos Pacientes",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND uni.uni_codigo=$uni_codigo";
}



$calculoIdade = " SELECT calcula_idade (usu_codigo) AS idade,count(calcula_idade (usu_codigo)) AS total FROM usuario GROUP BY calcula_idade (usu_codigo) ORDER BY idade";

// $sql = "select usr_nome,count(age.age_codigo) as total from agendamento as age join usuarios as usr on usr.usr_codigo = age.med_codigo
// where 1=1 		 	   $andData
// 	           $andUnidade group by med_codigo,usr_nome order by usr_nome";
	           
$query=pg_query($calculoIdade);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Idade</th>
				  <th>N&uacute;mero de pessoas com a mesma idade.</th>
				</tr>";
				
		while($r = pg_fetch_array($query)){
			if ($r['idade'] >= 0 && $r['idade'] < 119) {
				echo "<tr>";
				echo "  <td>{$r['idade']}</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
			}
		}
	} 
	
	echo "</table>";
}
rodape_rel();