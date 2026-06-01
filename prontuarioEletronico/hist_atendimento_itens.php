<?php
//echo"<pre>".print_r($_GET,TRUE)."</pre>";
$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
//##############################   EXAMES   ############################################################
echo $common->menuTab(Array("Exame","Atendimento","Medicamento","Alerta","Anamnese","Encaminhamento","Procedimento"));
echo $common->bodyTab('1');
echo $table->openTable("lista", "100%");
	echo $table->criaLinha(array("Data", "Médico","Procedimento","Unidade"), null, null, "S");
		$sql ="SELECT to_char(dt_requisicao ,'dd/mm/yyyy'),
					  u.uni_desc,
					  m.med_nome,
					  proc_nome						
				 FROM requisicao_exames r
				 JOIN procedimento p
				   ON r.proc_codigo = p.proc_codigo
				 JOIN atendimento at
				   ON at.ate_codigo = r.ate_codigo
				 JOIN unidade u
			 	   ON u.uni_codigo = at.uni_codigo
				 JOIN medico m
				   ON at.med_codigo  = m.med_codigo 
			    WHERE r.usu_codigo=$usu_codigo
				  AND ate_data = '$ate_data'";
	
		
		$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();
echo $common->closeTab();
#############################################  ATENDIMENTO ####################################################
echo $common->bodyTab('2');
	//echo $table->openTable("lista", "100%");
		//echo $table->criaLinha(array("Data.", "Unidade","Reclamação","Exame Físico","Tratamento"), null, null, "S");
	$sql =" SELECT 	to_char(ate_data,'dd/mm/yyyy'),
					u.uni_desc,
					ate_reclamacao,
					ate_exame_fisico,
					ate_tratamento,* 
			  FROM atendimento a
			  JOIN unidade u
			    ON u.uni_codigo = a.uni_codigo
			  JOIN medico m
			    ON a.med_codigo  = m.med_codigo
			 WHERE usu_codigo=$usu_codigo
			   AND ate_data = '$ate_data'";
	$query = pg_query($sql);
	$row=pg_fetch_array($query);
	echo"<table class='lista'>
			<tr>
				<td width='225' style='font-weight:bold;'>Medico</td>
				<td>$row[med_nome]</td>
			</tr>
			<tr>
				<td width='225' style='font-weight:bold;'>Descrição do Paciente(reclamação)</td>
				<td>$row[ate_reclamacao]</td>
			</tr>
			<tr>
				<td style='font-weight:bold;'>Exame Fisico</td>
				<td>$row[ate_exame_fisico]</td>
			</tr>
			<tr>
				<td style='font-weight:bold;'>CID:</td>
				<td>$row[cd10_codigo]</td>
			</tr>
			<tr>
				<td style='font-weight:bold;'>Diagnóstico(Descricao)</td>
				<td>$row[ate_diagnostico]</td>
			</tr>
			<tr>
				<td style='font-weight:bold;'>Conduta/Tratamento (MPM)</td>
				<td>$row[ate_tratamento]</td>
			</tr>
	
</table>";
		/*while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}*/
	//echo $table->closeTable();	
echo $common->closeTab();
#############################################  MEDICAMENTO  ####################################################
echo $common->bodyTab('3');
	echo $table->openTable("lista", "100%");
		echo $table->criaLinha(array("Medicamento", "Médico","Recomendação","Quantidade","Unidade","Data"), null, null, "S");
$sql =" SELECT pro.pro_nome, 
		       m.usr_nome, 
		       ir.irec_recomendacao, 
		       ir.irec_quantidade, 
		       u.uni_desc, 
		       to_char(a.ate_data,'dd/mm/yyyy') 
		  FROM receita r
		  JOIN atendimento a 
		    ON r.ate_codigo = a.ate_codigo 
		  JOIN unidade u
		    ON u.uni_codigo = a.uni_codigo
		  JOIN itemreceita ir
		    ON ir.rec_codigo = r.rec_codigo 
		  JOIN usuarios m
		    ON a.med_codigo = m.usr_codigo 
		  JOIN produto pro
		    ON ir.pro_codigo = pro.pro_codigo
		 WHERE usu_codigo=$usu_codigo
		   AND ate_data = '$ate_data'";

$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();	
echo $common->closeTab();
#############################################  ALERTA   ####################################################
echo $common->bodyTab('4');
	echo $table->openTable("lista", "100%");
		echo $table->criaLinha(array("Data", "Descrição"), null, null, "S");
		$sql= "SELECT to_char(alepac_data,'dd/mm/yyyy'),alerta_desc FROM alerta a
				 JOIN alerta_usuario au
				   ON a.alerta_cod = au.alerta_cod
			     WHERE au.usu_codigo = $usu_codigo
";
		$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();	
echo $common->closeTab();
#############################################  ANAMNESE   ####################################################

echo $common->bodyTab('5');
	echo $table->openTable("lista", "100%");
		echo $table->criaLinha(array("Questão", "Resposta"), null, null, "S");
		$sql= "SELECT  a.ana_questao,ma.ana_resposta from medico_anamnese ma 
			  JOIN anamnese a
			    ON a.ana_codigo = ma.ana_codigo
			    where age_codigo = $age_codigo";
		$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();	
echo $common->closeTab();
#############################################  ENCAMINHAMENTO   ####################################################

echo $common->bodyTab('6');
	echo $table->openTable("lista", "100%");
		echo $table->criaLinha(array("Especialidade","Data", "Unidade","Medico"), null, null, "S");
		$sql= "select esp.esp_nome,to_char(a.ate_data,'dd/mm/yyyy'), u.uni_desc,m.med_nome from atendimento a
				 JOIN unidade u
				   ON u.uni_codigo = a.uni_codigo
				 JOIN medico m
				   ON a.med_codigo  = m.med_codigo 
				 JOIN especialidade esp
				   ON esp.esp_codigo = a.esp_codigo_encaminhamento
				    WHERE usu_codigo=$usu_codigo
					  AND ate_data = '$ate_data'";
		$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();	
echo $common->closeTab();
#############################################  PROCEDIMENTOS   ####################################################
echo $common->bodyTab('7');
	echo $table->openTable("lista", "100%");
		echo $table->criaLinha(array("Data","Medico","Grupo","Procedimento"), null, null, "S");
		
		$sql = "select to_char(promed_data,'dd/mm/yyyy'),
				       m.med_nome,
				       g.no_grupo,
				       no_sub_grupo 
				       from procedimentos_medico pm	
					join medico m
					  on m.med_codigo = pm.med_codigo										
					join tb_sub_grupo sb
					  on sb.co_sub_grupo = pm.co_sub_grupo

					join tb_grupo g
					  on g.co_grupo = sb.co_grupo
					
				    WHERE usu_codigo=$usu_codigo
				        AND pm.co_grupo = sb.co_grupo
					  AND promed_data = '$ate_data'";
		
	/*	$sql= "select to_char(promed_data,'dd/mm/yyyy'),
				       m.med_nome,proc_nome from procedimentos_medico pm	
					join medico m
					  on m.med_codigo = pm.med_codigo
					join procedimento p
					  on p.proc_codigo = pm.proc_codigo
				    WHERE usu_codigo=$usu_codigo
					  AND promed_data = ''";*/
		$query = pg_query($sql);
		while($row=pg_fetch_row($query)) {
				//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
			echo $table->criaLinha($row, null, null, "N");
		}
	echo $table->closeTable();	
echo $common->closeTab()

?>