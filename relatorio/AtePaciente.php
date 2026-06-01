<?php 
require_once '../global.php';
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';
?>
<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>

	function abreRelatorio(){
		var usu_codigo = $("#usu_codigo").val();
		url = "AtendimentoPaciente.php?usu_codigo="+usu_codigo;
		window.open(url,null,"height=800,width=800,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#usu_nome").buscar();
	});
</script>
<?php 
echo $common->menuTab(array("Atendimento por Especialidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","","onSubmit=\"abreRelatorio()\"");
	
		$sqlUnidade = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc;";
		
		echo $form->hiddenForm("usu_codigo", "$usu_codigo");
		echo $form->inputText("usu_nome",$usu_nome,"Paciente",50);
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("gerar relatorio","","report.png","onClick=\"abreRelatorio()\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("Voltar", "../rel_index.php", "voltar.png", null);
				echo"</div>";
		echo"</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>