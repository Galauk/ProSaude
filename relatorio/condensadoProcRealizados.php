<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$uni_codigo = $_GET["uni_codigo"];
$tp_rel = $_GET["tp_rel"];

if($ate_tipo=='V') { $n = 'Visitas Domiciliares'; }
if($ate_tipo=='P') { $n = 'Procedimentos'; }
if($ate_tipo=='A') { $n = 'Atendimentos Individuais'; }

cabecario_rel("Condensado Procedimentos Realizados ".$n,$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}
if($data_inicial){
	$andData = " AND '$data_inicial' <= ate.ate_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= ate.ate_data";
}

$sql = "
	SELECT uni.uni_codigo, uni.uni_desc, ate.ate_tipo, COUNT(ate.ate_codigo) AS total
  FROM atendimento AS ate
  JOIN unidade as uni
  	ON ate.uni_codigo = uni.uni_codigo
	WHERE ate_tipo != ''
	$andUni	
 	$andData
	GROUP BY uni.uni_codigo, uni_desc, ate_tipo
	ORDER BY uni_desc";
$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
	$relatorio = array();
	while ($linha = pg_fetch_assoc($query)) {
		$relatorio[$linha['uni_codigo']]['unidade'] = $linha['uni_desc']; 
		$relatorio[$linha['uni_codigo']]['proc'][] = $linha; 
	}
}
?>
<table class="lista">
<?
foreach($relatorio as $unidade){
	echo "<tr>";
	echo '<th colspan="2">'.$unidade['unidade'].'</th>';
	echo '</tr>';
	foreach ($unidade['proc'] as $proc) {
		echo '<tr>';
		echo '<td>&nbsp;&nbsp;'.$arrayTipoAtendimento[$proc['ate_tipo']].'</td>';
		echo '<td class="d">'.$proc['total'].'</td>';
		echo '</tr>';
	}
}
echo "</table>";
rodape_rel();
