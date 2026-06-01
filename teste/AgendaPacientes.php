<?php

	include_once "../global.php";
	include_once "../../WebSocialComum/library/php/funcoes.db.php";
	
	
?><html>
<head>
	<title>Porta de Entrada</title>
	


<?php
//echo "<pre>".print_r($_GET,1);
	
	$sqlAgenda = "SELECT *,
						 to_char(usu_datanasc,'DD/MM/YYYY')as data 
  				    FROM agendamento a
  				    JOIN usuario u 
  				      ON u.usu_codigo = a.usu_codigo
  			        JOIN especialidade e
  			          ON e.esp_codigo = a.esp_codigo
  				   WHERE age_codigo = $age_codigo";
	$queryAgenda = pg_query($sqlAgenda);
	$regAgenda = pg_fetch_array($queryAgenda);
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	
	echo $common->incJquery();
	
		$sqlMedico = "SELECT DISTINCT(u.usr_codigo),u.usr_nome
						FROM usuarios AS u
						JOIN medico_especialidade AS me
					  	  ON me.med_codigo=u.usr_codigo
					   WHERE u.usr_tipo_medico IN ('M','E','D','A','P')
					   ORDER BY u.usr_nome;";
		
		$optionEsp = array(
			"nome" => "especialidade",
			"valor" => $regAgenda[esp_codigo],
			"option" => ($regAgenda[esp_codigo]== null ? "Selecione um médico" : "$regAgenda[esp_nome]"),
			"disabledFirst" => ($regAgenda[esp_codigo]== null ? "S" : ""),
			"idDiv"=>null,
			"sel"=>$regAgenda[esp_codigo]
		);
		$optionMed = array(
			"nome" => "medico",
			"valor" => NULL,
			"sql" => $sqlMedico,
			"js" => NULL,
			"idDiv"=>NULL,
			"sel"=>$regAgenda[med_codigo]
		);
		
		$selectEspecialidade = $form->inputSelect($optionEsp);
		$selectMedico = $form->inputSelect($optionMed);
	
?><link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
function guiaDeComparecimento(age_codigo){
	var url ="guiaDeComparecimento.php?age_codigo="+age_codigo;
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no');
}

function editarPaciente(id){	
	usu_codigo = document.getElementById('pac_codigo').value;	
	var url ="paciente.php?acao=form&usu_codigo="+usu_codigo+"&id_login="+id+"&porta=S";
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no');
}
function wbio() {
	window.open( '../biometria/validar.php',
			 null,
			 'height=268,width=230,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
	}


function buscaEsp(){
	var url = "../selectEspDoMed.ajax.php?usr_codigo=" + $("#medico").val();
	
	$("#td-esp select")
	.html("<option>Carregando...</option>")
	.attr("disabled","disabled")
	.parents("div#td-esp")
	.load(url,function(){
		$("#td-esp select").removeAttr("disabled");

		if( $("#especialidade option").size() == 2 ){
			if($("#usu_codigo").val())
				$("#final a").focus();
			else
				$("#buscar").select();
		} else {
			$("#especialidade").focus();
		}
	});
}
$(function(){

	$("#medico").change(function(){
		buscaEsp();		
	}).focus();

	if($("#age_codigo").val() != ""){
		buscaEsp();
	}

	$("#buscar").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();

			if(ui.item){
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
			}
		}
	});

	$("#tabs").bind("tabsselect", function(event, ui) {
		var selectedTab = $("#tabs").tabs('option', 'selected');
		
		if(selectedTab == 1){
			window.location.href = window.location.href;
			event.preventDefault();
			return false;
		}
		  	
	});
			

	$("#final a").click(function(){
		if(!validarDados())
			return false;

		$.ajax({
			url: "registrar.php",
			type: "POST",
			data:{ 
				pacientes: $("#pacientes").val(),
				usr_codigo: $("#medico").val(),
				esp_codigo: $("#especialidade").val(),
				temperatura: $("#temperatura").val(),
				altura: $("#altura").val(),
				peso: $("#peso").val(),
				pressao_sistolica: $("#pressao_sistolica").val(),
				pressao_diastolica: $("#pressao_diastolica").val(),
				freq_cardiaca: $("#freq_cardiaca").val(),
				freq_respiratoria: $("#freq_respiratoria").val(),
				p_cefalico: $("#p_cefalico").val(),
				glicose: $("#glicose").val(),
				pc_saturacao: $("#pc_saturacao").val(),
				obs: $("#obs").val(), 
				pc_clas_risco: $("#pc_clas_risco").val(),
				escolha:  $("input[@name='escolha']:checked").val()
				//var var_name =
			},
			success: function(r){
				window.console && console.log("registrar.php: "+r);
			
				$('#tabs').tabs( "select" , 1 );
			}
		});
		
		return false;
	});

	function validarDados(){
		if($("#medico").val() == 0){
			alert("Selecione o médico.");
			$("#medico").focus();
			return false;
		}
		if(!$("#especialidade").val()){
			alert("Selecione a especialidade do médico.");
			$("#especialidade").focus();
			return false;
		}
		if(!$("#pacientes").val()){
			alert("Informe a quantidade de pacientes");
			$("#buscar").select();
			return false;
		}
		return true;
	}

});

function buscarPorUsuCodigo(usu_codigo){
	window.console && console.log("recebido: "+usu_codigo);
	$.ajax({
		url: '/WebSocialSaude/buscaGenerica.php?tipo=usu_cod_bio',
		datatype: 'JSON',
		type: 'GET',
		data:{
			term: usu_codigo
		},
		success: function(json){
			if (json && json[0].id) {
				for ( var i in json[0].data) {
					$("#" + i).val(json[0].data[i]);
				}
				window.console && console.log("achou: "+usu_codigo);
				var usu_codigo = $("#usu_codigo").val();
				
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
				
			} else {

			}
		}
	});
}


</script>
</head>
	<body>
	<?=$common->menuTab(array("Agendador"),"","tabs",FALSE,array(1=>"fila.php")); ?>
	<?=$common->bodyTab('1'); ?>
	
	<form method="POST" action="">
		<input type="hidden" name="usu_codigo" id="usu_codigo" value="<?=$regAgenda[usu_codigo];?>"/>
		<input type="hidden" name="age_codigo" id="age_codigo" value="<?=$regAgenda[age_codigo];?>"/>
		<label>Médico</label>
		<div id="box-select">
			<div><?=$selectMedico;?></div>
		</div>
		<label>Especialidade</label>
		<div id="box-select">			
			<div id="td-esp"><?=$selectEspecialidade;?></div>				
		</div>
		<div class="clear"></div>		
		<div class="clear"></div>
		<br>
		<label>Quantidade de agendamentos</label>
		<input type="text" name="pacientes" size="4" id="pacientes"><br><br>
		<div id="final">
			<a href='#'><input type="button" value="Adicionar"></a>
		</div>
		
	</form>
	<br><br>
	<b>Incluir Pré consulta? </b><input type="radio" name="escolha" value="S"  checked="checked">SIM
	<input type="radio" name="escolha" id="escolha" value="N">Não
	<form method="post" action="salvar">	
	<label>Temperatura <small>(ºC)</small>:</label>
	<input type="text" name="temperatura" id="temperatura" value="36" class="float focus" rel="2,1" /><br />

	<label>Peso <small>(Kg)</small>:</label>
	<input type="text" name="peso" id="peso" value="80" class="float" rel="3,2" /><br />

	<label>Altura <small>(m)</small>:</label>
	<input type="text" name="altura" id="altura" value="170" class="float" rel="1,2" /><br />

	<label>Pressão Sistólica <small>(mm/Hg)</small>:</label>
	<input type="text" name="pressao_sistolica" id="pressao_sistolica" value="110" /><br />

	<label>Pressão Diastólica <small>(mm/Hg)</small>:</label>
	<input type="text" name="pressao_diastolica" id="pressao_diastolica" value="80" /><br />

	<label>Freq. Cardíaca <small>(BPM)</small>:</label>
	<input type="text" name="freq_cardiaca" id="freq_cardiaca" value="60" /><br />

	<label>Freq. Respiratória <small>(MPM)</small>:</label>
	<input type="text" name="freq_respiratoria" id="freq_respiratoria" value="50" /><br />

	<label>Perímetro Cefálico <small>(cm)</small>:</label>
	<input type="text" name="p_cefalico" id="p_cefalico" value="80" class="float" rel="2,2" /><br />
        
        <label>Glicose <small>(mg glicose/dl)</small>:</label>
	<input type="text" name="glicose" id="glicose" value="80" class="float" rel="3,2" /><br />
        
        <label>Saturação O²:</label>
	<input type="text" name="pc_saturacao" id="pc_saturacao" value="70" class="float" rel="3,2"/><br />
	<label>Outras informações:</label>
	<div class="textarea">
		<textarea name="obs" id="obs" class="tinymce">teste</textarea>
		
	</div><br />
        <input type="radio" name="pc_clas_risco" id="pc_clas_risco" value='1' checked="checked" />
        <label style="background-color: red; color: white; width: 60px;   text-align: left; padding-left: 10px; text-align: center; "title="Emergência, necessidade de atendimento imediato(politraumatizados, queimados, TCE, desconforto respiratório grave,perfurações no peito,abdômen e cabeça, crises convulsivas...)">
            <b>IMEDIATO</b>
        </label>
        
        <input type="radio" name="pc_clas_risco" id="pc_clas_risco" value="2" />
        <label style="background-color: yellow; color: black; width: 60px; text-align: left; padding-left: 10px; " title="Urgência, atendimento mais o rápido possível(TCE leve, diminuição do nível de consiência, agitação, confusão mental, convulsão, dor torácica intensa, crise asmática, diabéticos, descompensados, desmaio, etc...) ">
            <b>20 min</b> 
        </label>
        
        <input type="radio" name="pc_clas_risco" id="pc_clas_risco" value="3"/>
        <label style="background-color: green; color: white; width: 60px; text-align: left; padding-left: 10px; " title="Prioridade não urgente(idade superio a 60 anos, pacientes escoltados, deficêntes físicos, retorno com período inferior a 24 horas, asma fora de crise, enxaqueca, dor de ouvido moderada à grave, dor abdôminal, vômitos e diarréia sem sinal de desidratação, etc...)">
            <b>60 min</b>
        </label>
        
        <input type="radio" name="pc_clas_risco" id="pc_clas_risco" value="4" />
        <label style="background-color: blue; color: white; width: 60px; text-align: left; padding-left: 10px; " title="Consultas de baixa complexidade - atendimento de acordo com o horário de chegada(queixas crônicas, curativos, resfriados, avaliação de resultados de exames. Após consulta médica o paciente é liberado)">
            <b>4 horas</b> 
        </label>
</form>
	
	<div class="clear"></div>					
	<?=$common->closeTab()?>
	
	</body>
</html>