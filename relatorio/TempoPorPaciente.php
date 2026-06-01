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
 	     	        <td colspan=2><font size=2 face=courier> TEMPO DE ESPERA DO PACIENTE PARA SER ATENDIDO </font></td>
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
			<font size='2' face='courier'>";
			//$select = pg_query("select * from agendamento where med_codigo = $_GET[med_codigo]");

		if($dt_inicial == ''){
			$dt_inicial = date('d/m/Y');
		}

	    $select = " SELECT a.usu_codigo, u.usu_nome, to_char(a.age_data, 'dd/mm/YYYY') as age_data, 
				to_char(a.age_data_atend, 'dd/mm/YYYY HH24:MI:SS') as age_data_atend,
				a.age_hora, m.med_nome, uni_desc,
				age_data_atend - ( age_data || ' ' || COALESCE(age_hora,'00:00') )::timestamp AS espera

			FROM agendamento AS a

				LEFT JOIN medico AS m ON (m.med_codigo=a.med_codigo)
				LEFT JOIN usuario AS u ON (u.usu_codigo=a.usu_codigo)
				LEFT JOIN unidade AS uni ON (uni.uni_codigo=a.uni_codigo) 

			WHERE";
			if($dt_inicial != '' AND $_GET[dt_final] != '' ){
				$select.= " a.age_data_atend BETWEEN '$dt_inicial' AND '$_GET[dt_final]' ";
			}
			if($dt_inicial != '' AND $_GET[dt_final] == ''){
				$select.= " a.age_data_atend >= '$dt_inicial' ";
			}
			if($dt_inicial == '' AND $_GET[dt_final] != ''){
				$select.= " a.age_data_atend >= '$_GET[dt_final]' ";				
			}
			if($_GET[uni_codigo] != '' ){
				$select.= " AND a.uni_codigo='$_GET[uni_codigo]' ";
			}

			$select.= " ORDER BY 6";
# echo $select;
			$query = pg_query($select);
			//echo "select esp_nome from especialidade where esp_codigo = $_GET[esp_codigo]";
			$linha = pg_fetch_array($query);
			/*if($linha[0])
			{
				echo $linha[0];
			} else {
				echo "TODAS";
			}*/
			echo "</font></td>
				</tr>
				</table>
				</td>
	    	       </tr>
 	              </table><br>";
 	    echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
			echo "<tr>";
				echo "<th align=left>";
					echo "MEDICO";
				echo "</th>";
				echo "<th align=left>";
					echo "PACIENTE";
				echo "</th>";
				echo "<th align=left>";
					echo "UNIDADE";
				echo "</th>";
				echo "<th align=left>";
					echo "HORARIO DE CONSULTA";
				echo "</th>";
				echo "<th align=left>";
					echo "TEMPO DE ESPERA";
				echo "</th>";
			echo "</tr>";
/*			$sql = "select med_nome, usu_nome, to_char(ate_data, 'dd/mm/yyyy') as data
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
*/
			//echo $sql;
			//echo pg_last_error($db);
			$medico_anterior = "";
			$i = 0;
			$controle = 0;
//			while($linha = pg_fetch_array($exec_sql))
			while($linha = pg_fetch_array($query))
			{
   $sep = explode(':',$linha[espera]);
   $horario_espera = $sep[0].":".$sep[1];
  if($horario_espera>"00:00") {
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
								echo "QUANTIDADE DE ATENDIMENTOS:";
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
					echo "</td>";
					echo "<td>";
						echo $linha[usu_nome];
					echo "</td>";
					echo "<td>";
						echo $linha[uni_desc];
					echo "</td>";
					echo "<td ALIGN=CENTER>";
						echo $linha[age_hora];
					echo "</td>";
					echo "<td align=center>";
						echo $horario_espera;
					echo "</td>";
				echo "</tr>";
				$medico_anterior = $linha[med_nome];
			}
}
		echo "</table>";
?>
