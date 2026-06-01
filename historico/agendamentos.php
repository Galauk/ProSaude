<?php

@header('Content-Type: text/html; charset=ISO-8859-1');
require_once '../global.php';

$usu_codigo = $_GET['usu_codigo'];
$includes = (isset($_GET['includes']) && $_GET['includes']);

$sql = "SELECT age.age_codigo,
		       to_char(age.age_data, 'dd/mm/YYYY') AS age_data,
		       age.age_hora,
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
$query = pg_query($sql);

$common = new commonClass($includes);
if($includes){
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();
}

// agendamento.age_tipo
$arrSituacoes = array(
    	"S"=>"Recepicionado",
    	"E"=>"Em atendimento",
    	"N"=>"Agendado",
    	"T"=>"Transferido",
    	"A"=>"Atendido",
    	"P"=>"Pré-consulta",
    	"F"=>"Faltou"
    	);

    	if(pg_num_rows($query)): ?>
<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
	<tr class="ui-widget-header">
		<th>Data</th>
		<th>Hora</th>
		<th>Situação</th>
		<th>Especialidade</th>
		<th>Médico</th>
		<th>Unidade</th>
	</tr>
	<?php while($r = pg_fetch_array($query)):
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