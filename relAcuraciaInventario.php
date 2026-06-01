<?
	header('Content-Type: text/html; charset=utf-8');
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$common = new commonClass();
	
	$inv_codigo = $_GET["inv_codigo"];
	
	echo "<link href=\"estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
	$selectGrupoSetor = "SELECT s.set_nome, 
								g.gru_nome
						   FROM inventario i
						   JOIN grupo g
						     ON i.gru_codigo = g.gru_codigo
						   JOIN setor s
						     ON i.set_codigo = s.set_codigo
						  WHERE inv_codigo = $inv_codigo";
	$exec = pg_query($selectGrupoSetor);
	$row = pg_fetch_array($exec);	
	echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"javascript:this.style.display='none';window.print();\"")."</center>";

	echo "<table class=table style='font-size:14px;font-family:verdana' border=0>
			<tr>
				<td width=130><b>GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE</b></td>
				<td width=10 align=right>".date("d/m/Y h:i:s")."</td>
			</tr>
			<tr>
				<td colspan=2>".strtoupper(html_entity_decode($Tit))."</td>
			</tr>
			<tr>
				<td colspan=2>Grupo: ".strtoupper(html_entity_decode($row[gru_nome]))."</td>
			</tr>
			<tr>
				<td colspan=2>Setor: ".strtoupper(html_entity_decode($row[set_nome]))."</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
	echo "<table class=lista>
			<tr>
				<th colspan=7 style='font-size:16px;text-align:center'>
					RELAT&Oacute;RIO DE ACUR&Aacute;CIA DO INVENT&Aacute;RIO
				</th>
			</tr>";
	$select = "SELECT * 
				 FROM inventario_produto_lote_quantidade iplq
				 JOIN inventario_produto ip
				   ON ip.invp_codigo = iplq.invp_codigo
				 JOIN inventario i
				   ON i.inv_codigo = ip.inv_codigo
				 JOIN produto p
				   ON p.pro_codigo = ip.pro_codigo
				WHERE i.inv_codigo = $inv_codigo
				ORDER BY p.pro_nome";

		$exec_select = pg_query($select);
		echo "
			<tr>
				<th><b>PRODUTO</b></th>
				<th><b>LOTE</b></th>
				<th><b>VALIDADE</b></th>
				<th><b>QTDE ESTOQUE</b></th>
				<th><b>QTDE INVENT&Aacute;RIO</b></th>
				<th><b>ACUR&Aacute;CIA</b></th>
			</tr>";
		while($linha = pg_fetch_array($exec_select)){
			$invp_codigo = $linha['invp_codigo'];
			$invplq_lote = $linha['invplq_lote'];
			$invplq_validade = $linha['invplq_validade'];
			$invplq_quantidade = $linha['invplq_quantidade'];
			$pro_codigo = $linha['pro_codigo'];
			$pro_nome = $linha['pro_nome'];
			$set_codigo = $linha['set_codigo'];
			$sal_qtde = $linha['invplq_quantidade_saldo'];
			$invplq_acuracia = $linha[invplq_acuracia];
			$inv_acuracia = $linha[inv_acuracia];
			
			echo "<tr>
				<td>$pro_nome</td>
				<td>$invplq_lote</td>
				<td>".formatarData($invplq_validade)."</td>
				<td align=right>".intval($sal_qtde)."</td>
				<td align=right>".intval($invplq_quantidade)."</td>
				<td align=right>".($invplq_acuracia != 100.00 ? "<b style='color:red;'>".moeda($invplq_acuracia)." %</b>" : moeda($invplq_acuracia)." %")."</td>";
			echo "</tr>";
		}
	echo "
		<tr>
			<td colspan=4>&nbsp;</td>
			<td align=right style=\"font-size:14px;\"><b>ACUR&Aacute;CIA TOTAL</b></td>
			<td align=right style=\"font-size:14px;\"><b>".moeda($inv_acuracia)." %</b></td>
		</tr>
	</table>";
		
?>
