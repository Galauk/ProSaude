<?php 
require_once '../global.php';
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';
?>
<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script language="JavaScript" type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>

	function validaDados(){
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();
		abreRelatorio(di,df);
		//if( validarDatas(di,df) ){
		//	abreRelatorio(di,df);
		//}
		
	}
	function abreRelatorio(di,df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var sex = $("input[name=sex_codigo]:checked").val();
		var idi = $("#idade_ini").val();
		var idf = $("#idade_fim").val();
		var uni = $("#uni_codigo").val();
		url = "rel_pacientePorUnidade.php?uni_codigo="+uni+"&idf="+idf+"&idi="+idi+"&sex_codigo="+sex+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#proc_nome").buscar({
			tipo:"procedimento",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		});
	})
</script>
<?php 
echo $common->menuTab(array("Pacientes Por Unidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");

		$sqlUnidade = "select usacodigo,usanomfan from saunisau where usacodigo in (263,1,37,365,45,33,36,34,43,35,365,347,379,37)";
		
		echo $form->inputText("idade_ini",null,"Idade Inicial",5,3,2);
		echo $form->inputText("idade_fim",null,"Idade Final",5,3,2);
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,"TODAS");
		echo $form->inputCheckboxRadio("sex_codigo", null, "Sexo", null, array("2"=>"Feminino", "1"=>"Masculino"), "radio");
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