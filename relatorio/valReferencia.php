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
<?php

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>
	
	function abreRelatorio(){
		var txa_codigo = $("#txa_codigo").val();
		var sexo = $("input[name=sexo]:checked").val();
		url = "valoresReferencia.php?txa_codigo="+txa_codigo+"&sexo="+sexo;
		window.open(url,null,"height=800,width=800,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	function verificarTipo(){
		$("#div-sexo").toggle($("#txa_codigo").val() == 1);					
	}

	$(function(){
		verificarTipo();
	});

</script>
<?php 
echo $common->menuTab(array("Valores de referência"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","","onSubmit=\"abreRelatorio();return false;\"");
	
		$sqlProcedimento = "SELECT DISTINCT(t.txa_codigo),
							       proc_nome 
							  FROM valoresdereferencia AS v
							  JOIN tipodeexame AS t
							    ON t.txa_codigo=v.txa_codigo
							  JOIN procedimento AS p
							    ON p.proc_codigo=t.proc_codigo
							 ORDER BY proc_nome";
		
		echo $form->inputSelect("txa_codigo",null,"Exame",$sqlProcedimento,"onclick=\"verificarTipo()\"",null,null,null,"SELECIONE",NULL,'N','S');
		echo "<div id=\"div-sexo\">";
		echo $form->inputCheckboxRadio("sexo", 'F', "Sexo", null, $arraySexo, "radio");
		echo "</div>";
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