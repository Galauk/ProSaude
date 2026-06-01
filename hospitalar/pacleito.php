<?php

	include_once "../global.php";
	
?><html>
<head>
	<title>Porta de Entrada</title>
<?php
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();


/*	$("#buscar").buscar();
	$("#buscar").focus();
	callback: function(event, ui){
		var usu_codigo = $("#usu_codigo").val();
		
		if(ui.item){
			$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
			$("#final a").focus();
		}
	}
});	*/
?>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">

$(function(){
	$("#buscar").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();
			
			if(ui.item){
				$("#iframe").show("normal").find("iframe").attr("src","aih_historico.php?lei_codigo=<?=$lei_codigo?>&usu_codigo="+usu_codigo);
				//$("#iframe").show("normal").load("aih_historico.php?lei_codigo=<?=$lei_codigo?>&usu_codigo="+usu_codigo);
				$("#final a").focus();
			}
		}
	});
});
$("#buscar").focus();
</script>

	<body>
	<form method="POST" action="$PHP_SELF">
		<input type="hidden" name="usu_codigo" id="usu_codigo" />
		<input type="hidden" name="acao" id="add" />
				<?=$form->inputText('buscar', $valor,'Buscar','60');?>
				<?=$form->inputText('usu_prontuario', $valor,'Prontuario','60',NULL,NULL,NULL,"S");?>
				<?=$form->inputText('usu_nome', $valor,'Nome do Paciente','60',NULL,NULL,NULL,"S");?>
				<?=$form->inputText('usu_mae', $valor,'Nome da Mae','60',NULL,NULL,NULL,"S");?>
				<?=$form->inputText('usu_pai', $valor,'Nome do Pai','60',NULL,NULL,NULL,"S");?>
				<?=$form->inputText('usu_datanasc', $valor,'Data de Nascimento','10',NULL,NULL,NULL,"S");?>
			<div id="iframe" style='width:600px;height:200px;top:10px;position:relative'>
				<iframe name=fazer src='about:blank' frameborder=no marginheight=0 marginwidth=0 scrolling=no width='600' height='180'></iframe> 
			</div>
		</form>
		
<?php 
   if($acao=="add") {
   	
   	$sql = pg_query("insert into paciente_leito (usu_codigo,lei_codigo,CURRENT_TIMESTAP(),");
   	
   	
   	
   }

?>	
		
		