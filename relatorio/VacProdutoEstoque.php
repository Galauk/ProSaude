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
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var pro_codigo = $("#pro_codigo").val();
		var uni_codigo = $("#uni_codigo").val();
		url = "VacinaProdutoEstoque.php?pro_codigo="+pro_codigo+"&uni_codigo="+uni_codigo+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#pro_nome").buscar({
			tipo:"vacinas",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		});
	})
</script>
<?php 
echo $common->menuTab(array("Vacinas por Produto (em estoque)"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlUnidade = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc;";
		
		echo $form->hiddenForm("pro_codigo", "$pro_codigo");
		echo $form->inputText("pro_nome",$pro_nome,"Produto",50);
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,"TODAS");
		echo $form->inputCheckboxRadio("tp_rel", null, "Tipo de Relatorio", null, array("0"=>"Sintetico", "1"=>"Analitico"), "radio");
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