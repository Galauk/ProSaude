<?php 
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	include_once $_SESSION[root].$_SESSION[comum]."/library/php/funcoes.inc.php";
	
	$sqlSecretaria = "SELECT * FROM secretaria WHERE tipo_secretaria = 'SAU'";
	$querySecretaria = pg_query($sqlSecretaria);
	$row = pg_fetch_array($querySecretaria);
	
	$di = $_GET["di"];
	$df = $_GET["df"];
	$cd10_codigo = $_GET["cd10_codigo"];
	$gd_codigo = $_GET["gd_codigo"];
	
	$where = "WHERE gc_codigo > 0";
	$where .= ($di != ""  ? " AND a.ate_data >= '$di'" : "").""; // SOMENTE DATA INICIAL
	$where .= ($df != ""  ? " AND a.ate_data <= '$df'" : "").""; // SOMENTE DATA FINAL
	if($cd10_codigo != "" && $gd_codigo != 0){
		$where .= "AND gd.gd_codigo = $gd_codigo";
	}
	if($cd10_codigo != "" && $cd10_codigo != 0){
		$where .="AND c.cd10_codigo = $cd10_codigo";
	}
	
	
	$sql2 = "SELECT gc.gc_codigo ,
					cd10_descricao,
					gd_descricao,
					gd.gd_codigo,
					usu_nome 
			   FROM grupos_cid AS gc
			   JOIN cid10 AS c
			     ON c.cd10_codigo = gc.cd10_codigo
			   JOIN grupo_doencas AS gd
			     ON gd.gd_codigo = gc.gd_codigo
			   JOIN atendimento AS a
			     ON a.cd10_codigo = gc.cd10_codigo
			   JOIN usuario AS u
			     ON u.usu_codigo = a.usu_codigo
			 $where
			  ORDER BY gd_descricao";
	
	$q = pg_query($sql2) or die (pg_last_error());

 ?><html>
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
	 				<div id="sec_nome"><?=$row['uni_desc'];?></div>
	 				<div id="pref_nome"><?=$row['nome_secretaria'];?></div>
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		
	 		<div id="dados_pac">
	 			<div id="pac_nome"><?=$row['usu_nome'];?></div>
	 			<div id="pac_end"><?=$endereco;?></div>
	 		</div>
	 		
	 		<div id="receita">
				<h2 class="titulo">Relatórios Doen&ccedil;as</h2>
				<table class="lista">
				<?php
					$grupo = 1;
					$megatotal = 0;
					while($r=pg_fetch_array($q)){
						if($grupo != $r[2]){
							if($grupo != 1){
								echo "
								<tr>
									<td colspan=3 align=right><b>Total:  </b>".number_format($total,2,",",".")."</td>
								</tr>
								<tr>
									<td colspan=3>&nbsp;</td>
								</tr>";
								$megatotal += $total;
								$total = 0;
								
								
							}
							echo "
							<tr>
								<th colspan=3>
									$r[2]
								</th>
							</tr>";
								
							$grupo = $r[2];	

						}
						echo 
						"<tr>
							<td>
								$r[0]	
							</td>
							<td>
								$r[1]	
							</td>
							<td>
								$r[4]
							</td>
						</tr>";
						$total++;
					}
					$megatotal += $total;
				?>
				<tr>
					<td colspan=3 align=right><b>Total: </b><?=number_format($total,2,",",".") ?></td>
				</tr>
				<tr>
					<td colspan=3><b>Total Geral: </b><?=number_format($megatotal,2,",",".") ?></td>
				</tr>
				</table>
	 		</div>
	 		
	 		<div id="footer">
	 			<?=$row['uni_desc'];?> - <?=$row['uni_endereco'];?>
	 		</div>
	 	</div>
	 </body>
 </html>