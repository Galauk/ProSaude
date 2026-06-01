<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
		echo "<center><input type=\"button\" value=\"Imprimir\" onclick=\"javascript:window.print();this.style.display='none';\"></center><br>";
          echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=200><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td align=center><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> ENCAMINHAMENTO POR PACIENTE E M&Eacute;DICO </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td><font size=2 face=courier>PERIODO $_GET[dt_inicial] &nbsp; A &nbsp; $_GET[dt_final] </font></td>
					<td>&nbsp;</td>
 	    	       </tr>
 	    	       <tr>
					<td>
					<table>
						<tr>
				<td><font size=2 face=courier>ESPECIALIDADE:</font></td>
		        <td>
					<font size=2 face=courier>";
					$select = pg_query("select esp_nome from especialidade where esp_codigo = $_GET[esp_codigo]");
					//echo "select esp_nome from especialidade where esp_codigo = $_GET[esp_codigo]";
					$linha = pg_fetch_array($select);
					if($linha[0])
					{
						echo $linha[0];
					} else {
						echo "TODAS";
					}
				echo "</font></td>
					</tr>
					</table>
					</td>
	    	       </tr>
 	              </table><br>";
 	    echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
			echo "<tr>";
				echo "<th align=left>";
					echo "M&Eacute;DICO";
				echo "</th>";
				echo "<th align=left>";
					echo "PACIENTE";
				echo "</th>";
				echo "<th align=left>";
					echo "DATA";
				echo "</th>";
			echo "</tr>";
			$sql = "select med_nome, usu_nome, to_char(ate_data, 'dd/mm/yyyy') as data
						from atendimento, medico, usuario
						where atendimento.med_codigo = medico.med_codigo
						and atendimento.usu_codigo = usuario.usu_codigo";
			if(!empty($_GET['esp_codigo']))
			{
				$sql .= " and atendimento.esp_codigo_encaminhamento = $_GET[esp_codigo] ";
			}
			$sql .= " and atendimento.ate_data between '$_GET[dt_inicial]' and '$_GET[dt_final]'
						order by ate_data, med_nome, usu_nome";
			$exec_sql = pg_query($sql);
			//echo $sql;
			//echo pg_last_error($db);
			$medico_anterior = "";
			$i = 0;
			$controle = 0;
			while($linha = pg_fetch_array($exec_sql))
			{
				$controle++;
				echo "<tr>";
					echo "<td>";
						if($linha[med_nome] == $medico_anterior)
						{
							echo "&nbsp;";
							$i++;
						} else {
								if($controle != 1)
								{
										echo "QUANTIDADE DE ENCAMINHAMENTOS:";
									echo "</td>";
									echo "<td>";
										echo $i;
									echo "</td>";
								}
							echo "<tr>";
								echo "<td>";
									echo "&nbsp;";
								echo "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td>";
								echo $linha[med_nome];
								$i = 1;
						}
						//echo ($linha[med_nome] == $medico_anterior ? "&nbsp;" : "&nbsp;</td></tr><tr><td>".$linha[med_nome]);
					echo "</td>";
					echo "<td>";
						echo $linha[usu_nome];
						//echo $linha[data];
					echo "</td>";
					echo "<td>";
						echo $linha[data];
						//echo $linha[usu_nome];
					echo "</td>";
				echo "</tr>";
				$medico_anterior = $linha[med_nome];
			}
		echo "</table>";
?>