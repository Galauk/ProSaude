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

$where = "WHERE usu.usu_codigo > 0";
$where .= ($di != ""  ? " AND usu.usr_cad_dt >= '$di'" : "").""; // SOMENTE DATA INICIAL
$where .= ($df != ""  ? " AND usu.usr_cad_dt <= '$df'" : "").""; // SOMENTE DATA FINAL
if($uni_codigo != "" && $uni_codigo != 0){
	$where .= "AND usu.uni_codigo = $uni_codigo";
}


$sql = "SELECT usu.usu_nome, p.ds_pergunta_detalhe, p.co_pergunta_detalhe, usu.uni_codigo
FROM usuario AS usu
INNER JOIN usuario_doencas ud ON ud.usu_codigo = usu.usu_codigo
INNER JOIN tb_pergunta_detalhe p ON ud.co_pergunta_detalhe = p.co_pergunta_detalhe
$where
ORDER BY usu.uni_codigo, p.co_pergunta_detalhe ASC";

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
		<title>Agendamento</title>
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
				<h2 class="titulo">Relatórios Doen&ccedil;as</h2>
				<table class="lista">
				<?php
					$doenca = $relatorio[0]['co_pergunta_detalhe'];
					$unidade = $relatorio[0]['uni_codigo'];
					$totalUnidade = 0;
					$total = 0;
					echo '<tr><td align="center"><big><b>UNIDADE: '.$unidades[$unidade].'</b></big></td></tr>';
					echo "<tr><th>".$relatorio[0]['ds_pergunta_detalhe']."</th></tr>";
					foreach ($relatorio as $linha) {
						if($unidade != $linha['uni_codigo']){
							if($doenca != $linha['co_pergunta_detalhe']){
								echo '<tr><td align="right"><b>Total: '.$total.'</b></td></tr>';
								$total = 0;
							}
							if($unidade != $r['uni_codigo']){
								echo '<tr><td align="right"><b>Total Geral: '.$totalUnidade.'</b></td></tr>';
								$totalUnidade = 0;
							}
							echo '<tr><td align="center"><big><b>UNIDADE: '.$unidades[$linha['uni_codigo']].'</b></big></td></tr>';
						} else {

						if($doenca != $linha['co_pergunta_detalhe']){
							echo '<tr><td align="right"><b>Total: '.$total.'</b></td></tr>';
							$total = 0;
						}
						}
						if($doenca != $linha['co_pergunta_detalhe']){
							echo "<tr><th>".$linha['ds_pergunta_detalhe']."</th></tr>";
						}
						
						echo '<tr><td>'.$linha['usu_nome']."</td></tr>";
						$total++;
						$totalUnidade++;

						$doenca = $linha['co_pergunta_detalhe'];	
						$unidade = $linha['uni_codigo'];
						
					}
					echo '<tr><td align="right"><b>Total: '.$total.'</b></td></tr>';
				?>
					<tr><td align="right"><b>Total Geral: </b><?=$totalUnidade ?></td></tr>
				</table>
	 		</div>
	 	</div>
	 </body>
 </html>