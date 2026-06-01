<?php

	include_once "../global.php";
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();

	$sqlMedico = "SELECT usr_codigo,usr_nome
					FROM usuarios
				   WHERE usr_tipo_medico='M'
				ORDER BY usr_nome;";
	
	$optionEsp = array(
		"nome" => "especialidade",
		"valor" => NULL,
		"option" => "Selecione um médico",
		"disabledFirst" => "S"
	);
	$optionMed = array(
		"nome" => "medico",
		"valor" => NULL,
		"sql" => $sqlMedico
	);
	
	$selectEspecialidade = $form->inputSelect($optionEsp);
	$selectMedico = $form->inputSelect($optionMed);

	echo $common->menuTab(array("Porta de Entrada"));
	echo $common->bodyTab('1');
	
?>
<script type="text/javascript">

$(function(){

	$("#medico").change(function(){
		
		var url = "selectEspDoMed.ajax.php?usr_codigo=" + $(this).val();
		
		$("#td-esp select")
		.html("<option>Carregando...</option>")
		.attr("disabled","disabled")
		.parents("td")
		.load(url,function(){
			$("#td-esp select").removeAttr("disabled");
 
			if( $("#especialidade option").size() == 2 ){
				var foco = "#usr_prontuario";
			} else {
				var foco = "#especialidade";
			}
			$( foco ).focus();
		});
		
	}).focus();

	$("#usr_prontuario").change(function(){
		$.ajax({
			url: "../buscarDadosPaciente.ajax.php",
			data: {
				usu_prontuario: $(this).val()
			},
			success: function(retorno){
				for(var campo in retorno[0]){
					$("#"+campo).val( retorno[0][campo] );
				}
				if(retorno.length){
					$("#a-final").focus();
				}

			}
		});
	});

	$("#a-final").click(function(){
		alert( "usu_codigo: "+$("#usu_codigo").val() );
	});

});

</script>
	<br><br><br>
	<div style='position: absolute; width: 766; height:20px; top: 50; left: 21;border:0px solid;' ><img src=imgs/medico.png border=0 style='margin-top:0px;'></div>
	<div style='position: absolute; width: 277; height:20px; top: 68; left: 19;border:0px solid;' >
		<table width=100% align='center' cellspacing=0 cellpadding=0 border=0>
			<tr>
			  <td width=5><img src=imgs/med_01.png></td>
			  <td bgcolor='#DDE6EF'><?=$selectMedico;?></td>
			  <td width=5><img src=imgs/med_02.png></td>
			</tr>	
		</table>		
		</div>
		
	<div style='position: absolute; width: 766; height:20px; top: 50; left: 297;border:0px solid;' ><img src=imgs/especialidade.png border=0 style='margin-top:0px;'></div>
	<div style='position: absolute; width: 320; height:20px; top: 68; left: 295;border:0px solid;' >
	<table width=100% align='center' cellspacing=0 cellpadding=0 border=0>
		<tr>
		  <td width=5><img src=imgs/med_01.png></td>
		  <td bgcolor='#DDE6EF' id='td-esp'><?=$selectEspecialidade;?></td>
  		  <td width=5><img src=imgs/med_02.png></td>
		</tr>	
	</table>		
	</div>
	<div style='position: absolute; width: 74px; height:90px; top: 100; left: 25;border:0px solid;' ><img src=imgs/sem_foto.png height='90' border=0 style='margin-top:0px;'></div>
	<div style='position: absolute; width: 766;  height:20px; top: 100; left: 99;border:0px solid;' >
		<?=$form->inputText('usr_prontuario', $valor,'Número de Prontuario','55');?>
		<a href="#" style="margin-left:40px;">
			<img src="<?=LINKCOMUM;?>/imgsBotoes/buscar.png" alt="Procurar" title="Procurar" />
		</a>	
	</div>
	<?=$form->hiddenForm("usu_codigo", "0");?>
	<div style='position: absolute; width: 766;  height:20px; top: 122; left: 99;border:0px solid;'><?=$form->inputText('usu_nome', $valor,'Nome do Paciente','60');?></div>
	<div style='position: absolute; width: 766;  height:20px; top: 145; left: 99;border:0px solid;'><?=$form->inputText('usu_mae', $valor,'Nome da Mae','60');?></div>
	<div style='position: absolute; width: 766;  height:20px; top: 168; left: 99;border:0px solid;'><?=$form->inputText('usu_pai', $valor,'Nome do Pai','60');?></div>
	<div style='position: absolute; width: 766;  height:20px; top: 191; left: 99;border:0px solid;'><?=$form->inputText('usu_datanasc', $valor,'Data de Nascimento','10');?></div>
	<div style='position: absolute; width: 612;  height:180px; top: 205; left: 15;border:0px solid;' id="iframe" ><iframe name=fazer src='historico.php' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=100% height=180></iframe></div>
	<div style='position: absolute; width: 338px; height:36px; top: 377; left: 27;border:0px solid;' ><a href='#' id='a-final'><img src=imgs/addpacatend.png border=0 style='margin-top:0px;'></a></div>

	<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
 

		
<?=$common->closeTab();?>