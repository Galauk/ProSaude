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
$usu_codigo = $_GET["usu_codigo"];

if($usu_codigo > 0){
	$andUsu = " AND vac.usu_codigo=$usu_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= vac.vac_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= vac.vac_data";
}

$sql = "select usu_nome,pro_nome,TO_CHAR(vac.vac_data,'DD/MM/YYYY') AS vac_data 
from vacina_usuario as vac 
join produto as pro on pro.pro_codigo = vac.pro_codigo 
join usuario as usu on usu.usu_codigo = vac.usu_codigo
 where vac_acao='Z' 
 $andData
 $andUsu";

cabecario_rel("Vacinas Aprazadas por Usuários",$data_inicial,$data_final,$linhaUnidade[uni_desc]);
	
$query=pg_query($sql) or die($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";

	
	while($r = pg_fetch_array($query)){
		if($r['usu_nome'] != $usu_nome){
			if($usu_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$usu_nome = $r['usu_nome'];
			echo "<tr>
			  <th colspan=\"2\">$usu_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Vacina</th>
					  <th>Data do Aprazamento</th>
					</tr>";
		}
		echo "<tr>";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td class=\"d\">{$r['vac_data']}</td>";
		echo "</tr>";
	}

echo "</table>";
	
rodape_rel();