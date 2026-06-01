<?php
include_once "../global.php";
require_once "../../WebSocialComum/library/php/funcoes.inc.php";
echo "<link href='".$_SESSION[linkroot].$_SESSION[modulo]."estilo.css' rel='stylesheet' type='text/css'>";
	$sql = "SELECT i.proc_codigo as proc_c,
					proc_nome,
					c.med_codigo medico,
					a.usu_codigo,
					u.usu_nome,
					ai.agei_data,
					ai.agei_status,
					a.age_codigo,
					a.med_codigo,
					a.usr_codigo_medico,
					ai.agei_codigo,
					TO_CHAR(col.col_data_coleta,'dd/mm/yyyy') as col_data_coleta2,*				
			  FROM medico m 
			  JOIN convenio c
			    ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
			    ON i.conv_codigo = c.conv_codigo
			 left JOIN agenda_itens ai
			    ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
			    ON a.age_codigo = ai.age_codigo
			  JOIN usuario u
			    ON u.usu_codigo = a.usu_codigo
			  JOIN procedimento proc
			    ON proc.proc_codigo = i.proc_codigo
			  JOIN coleta col
			    ON col.agei_codigo = ai.agei_codigo
		      JOIN tipodeexame as tp 
			    ON tp.proc_codigo = i.proc_codigo 
			   AND a.age_codigo = $age_codigo 		
		     ORDER BY proc_nome";	
	$query = pg_query($sql);
	//$msg = "RELATORIO";

	$sqlPaciente = "SELECT i.proc_codigo as proc_c,
					proc_nome,
					c.med_codigo medico,
					a.usu_codigo,
					u.usu_nome,
					ai.agei_data,
					ai.agei_status,
					a.age_codigo,
					a.med_codigo,
					a.usr_codigo_medico,
					ai.agei_codigo,
					TO_CHAR(col.col_data_coleta,'dd/mm/yyyy') as col_data_coleta2,*				
			  FROM medico m 
			  JOIN convenio c
			    ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
			    ON i.conv_codigo = c.conv_codigo
			 left JOIN agenda_itens ai
			    ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
			    ON a.age_codigo = ai.age_codigo
			  JOIN usuario u
			    ON u.usu_codigo = a.usu_codigo
			  JOIN procedimento proc
			    ON proc.proc_codigo = i.proc_codigo
			  JOIN coleta col
			    ON col.agei_codigo = ai.agei_codigo
		      JOIN tipodeexame as tp 
			    ON tp.proc_codigo = i.proc_codigo 
			   AND a.age_codigo = $age_codigo 		
		     ORDER BY proc_nome";
	$queryPaciente = pg_query($sqlPaciente);
	//echo $sqlPaciente;
	$resPaciente = pg_fetch_array($queryPaciente);
	cabecario_guia($resPaciente[usu_nome]);
	echo "<table width='100%'  class=\"lista\">
		<tr>
			<th>Exame</th>
			<th>Data Entrega</th>
			
		</tr>";
	while($res = pg_fetch_array($query)){
		echo "<tr><td>$res[proc_nome]</td>
			  <td>$res[col_data_coleta2]</td><tr>";
	}
	echo "<tr>
			<th colspan=2>Observações</th>			
	</tr>
	<tr>
		<td colspan=2>É indispensável a apresentação desde protocolo para retirada dos exames
		<br>NOTA: Nenhum resultado será fornecido pro telefone</td>
		
	</tr>";
	echo"
	</table>
	";
		
	rodape_rel();
	
?>