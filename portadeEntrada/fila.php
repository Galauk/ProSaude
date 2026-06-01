<?php

@header('Content-Type: text/html; charset=ISO-8859-1');
require_once '../global.php';
require_once COMUM . "/library/php/funcoes.db.php";
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass(FALSE);

$hoje = date("d/m/Y");
$uni_codigo = getUnidadeByLogon();

$sql = "SELECT 
(SELECT EXTRACT(HOURS FROM (to_timestamp(current_date || ' ' || current_time, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP)))as horas , 
(SELECT EXTRACT(MINUTE FROM (to_timestamp(current_date || ' ' || current_time, 'YYYY-MM-DD HH24:MI')-to_char(age_data_atend, 'YYYY-MM-DD HH24:MI')::TIMESTAMP)))as minutos, 

a.age_codigo, 
	pc_clas_risco, 
	u.usr_codigo, 
	u.usr_nome, 
	usu.usu_nome, 
	a.age_horario as age_hora, 
	a.age_tipo, 
	a.age_item, 
	e.esp_nome, 
	a.age_atendido, 
	to_char(usu_datanasc,'DD/MM/YYYY')as datanasc,
	age_ordem,
	0 as io_situacao_internacao
  FROM agendamento AS a 
  LEFT JOIN pre_consulta pre 
    ON pre.age_codigo = a.age_codigo 
  JOIN usuarios AS u 
    ON u.usr_codigo=a.med_codigo 
  JOIN especialidade AS e 
    ON e.esp_codigo=a.esp_codigo 
  JOIN usuario AS usu 
    ON usu.usu_codigo=a.usu_codigo 
 WHERE age_data='$hoje' 
   AND a.uni_codigo=$uni_codigo 
   AND a.age_atendido <> 'A'
   ORDER BY med_codigo,age_ordem,age_codigo";

   /*UNION ALL
   SELECT a.age_codigo, 
	pc_clas_risco, 
	u.usr_codigo, 
	u.usr_nome, 
	usu.usu_nome, 
	a.age_horario as age_hora, 
	a.age_tipo, 
	a.age_item, 
	e.esp_nome, 
	a.age_atendido, 
	to_char(usu_datanasc,'DD/MM/YYYY')as datanasc,
	age_ordem,
	io_situacao_internacao
	
  FROM agendamento AS a 
  LEFT JOIN pre_consulta pre 
    ON pre.age_codigo = a.age_codigo 
  JOIN usuarios AS u 
    ON u.usr_codigo=a.med_codigo 
  JOIN especialidade AS e 
    ON e.esp_codigo=a.esp_codigo 
  JOIN usuario AS usu 
    ON usu.usu_codigo=a.usu_codigo 
  JOIN atendimento ate
    ON ate.age_codigo = a.age_codigo
 JOIN atendimento_internacao atei
    ON ate.ate_codigo = atei.ate_codigo
  LEFT JOIN internacao_observacao io
    ON io.io_codigo = atei.io_codigo
 WHERE age_data='$hoje' 
   AND a.uni_codigo=$uni_codigo 
 ORDER BY pc_clas_risco, 
	  age_codigo, 
	  usr_nome, 
	  age_ordem";
*/
//fdebug($sql);
//die($sql);
$query = pg_query($sql);

function openTable(){
	echo <<< TBL
	<table class="grid ui-widget ui-widget-content ui-corner-all">
		<thead>
			<tr class="ui-widget-header">
				<th>Espera</th>
				<th>Hora</th>
				<th>Tipo Atendimento</th>
				<th>Paciente</th>
				<th>Especialidade</th>
				<th>Data Nasc.</th>
				<th>Localizacao</th>
				<th>Opções</th>
			</tr>
		<thead>
		<tbody>		
TBL;
}

$tipoAt = array(
		"CB"=>"Clínica Básica",
		"ES"=>"Especialidade"
		);
		$tipoAg = array(
		"PC"=>"Agendamento",
		"AL"=>"Agendam. Livre"
		);

		//$opcoes  = '<img src="'.LINKCOMUM.'"/" class="falta-medica" alt="Falta Médica" title="Falta Médica" rel="%1$d"/> ';
		//$opcoes .= '<img src="'.LINKCOMUM.'"/" class="falta-paciente" alt="Paciente Ausentou-se" title="Paciente Ausentou-se" rel="%1$d"/>';
		$opcoes  = '<img src="'.LINKSAUDE.'/imgsBotoes/editar_on.png" class="editar" alt="Editar" title="Editar" rel="%1$d" style="cursor:pointer;"/>
					<img src="'.LINKSAUDE.'/imgsBotoes/excluir.png" class="falta" alt="Cancelar" title="Cancelar" rel="%1$d" style="cursor:pointer;"/>';
		
		// este input controla a ordem da fila
		$opcoes .= '<input type="hidden" name="ordem[]" value="%1$d" />';
		?>
<style>
	table tbody tr td { cursor: n-resize; }
</style>
<script type="text/javascript">
$(function(){

	$("#fila .abas").tabs();
	$("#novo a").focus();

	// adiciona opção "drag 'n drop" na tabela para ordenar o recepcionamento.
	$( "table tbody" ).sortable({
		revert: true,
		axis: "y",
		stop: function(e,u){
			var arr = $("input[name^=ordem]");
			var ordem = [];
			arr.each(function(){
				ordem.push(this.value);
			});

			window.console && console.log('enviando...');
			$.ajax({
				url: 'ordem.php',
				type: 'post',
				data: {
					ordem: ordem
				},
				success: function() {
					window.console && console.log('reordenado!');
				}
			});

		}
	});
	$( "td, th" ).disableSelection();

	$(".falta").click(function(){
		var age_codigo = $(this).attr("rel");
		$("#msg").dialog({
			modal: true,
			width: 300,
			height: 200,
			buttons: {
				"Não Cancelar": function(){
					$("#msg").dialog("destroy");
				},
				"Cancelar Agendamento": function(){
					var motivo = $("#msg input:checked").val();
					if(motivo.length > 0){
						$.ajax({
							url: "registrar.php",
							type: "POST",
							data: {
								age_codigo: age_codigo,
								motivo: motivo
							},
							dataType:'JSON',
							beforeSend:function(){
								$(".ui-button", $("#msg").nextAll('.ui-dialog-buttonpane')).removeClass("ui-state-enable");
								$(".ui-button", $("#msg").nextAll('.ui-dialog-buttonpane')).addClass("ui-state-disabled");
							},
							success: function(r){
								$("#msg").dialog("destroy");
								if(r.success){
									$("#tabs").tabs("load",1);
								} else {
									alert('Erro ao cancelar');
								}
							}
						});
					}
				}
			}
		});
	});

	$(".editar").click(function(){
		var age_codigo = $(this).attr("rel");
		location.href = "portadeentrada2.php?age_codigo="+age_codigo;
	});	

});

function mostarAjuda(){
	$("#ajuda-dialog").dialog({
		modal: true,
		width: 300,
		height: 120,
		buttons: {
			"OK": function(){
				$("#ajuda-dialog").dialog("destroy");
			}
		}
	});
	
}
</script>
<div id="fila">
<div id="msg" title="Cancelar Agendamento" style="display: none">
	<input type="radio" name="falta" checked="checked" value="M" id="falta-m" /> <label for="falta-m">Falta Médica</label><br />
	<input type="radio" name="falta" value="P" id="falta-p" /> <label for="falta-p">Paciente Ausentou-se</label><br />
	<input type="radio" name="falta" value="C" id="falta-c" /> <label for="falta-c">Cancelar agendamento</label><br />
	<input type="radio" name="falta" value="C" id="falta-c" /> <label for="falta-c">Erro de digitação</label>
</div>
<div id="ajuda-dialog" title="Ajuda" style="display: none">
<span>Para alterar a ordem dos pacientes na fila, clique no nome do paciente e arraste até a posição desejada.</span>
</div>
<span id="novo" style="float:left"><a href="#" onclick="window.location=window.location;return false;"><?=$common->commonButton("Adicionar Novo Paciente","javascript:void(0);","adicionar.png",NULL);?></a></span>
<span style="float:left"><a href="#" onclick="mostarAjuda();"><?=$common->commonButton("Ajuda","javascript:void(0);","help.png",NULL);?></a></span> 
<div style="clear:both"></div>
<?php 
//echo "<pre>".print_r($_REQUEST,1);
$ultimoMedico = 0;
while($r = pg_fetch_array($query)){
	if($ultimoMedico != $r['usr_codigo']){
		if($ultimoMedico){
			echo "</table></div></div>"; // tabela que lista os agendamentos
			echo $common->closeTab();
		}

		$ultimoMedico = $r['usr_codigo'];
		echo "<div class=\"order\">";
		echo $common->menuTab($r['usr_nome'],NULL,"tabs_".$r['usr_codigo'],"abas");
		echo $common->bodyTab('1');
		openTable();
	}
	if($r[age_atendido]=="S") {
		$sts = "<font>Aguardando Triagem</font>";
	}
	if($r[age_atendido]=="P") {
		$sts = "<fon>Aguardando Medico</font>";
	}
	if($r[age_atendido]=="F") {
		$sts = "<font>Paciente Ausentou-se</font>";
	}
	if($r[age_atendido]=="M") {
		$sts = "<font>Falta Médica</font>";
	}
	if($r[age_atendido]=="E" or $r[age_atendido]=="I") {
		$sts = "<font>Em atendimento</font>";
	}
	if($r[io_situacao_internacao]== 1) {
		$sts = "<font>Aguardando Internacao</font>";
	}
	if($r[io_situacao_internacao]== 2) {
		$sts = "<font>Internado/Observação</font>";
	}
	if($r[io_situacao_internacao]== 3) {
		$sts = "<font>Internado/Observação</font>";
	}	
	if($r[pc_clas_risco] == 1){
		$cor = "red";
	}else if($r[pc_clas_risco] == 2){
		$cor = "GoldenRod";
	}else if($r[pc_clas_risco] == 4){
		$cor = "green";
	}else if($r[pc_clas_risco] == 5){
		$cor = "blue";
	}else if($r[pc_clas_risco] == 2){
		$cor = "#cc7000";
	}else{
		$cor = "";
	}
	echo "	<tr>
				<td style='color:$cor' width=\"120\" class=\"ui-widget ui-widget-content\" align=center><font size=2><b>".mascaraHoraEMinutos($r['horas'],$r['minutos'])."</b></font></td>
				<td style='color:$cor' width=\"40\" class=\"ui-widget ui-widget-content c\">".$r['age_hora']."</td>
				<td style='color:$cor' width=\"120\" class=\"ui-widget ui-widget-content\">".$tipoAt[$r['age_tipo']]."</td>
				<td style='color:$cor' width=\"200\" class=\"ui-widget ui-widget-content\">".$r['usu_nome']."</td>
				<td style='color:$cor' width=\"150\" class=\"ui-widget ui-widget-content\">".$r['esp_nome']."</td>
				<td style='color:$cor' width=\"150\" class=\"ui-widget ui-widget-content\">".$r['datanasc']."</td>
				<td style='color:$cor' width=\"50\" class=\"ui-widget ui-widget-content c a\"><b>$sts</b></td>
				<td width=\"50\" class=\"ui-widget ui-widget-content c a\">".sprintf($opcoes,$r['age_codigo'])."</td>
			</tr>\n";
	// E = EM ATENDIMENTO
	//P = PRE
	//A =ATENDIDO
}
if($ultimoMedico){
	echo "<tbody></table></div></div>"; // tabela que lista os agendamentos
	echo $common->closeTab();
} else {
	echo "<br /><em>Nenhum paciente na fila de espera.</em>";
}
// /fila
?></div>
