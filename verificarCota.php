<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$select = "select a.ctp_quantidade, a.ctp_periodo, b.pro_nome, c.prg_nome
					from cota_paciente a, produto b, programa_atendimento c,
					programa_produto d
					where d.pro_codigo = b.pro_codigo
					and d.prg_codigo = c.prg_codigo
					and a.prgp_codigo = d.prgp_codigo
					and usu_codigo = {$_GET[usu_codigo]}";
	$exec_select = pg_query($select);
	echo "<table>";
		echo "<tr>
					<th  style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>
						Quantidade
					</th>
					<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=20%>
						Periodo
					</th>
					<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=40%>
						Produto
					</th>
					<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=30%>
						Programa
					</th>
				</tr>";
	while($linha = pg_fetch_array($exec_select))
	{
		echo "<tr>";
			echo "<td  style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo intVal($linha[ctp_quantidade]);
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=20%>";
				echo $linha[ctp_periodo];
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=40%>";
				echo $linha[pro_nome];
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=30%>";
				echo $linha[prg_nome];
			echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
?>