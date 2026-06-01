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

	function validaDados(){
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();

		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
		
	}
	function abreRelatorio(di,df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var esp_codigo = $("#esp_codigo").val();
		var uni_codigo = $("#uni_codigo").val();
		url = "AtendimentoEspecialidade.php?di="+di+"&df="+df+"&esp_codigo="+esp_codigo+"&uni_codigo="+uni_codigo+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#esp_nome").buscar({
			tipo:"especialidade",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		});
	})
</script>
<?php 
echo $common->menuTab(array("Atendimento por Especialidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlUnidade = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc;";
		
		echo $form->hiddenForm("esp_codigo", "$esp_codigo");
		echo $form->inputText("esp_nome",$esp_nome,"Especialidade",50);
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,"TODAS");
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputCheckboxRadio("tp_rel", null, "Tipo de Relatorio", null, array("0"=>"Sintetico", "1"=>"Analitico"), "radio");
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaDados()\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("Voltar", "../rel_index.php", "voltar.png", null);
				echo"</div>";
		echo"</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>