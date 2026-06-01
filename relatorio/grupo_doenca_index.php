
<?php
	include_once "../global.php";
	//$db = pg_connect("host=189.75.189.51 dbname=historico user=postgres port=5432 password=gvw60!@.5A");
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->menuTab(array("Historico"));
		echo $common->bodyTab();
?>

<link rel="stylesheet" href="../lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="../lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="../../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="../lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../../WebSocialComum/library/js/jquery.shortcuts.min.js"></script>
<script type="text/javascript" src="../../WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript" src="../../WebSocialComum/library/js/ajax_motor.js"></script>
<script>

$(function(){
	$("#tabs").tabs();
	
	$("#buscar").buscar({
		tipo:'cd10',
		callback: function(event,ui){
			$("#parte2").show("");
			
		}
	});
});

	function chamaVinculacoes(gd_descricao,gd_codigo,acao,codigo){
		if(acao == ""){
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>gruposComCid.php?gd_descricao="+gd_descricao+"&gd_codigo="+gd_codigo;
		}else{
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>gruposComCid.php?gd_descricao="+gd_descricao+"&gd_codigo="+gd_codigo+"&cd10_codigo="+codigo+"&acao="+acao;
		}
		ajax_tudo(url,retornaVinculacao);
	}
	
	function retornaVinculacao(txt){
		resp = txt.split('|');
		resposta = resp[0];
		qtde = resp[1];
		div = document.getElementById('hide');
		div.style.display = "block";
		div.innerHTML = resposta;
		if (qtde == 0){
			//setTimeout("location='vincular_grupos_doencas.php'", 0);
		}
	}

	function abreRelatorio(){
		var cd10_codigo = $("#cd10_codigo").val();
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();
		var gd_codigo = $("#gd_codigo").val();
		if(cd10_codigo == ""){
			cd10_codigo = 0;
		}
		url = "relatorio_grupo_doenca.php?di="+di+"&df="+df+"&cd10_codigo="+cd10_codigo+"&gd_codigo="+gd_codigo;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
	 function Ajusta_Data(input, evnt){
		 //Ajusta m�scara de Data e s� permite digita��o de n�meros
		 	if (input.value.length == 2 || input.value.length == 5){
		 			input.value += "/";
		 	}
		 	return Bloqueia_Caracteres(evnt);
		 }
	 function Bloqueia_Caracteres(evnt){
		 if ((evnt.charCode < 48 || evnt.charCode > 57) && evnt.keyCode == 0){
		 	return false
		 }
	}
</script>
	
			<input type="hidden" id="pro_codigo" />
			<input type="hidden" id="pro_nome" />
			<form method="POST" action="vincular_grupos_doencas.php" name="grupos_cid">
				<input type="hidden" name="cd10_codigo" id="cd10_codigo" />
				<input type="hidden" name="acao" value="gerar">
 				
				<div id="bloco">
					<div id="pac-dados">
						<?=$form->inputText('buscar', $valor,'Buscar','60');?>
						<?//$form->inputText('usu_nome', $valor,'Nome do Paciente','60',NULL,null,NULL,"S");?>
						<?=$form->inputText("cd10_descricao", $valor,"CID", 60, NULL, "onChange=\"return buscaDispensados(this.value)\"");?>
						<?php $sqlGrupos = "SELECT * FROM grupo_doencas";?>
						<?=$form->inputSelect("gd_codigo",null,"Grupo de Doen&ccedil;as",$sqlGrupos); 
						echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
						echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");?>
						<?php
							echo "<div style='clear:both; width:400px; border:solid 0px;'>";
								echo"<div style='float:right; width:205px;'>";		
									echo $common->commonButton("voltar", "vincular_grupos_doencas.php", "voltar.png");
								echo"</div>";
								echo"<div style='float:right'>";
									echo $common->commonButton("gerar relatorio","","report.png","onClick=\"abreRelatorio()\"");
								echo"</div>";
							echo"</div>";
						?>
					</div>	
				</div>
				<div class="clear"></div>
			</form>
<?php 
		echo $common->closeTab();
		//echo "<pre>".print_r($_POST,1);
?>
	
