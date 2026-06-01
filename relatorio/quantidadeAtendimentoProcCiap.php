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

cabecario_rel("Quantidade De Atendimentos por Procedimentos e CIAP ".$n,$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}
if($data_inicial){
	$andData = " AND ate.ate_data >= '$data_inicial'";
}
if($data_final){
	$andData .= " AND ate.ate_data <= '$data_final'";
}

$sqlCiap = "
SELECT uni.uni_codigo, ciap.ds_ciap, COUNT(ate.ate_codigo) AS total
	FROM atendimento AS ate
	JOIN agendamento AS age ON ate.age_codigo = age.age_codigo
	JOIN unidade as uni ON ate.uni_codigo = uni.uni_codigo
	JOIN rl_cds_atend_individual_ciap as rel ON rel.ate_codigo = ate.ate_codigo
	JOIN tb_ciap as ciap ON ciap.co_seq_ciap = rel.co_ciap
	WHERE 1 = 1
	$andData
	GROUP BY uni.uni_codigo, ciap.ds_ciap
	ORDER BY uni_desc, total DESC
";
$sqlProc = "
SELECT uni.uni_codigo, proc.proc_nome, COUNT(ate.ate_codigo) AS total
	FROM atendimento AS ate
	JOIN agendamento AS age ON ate.age_codigo = age.age_codigo
	JOIN unidade as uni ON ate.uni_codigo = uni.uni_codigo
	JOIN procedimento_atendimento AS pa ON ate.ate_codigo = pa.ate_codigo
	JOIN procedimento AS proc ON pa.proc_codigo = proc.proc_codigo
	WHERE 1 = 1
	$andData
	GROUP BY uni.uni_codigo, proc.proc_nome
	ORDER BY uni_desc, total DESC
";
$sqlUni = "SELECT uni_codigo, uni_desc FROM unidade";
$queryUni = pg_query($sqlUni);
$unidades = array();
while ($uni = pg_fetch_assoc($queryUni)) {
	$unidades[$uni['uni_codigo']] = $uni['uni_desc'];
}
$queryCiap=pg_query($sqlCiap);
$queryProc=pg_query($sqlProc);

$relatorio = array();
if(pg_num_rows($queryCiap)){
	while ($linha = pg_fetch_assoc($queryCiap)) {
		$relatorio[$linha['uni_codigo']]['ciap'][] = $linha; 
	}
}
if(pg_num_rows($queryProc)){
	while ($linha = pg_fetch_assoc($queryProc)) { 
		$relatorio[$linha['uni_codigo']]['proc'][] = $linha; 
	}
}
?>
<table class="lista">
<?
// pr($relatorio);
foreach($relatorio as $codigo => $unidade){
	echo '<tr><th colspan="2">'.$unidades[$codigo].'</th></tr>';
	echo '<tr><td colspan="2" style="color:#F00">CIAP:</td></tr>';
	foreach($unidade['ciap'] as $ciap){
		echo '<tr>';
		echo '<td><strong>&nbsp;&nbsp;'.$ciap['ds_ciap'].'</strong></td>';
		echo '<td class="d">'.$ciap['total'].'</td>';
		echo '</tr>';
	}
	echo '<tr><td colspan="2" style="color:#F00">Procedimentos:</td></tr>';
	foreach($unidade['proc'] as $proc){
		echo '<tr>';
		echo '<td><strong>&nbsp;&nbsp;'.$proc['proc_nome'].'</strong></td>';
		echo '<td class="d">'.$proc['total'].'</td>';
		echo '</tr>';
	}
}
echo "</table>";
rodape_rel();
