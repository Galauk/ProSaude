<?php

	require_once '../global.php';
	
	$usu_codigo = $_GET['usu_codigo'];
	
?><html>
<head>
<?php

	$common = new commonClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();
	
	$sqlExaAgendados = "";
	$queryExaAgendados = pg_query($sqlExaAgendados); 	// laboratório, data, tipo, usu_cad
	
	$sqlExaLiberados = "";
	$queryExaLiberados = pg_query($sqlExaLiberados);	// laboratório, data, tipo, usu_cad
	
	$sqlConAgendadas = "SELECT age.age_codigo,
						       to_char(age.age_data, 'dd/mm/YYYY') AS age_data,
						       age.age_hora,
						       age.age_atendido,
						       usr.usr_nome,
						       age.uni_codigo,
						       esp.esp_nome,
			       			   uni.uni_desc,
			       			   age.age_falta_medico
						  FROM agendamento AS age
						  JOIN usuarios AS usr
						    ON usr.usr_codigo=age.med_codigo
						  JOIN especialidade AS esp
						    ON esp.esp_codigo=age.esp_codigo
						  JOIN unidade AS uni
						    ON uni.uni_codigo=age.uni_codigo
						 WHERE age.usu_codigo = $usu_codigo
						 ORDER BY age.age_data DESC, age.age_hora DESC";
	$queryConAgendadas = pg_query($sqlConAgendadas); 	// data, hora, tipo, esp, médico, unidade
	
	$sqlEmergencia = "";
	$queryEmergencia = pg_query($sqlEmergencia);		// data, hora, unidade
	
	/*	SELECT DISTINCT(a.agex_codigo),
	       med_nome,
	       TO_CHAR(agex_data_cad,'DD/MM/YYYY') AS data,a.* 
	  FROM agendamento_exame AS a
	  JOIN agendamento_exame_lista AS i
	    ON i.agex_codigo=a.agex_codigo
	  JOIN medico AS m
	    ON m.med_codigo=a.med_codigo_responsavel
	 WHERE i.usu_codigo= 302673
	 --ORDER BY 


--select * from agendamento_exame_lista where usu_codigo=344235


--select * from agendamento_exame where usu_codigo=344235*/
	
    // agendamento.age_tipo
    $arrSituacoes = array(
    	"S"=>"Recepicionado",
    	"A"=>"Atendido",
    	"N"=>"Agendado",
    	"F"=>"Faltou",
    	"T"=>"Transferido",
    	"E"=>"Em atendimento",
    	"P"=>"Pré-consulta"
    );
	
?>
</head>
	<body style="margin:0;padding:0">
		<?=$common->menuTab(array("Exames Agendados","Exames Liberados","Consultas Agendadas","Atendimentos Emergenciais (PAM)"));?>
		<?=$common->bodyTab('1'); ?>
		
		<?=$common->closeTab()
		  .$common->bodyTab('2');?>
		
		
		<?=$common->closeTab()
		  .$common->bodyTab('3');?>
		<?php if(pg_num_rows($queryConAgendadas)): ?>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Data</th>
					<th>Hora</th>
					<th>Situação</th>
					<th>Especialidade</th>
					<th>Médico</th>
					<th>Unidade</th>
				</tr>
				<?php while($r = pg_fetch_array($queryConAgendadas)):
					if($r['age_falta_medico'] == 'S')
						$arrSituacoes['A'] = "Falta médica";
				?>
				<tr class="situacao<?=$r['age_atendido'];?>">
					<td class="ui-widget ui-widget-content"><?=$r['age_data'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['age_hora'];?></td>
					<td class="ui-widget ui-widget-content"><?=$arrSituacoes[$r['age_atendido']];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['esp_nome'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['usr_nome'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['uni_desc'];?></td>
				</tr>				
				<?php endwhile; ?>
			</table>
			<?php else: ?>
			<em>Não há consultas agendadas</em>
			<?php endif;?>
		
		<?=$common->closeTab()
		  .$common->bodyTab('4');?>
		
		
		<?=$common->closeTab();?>