<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$select = "SELECT a.age_codigo, 
					  to_char(a.age_data, 'dd/mm/yyyy') as data_agendamento, 
					  b.med_nome
				 FROM agendamento a, 
				 	  medico b, 
				 	  usuario c
				WHERE a.med_codigo = b.med_codigo
				  AND a.usu_codigo = c.usu_codigo
				  AND a.usu_codigo = {$_GET[pac_codigo]}
				  AND a.med_codigo = {$_GET[med_codigo]}
				  AND a.age_atendido = 'S'
				ORDER BY a.age_data desc";
	
	$exec_select = pg_query($select);
	
	echo "<table>";
		echo "<tr>";
			echo "<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo "Data";
			echo "</th>";
			echo "<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=80%>";
				echo "M&eacute;dico";
			echo "</th>";
			echo "<th style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo "Selecionar";
			echo "</th>";
		echo "</tr>";
		echo "<tr>";
			echo "<td colspan=2 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=90%>";
				echo "<b>Paciente n&atilde;o atendido por m&eacute;dico da listagem.</b>";
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo "<input type=checkbox onclick=\"passarAtendimento(0)\">";
			echo "</td>";
		echo "</tr>";
	while($linha = pg_fetch_array($exec_select))
	{
		echo "<tr>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo $linha[1];
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=80%>";
				echo $linha[2];
			echo "</td>";
			echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=10%>";
				echo "<input type=checkbox onclick=\"passarAtendimento($linha[0])\">";
			echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	
?>