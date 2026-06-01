<?php
include_once "../global.php";
//$db = pg_connect("host=189.75.189.51 dbname=historico user=postgres port=5432 password=gvw60!@.5A");
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->menuTab(array("Relatório de faixa etária por equipe"));
echo $common->bodyTab();
$sqlUnidade = "SELECT uni_codigo, uni_desc FROM unidade WHERE uni_cnes is not null ORDER BY uni_desc";
$faixa = array(
	'menos que 1',
	'1 - 4',
	'5 - 9',
	'10 - 14',
	'15 - 19',
	'20 - 39',
	'40 - 49',
	'50 - 59',
	'60 e mais'
);
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
});

function abreRelatorio(){
	var uni_codigo = $("#uni_codigo").val();
	var f = $("#faixa").val();
	url = "relatorio_etaria_unidade.php?uni_codigo="+uni_codigo+"&f="+f;
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
<form method="POST" name="grupos_cid">
	<input type="hidden" name="acao" value="gerar">	
	<div id="bloco">
		<div id="pac-dados">
			<?= $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,'TODOS'); ?>
			<?= $form->inputSelect("faixa",$faixa,"Faixa Etária",null,null,null,null,null,'TODOS'); ?>
			<?php
				echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("voltar", "javascript:window.history.go(-1)", "voltar.png");
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
	
