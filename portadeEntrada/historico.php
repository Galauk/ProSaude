<?php

	require_once '../global.php';
	
	$usu_codigo = $_GET['usu_codigo'];
	
?><html>
<head>
<?php

	$common = new commonClass();
	$table = new tableClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/portadeentrada/estilo.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();

	// busca as consultas
    $sql = "SELECT age.age_codigo,
			       to_char(age.age_data, 'dd/mm/YYYY') AS age_data,
			       age.age_horario,
			       age.age_atendido,
			       usr.usr_nome,
			       age.uni_codigo,
			       esp.esp_nome,
       			   uni.uni_desc
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo=age.med_codigo
			  JOIN especialidade AS esp
			    ON esp.esp_codigo=age.esp_codigo
			  JOIN unidade AS uni
			    ON uni.uni_codigo=age.uni_codigo
			 WHERE age.usu_codigo = $usu_codigo
			 ORDER BY age.age_data DESC, age.age_hora DESC";
    $queryConsulta = pg_query($sql);

	// busca os exames
    $sql = "	SELECT m.med_nome AS medico,
				       p.proc_nome AS procedimento,
				       to_char(e.agexl_data,'DD/MM/YYY') as data,
				       e.agexl_status AS status,
				       u.usr_nome AS usuarios
				  FROM agendamento_exame_lista AS e
				  JOIN medico AS m
				    ON m.med_codigo=e.med_codigo
				  JOIN usuarios AS u
				    ON u.usr_codigo=e.usr_codigo_alt
				  JOIN procedimento AS p
				    ON p.proc_codigo=e.proc_codigo
				 WHERE e.usu_codigo=$usu_codigo
				 ORDER BY e.agexl_data DESC";
    $queryExames = pg_query($sql);

	// busca os medicamentos
    $sql = "  SELECT to_char(m.mov_data,'DD/MM/YYYY') as data,
			         p.pro_nome AS produto,
			         i.ite_quantidade AS qtd,
			         s.set_nome||'/'||u.uni_desc AS setor
				FROM itens_movimento AS i
				JOIN movimento AS m
				  ON m.mov_codigo=i.mov_codigo
				JOIN produto AS p
				  ON p.pro_codigo=i.pro_codigo
				JOIN setor AS s
				  ON s.set_codigo=m.set_saida
				JOIN unidade AS u
				  ON u.uni_codigo=s.uni_codigo
				WHERE m.usu_codigo=$usu_codigo
				ORDER BY mov_data DESC";
    $queryMedicamentos = pg_query($sql);
    
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
		<?=$common->menuTab(array("Consultas","Exames","Medicamentos"));?>
		<?=$common->bodyTab('1'); ?>
		<div style="height:140px;overflow:auto;">
			<?php if(pg_num_rows($queryConsulta)): ?>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Data</th>
					<th>Hora</th>
					<th>Situação</th>
					<th>Especialidade</th>
					<th>Profissional</th>
					<th>Unidade</th>
				</tr>
				<?php while($r = pg_fetch_array($queryConsulta)):
					
				?>
				<tr class="situacao<?=$r['age_atendido'];?>">
					<td class="ui-widget ui-widget-content"><?=$r['age_data'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['age_horario'];?></td>
					<td class="ui-widget ui-widget-content"><?=$arrSituacoes[$r['age_atendido']];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['esp_nome'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['usr_nome'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['uni_desc'];?></td>
				</tr>				
				<?php endwhile; ?>
			</table>
			<?php else: ?>
			<em>Não há histórico de consultas</em>
			<?php endif;?>
		</div>
		
		<?=$common->closeTab();?>
		<?=$common->bodyTab('2');?>
		<div style="height:140px;overflow:auto;">
			<?php if(pg_num_rows($queryExames)): ?>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Data</th>
					<th>Procedimento</th>
					<th>Laboratório</th>
					<th>Usu. Cadastro</th>
				</tr>
				<?php while($r = pg_fetch_array($queryExames)): ?>
				<tr class="situacao<?=$r['age_atendido'];?>">
					<td class="ui-widget ui-widget-content"><?=$r['data'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['procedimento'];?></td>
					<td class="ui-widget ui-widget-content" nowrap="nowrap"><?=$r['medico'];?></td>
					<td class="ui-widget ui-widget-content" nowrap="nowrap"><?=$r['usuarios'];?></td>
				</tr>				
				<?php endwhile; ?>
			</table>
			<?php else: ?>
			<em>Não há histórico de exames</em>
			<?php endif;?>
		</div>
		<?=$common->closeTab();?>
		<?=$common->bodyTab('3');?>
		<div style="height:140px;overflow:auto;">
			<?php if(pg_num_rows($queryMedicamentos)): ?>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Data</th>
					<th>Quant.</th>
					<th>Medicamento</th>
					<th>Setor/Unidade</th>
				</tr>
				<?php while($r = pg_fetch_array($queryMedicamentos)): ?>
				<tr class="situacao<?=$r['age_atendido'];?>">
					<td class="ui-widget ui-widget-content"><?=$r['data'];?></td>
					<td class="ui-widget ui-widget-content"><?=number_format($r['qtd'],0,".",",");?></td>
					<td class="ui-widget ui-widget-content" nowrap="nowrap"><?=$r['produto'];?></td>
					<td class="ui-widget ui-widget-content" nowrap="nowrap"><?=$r['setor'];?></td>
				</tr>				
				<?php endwhile; ?>
			</table>
			<?php else: ?>
			<em>Não há histórico de medicamentos</em>
			<?php endif;?>
		</div>
		<?=$common->closeTab();?>
	</body>
</html>
	