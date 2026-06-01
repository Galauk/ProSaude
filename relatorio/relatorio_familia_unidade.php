<?php 
session_start();
include_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/formClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/commonClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."class/tableClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
include_once $_SESSION['root'].$_SESSION['comum']."library/php/__array.php";
include_once $_SESSION['root'].$_SESSION['comum']."/library/php/funcoes.inc.php";

$di = $_GET["di"];
$df = $_GET["df"];
$uni_codigo = $_GET["uni_codigo"];

$where = "WHERE u.usu_codigo > 0";
$where .= ($di != ""  ? " AND u.usr_cad_dt >= '$di'" : "").""; // SOMENTE DATA INICIAL
$where .= ($df != ""  ? " AND u.usr_cad_dt <= '$df'" : "").""; // SOMENTE DATA FINAL
if($uni_codigo != "" && $uni_codigo != 0){
	$where .= "AND u.uni_codigo = $uni_codigo";
}


$sql = "SELECT u.usu_nome, r.rua_nome, d.dom_numero, u.uni_codigo, d.dom_codigo
FROM usuario AS u
INNER JOIN domicilio d ON u.usu_codigo = d.usu_codigo_responsavel
INNER JOIN rua r ON d.rua_codigo = r.rua_codigo
$where
ORDER BY u.uni_codigo, d.dom_codigo ASC";

$q = pg_query($sql) or die (pg_last_error());
$queryUnidades = pg_query("SELECT uni_codigo, uni_desc FROM unidade");
$unidades = array();
while ($arr = pg_fetch_assoc($queryUnidades)) {
	$unidades[$arr['uni_codigo']] = $arr['uni_desc'];
}
$relatorio = array();
while($r = pg_fetch_assoc($q)){
	$relatorio[] = $r;
}
?>
<html>
	<head>
		<title>Família por Unidade</title>
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
				<h2 class="titulo">Relatórios de Familias</h2>
				<table class="lista">
				<?php
					$unidade = $relatorio[0]['uni_codigo'];
					$totalUnidade = 0;
					$total = 0;
					echo '<tr><td align="center"><big><b>UNIDADE: '.$unidades[$unidade].'</b></big></td></tr>';
					echo "<tr><th>".$relatorio[0]['ds_pergunta_detalhe']."</th></tr>";
					foreach ($relatorio as $linha) {
						if($unidade != $linha['uni_codigo']){
							if($unidade != $r['uni_codigo']){
								echo '<tr><td align="right"><b>Total Geral: '.$totalUnidade.'</b></td></tr>';
								$totalUnidade = 0;
							}
							echo '<tr><td align="center"><big><b>UNIDADE: '.$unidades[$linha['uni_codigo']].'</b></big></td></tr>';
						}
						echo '<tr><td>'.$linha['rua_nome'].", ".$linha['dom_numero']."</td></tr>";
						$total++;
						$totalUnidade++;

						$unidade = $linha['uni_codigo'];
						
					}
				?>
					<tr><td align="right"><b>Total Geral: </b><?=$totalUnidade ?></td></tr>
					<tr><td align="right"><b>Total: <?= $total ?></b></td></tr>
				</table>
	 		</div>
	 	</div>
	 </body>
 </html>