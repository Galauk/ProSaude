<?
session_start();
	
	//include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$ite_codigo = $_GET[ite_codigo];
	$txa_codigo = $_GET[txa_codigo];
	
		$sqlValores = "SELECT vlr_codigo,
							  vlr_valordereferencia,
							  vlr_faixa_etaria
					     FROM valoresdereferencia
					    WHERE ite_codigo = $ite_codigo";
		$queryFamilia = pg_query($sqlValores);
		//echo $sqlValores;
		echo "<tr>
				<td colspan=4>
					<a href='valoresReferencia.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo'><img src='../../WebSocialComum/imgs/adicionar_on.jpg'></a>
				</td>
			  </tr>";
		while($registro = pg_fetch_array($queryFamilia)): ?>

<tr class="oculta oculta<?=$ite_codigo;?>">
  <td colspan=1><b><?=$registro[1];?></b></td>
  <td colspan=1><b><?=$registro[2];?></b></td>
  <td colspan=1><a href="valoresReferencia.php?ite_codigo=<?=$ite_codigo;?>&vlr_codigo=<?=$registro[0];?>&txa_codigo=<?=$txa_codigo;?>"><img src="../../WebSocialComum/imgs/editar_on.jpg"></a></td>
  <td colspan=2><a href="valoresReferencia.php?acao=deletar&ite_codigo=<?=$ite_codigo;?>&vlr_codigo=<?=$registro[0];?>&txa_codigo=<?=$txa_codigo;?>"><img src="../../WebSocialComum/imgs/apagar_on.jpg"></a></td>
</tr>
		<?php endwhile; ?>
	