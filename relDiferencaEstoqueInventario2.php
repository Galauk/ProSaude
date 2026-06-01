<?
	header('Content-Type: text/html; charset=utf-8');
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    echo "<link href=\"estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
    $inv_codigo = $_GET["inv_codigo"];
	/*if($atualizar == "atualizar")
	{
		echo "<input type=\"text\" name=\"inv_codigo\" value=\"$inv_codigo\">";
		echo "<input type=\"button\" value=\"atualizar\">";
	}*/
	$sql = "SELECT *
			  FROM inventario_produto_lote_quantidade iplq
			  JOIN inventario_produto ip
				ON ip.invp_codigo = iplq.invp_codigo
			  JOIN inventario i
				ON ip.inv_codigo = i.inv_codigo
			  JOIN setor
				ON setor.set_codigo = i.set_codigo
			  JOIN grupo g
				ON g.gru_codigo = i.gru_codigo
			  JOIN produto p
				ON p.pro_codigo = ip.pro_codigo
			 WHERE ip.inv_codigo = $inv_codigo";
	$exec = pg_query($sql);
	
?>
	<center><input type="button" value="Imprimir" onclick="javascript:window.print();this.style.display='none';"></center>
<?
	$select = "select g.gru_nome, 
					  s.set_nome 
				 from inventario i 
				 join grupo g 
				   on i.gru_codigo = g.gru_codigo 
				 join setor s 
				   on s.set_codigo = i.set_codigo 
			    where i.inv_codigo = $inv_codigo";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=2 face=courier>GEST&Aacute;O P&Uacute;BLICA DE SA&Uacute;DE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> ".strtoupper(html_entity_decode($Tit))."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> Grupo: ".strtoupper(html_entity_decode($linha[gru_nome]))."</font></td>
 	    	       </tr>
					<tr>
 	     	        <td colspan=2><font size=2 face=courier> Setor: ".strtoupper(html_entity_decode($linha[set_nome]))."</font></td>
 	    	       </tr>
 	    	       <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>";
	echo "<table class=lista>";
				echo "<tr>
					<td colspan=3 style='font-size:12px;text-align:center>
						RELAT&Oacute;RIO DE INVENT&Aacute;RIO - COMPARA&Ccedil;&Atilde;O QUANTIDADE EM ESTOQUE COM QUANTIDADE DIGITADA
					</td>
				</tr>";
		echo "<tr bgcolor=#ffffff>";
			echo "<th>";
				echo "Produto";
			echo "</th>";
			echo "<th>";
				echo "Quantidade em Estoque";
			echo "</th>";
			echo "<th>";
				echo "Quantidade em Invent&aacute;rio";
			echo "</th>";
			echo "<th>";
			echo "Entrada";
			echo "</th>";
			echo "<th>";
			echo "Saida";
			echo "</th>";

		echo "</tr>";
	while($row = pg_fetch_array($exec))
	{
			echo "<tr>";
				echo "<td>";
					echo $row[pro_nome];
				echo "</td>";
				echo "<td>";
					$seleciona = "SELECT sal_qtde 
								    FROM saldo 
								   WHERE pro_codigo = $row[pro_codigo] 
								     AND sal_lote = $row[invplq_lote]
									 AND sal_validade = $row[invplq_validade]";
					$exec_sel = pg_query($seleciona);
					$dado = pg_fetch_array($exec_sel);
					echo intval($dado[sal_qtde]);
					//echo " - ".$row[estoqueatual];
				echo "</td>";
				echo "<td>";
					echo $row[invp_quantidade];
				echo "</td>";
				if ($row[estoqueatual] > $row[invp_quantidade]) {
				   $dif = $row[estoqueatual] - $row[invp_quantidade];
				   echo "<td>";
     			   echo "&nbsp;&nbsp;";
				   echo "</td>";
				   echo "<td>";
  				   echo intval($dif);
				   echo "</td>";
			    }
			    else { 
				   $dif = $row[invp_quantidade] - $row[estoqueatual];
                   echo "<td>";
                   echo intval($dif);
				   echo "</td>";
				   echo "<td>";
     			   echo "&nbsp;&nbsp;";
				   echo "</td>";
			    }

  	  echo "</tr>";
	}
	echo "<table>";
?>
