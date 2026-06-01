<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$data = date("d/m/Y");
	
	$sql = "select a.ite_codigo, a.pro_codigo, a.ite_quantidade, b.pro_nome, a.ite_consolidado
				from itens_movimento a, produto b
				where a.pro_codigo = b.pro_codigo
				and mov_codigo = {$_GET[mov_codigo]}
				order by b.pro_nome";
	$exec_sql = pg_query($sql);
	echo "<table width=98% cellspacing=0 cellpadding=0 border=0>";
		echo "<tr>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=35>";
				echo "C&oacute;digo";
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
				echo "Nome";
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=75>";
				echo "Quantidade";
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=140>";
				echo "Apagar";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<div>";
		echo "<table width=98% cellspacing=0 cellpadding=0 border=0>";
		while($linha = pg_fetch_array($exec_sql))
		{
			echo "<tr>";
				echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=35>";
					echo $linha[pro_codigo];
				echo "</td>";
				echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo $linha[pro_nome];
				echo "</td>";
				echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=75>";
					echo $linha[ite_quantidade];
				echo "</td>";
				if($linha[ite_consolidado] == "N")
				{
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=140>";
						echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg\" onclick=\"apagar($linha[ite_codigo], 2)\">";
					echo "</td>";
					echo "&nbsp;<a href='javascript:;' onclick=\"window.open('editar_dispensa_medicamentos.php?pro_cod=$linha[pro_codigo]&mov_cod=$_GET[mov_codigo]&ite_cod=$linha[ite_codigo]','Editar','width=550,height=470')\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg\" border=0></a>";
				}
			echo "</tr>";
		}
		echo "</table>";
	echo "</div>";
?>