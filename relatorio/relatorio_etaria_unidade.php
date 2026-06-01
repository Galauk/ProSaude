<?php 
session_start();
include_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/formClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/commonClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/tableClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
include_once $_SESSION['root'].$_SESSION['comum']."library/php/__array.php";
include_once $_SESSION['root'].$_SESSION['comum']."/library/php/funcoes.inc.php";

$faixas = array(
	array('sql'=>'idade < 1','label'=>'Menos que 1', 'n'=>array(0,1)),
	array('sql'=>'idade >= 1 AND idade <= 4','label'=>'De 1 à 4', 'n'=>array(1,4)),
	array('sql'=>'idade >= 5 AND idade <= 9','label'=>'De 5 à 9', 'n'=>array(5,9)),
	array('sql'=>'idade >= 10 AND idade <= 14','label'=>'De 10 à 14', 'n'=>array(10,14)),
	array('sql'=>'idade >= 15 AND idade <= 19','label'=>'De 15 à 19', 'n'=>array(15,19)),
	array('sql'=>'idade >= 20 AND idade <= 39','label'=>'De 20 à 39', 'n'=>array(20,39)),
	array('sql'=>'idade >= 40 AND idade <= 49','label'=>'De 40 à 49', 'n'=>array(40,49)),
	array('sql'=>'idade >= 50 AND idade <= 59','label'=>'De 50 à 59', 'n'=>array(50,59)),
	array('sql'=>'idade >= 60','label'=>'Mais que 60', 'n'=>array(60,200)),
);
function getFaixa($idade){
	global $faixas;
	foreach ($faixas as $i => $faixa) {
		if($idade >= $faixa['n'][0] && $idade <= $faixa['n'][1]){
			return $i;
		}
	}
	return 0;
}
$f = $_GET["f"];
$uni_codigo = $_GET["uni_codigo"];

$where = "WHERE usu.usu_codigo > 0";
if($f != ''){
	$range = preg_replace('/idade/', 'extract(year from age(usu.usu_datanasc))', $faixas[$f]['sql']);
	$where .= " AND ($range) ";
}
if($uni_codigo != "" && $uni_codigo != 0){
	$where .= "AND usu.uni_codigo = $uni_codigo";
}


$sql = "SELECT extract(year from age(usu_datanasc)) as idade, usu.usu_nome, usu.uni_codigo
FROM usuario AS usu
$where
ORDER BY usu.uni_codigo, idade ASC";
$q = pg_query($sql) or die (pg_last_error());
$queryUnidades = pg_query("SELECT uni_codigo, uni_desc FROM unidade");
$unidades = array();
while ($arr = pg_fetch_assoc($queryUnidades)) {
	$unidades[$arr['uni_codigo']] = $arr['uni_desc'];
}
$relatorio = array();
while($r = pg_fetch_assoc($q)){
	$faixa = getFaixa($r['idade']);
	$relatorio[$r['uni_codigo']][$faixa] += 1;
}
#print_r($relatorio);
#die;
?>
<html>
	<head>
		<title>Faixa etária por unidade</title>
		<link href='../receita.css' rel='stylesheet' type='text/css'>
	</head>
	<body onload="exit;window.print();">
	 	<div id="page">
	 		<div id="header">
	 			<div id="header_logo">
	 				<img src="<?=LINKSAUDE?>/imgs/brasao.jpg" title="Logo Prefeitura" />
	 			</div>
	 			<div id="header_dados">
	 				<div id="sec_nome"><?=$row['usu_nome'];?></div>
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		<div id="receita">
				<h2 class="titulo">Faixa etária por unidade</h2>
				<table class="lista">
				<?php
					foreach ($relatorio as $unidade => $faixa) {
						$totalUnidade = 0;
						echo '<tr><td colspan="2" align="center"><big><b>UNIDADE: '.$unidades[$unidade].'</b></big></td></tr>';
						foreach ($faixa as $nFaixa => $total) {
							echo '<tr><td>'.$faixas[$nFaixa]['label'].'</td><td>'.$total.'</td></tr>';
							$totalUnidade += $total;
						}
						echo '<tr><td></td><td><b>Total Geral: '.$totalUnidade.'</b></td></tr></table><table class="lista">';
					}
				?>
				</table>
	 		</div>
	 	</div>
	 </body>
 </html>