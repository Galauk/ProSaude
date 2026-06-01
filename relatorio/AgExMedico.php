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
		var med_codigo = $("#med_codigo").val();
		var proc_codigo = $("#proc_codigo").val();
		var agee_situacao = $("#agee_situacao").val();
		var newmed_codigo = $("#newmed_codigo").val();
		var usr_codigo = $("#usr_codigo").val();
		url = "AgendaExMedico.php?di="+di+"&df="+df+"&esp_codigo="+esp_codigo+"&tp_rel="+tp_rel+"&med_codigo="+med_codigo+"&newmed_codigo="+newmed_codigo+"&agee_situacao="+agee_situacao+"&usr_codigo="+usr_codigo;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#usr_nome").buscar({
			tipo:"usuarios",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		}),
		$("#esp_nome").buscar({
			tipo:"especialidade",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		}),
		$("#med_nome").buscar({
			tipo:"prestador",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
		}),
		$("#newmed_nome").buscar({
			tipo:"new_medico",
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback:function(){return true;}
	});
	});
	
	function clearUnidade(){
		var string  = $("#med_nome").val();
		if(string.length == 0){
			$("#med_codigo").val("");
		}
	}
	
	function clearEspecialidade(){
		var string  = $("#esp_nome").val();
		if(string.length == 0){
			$("#esp_codigo").val("");
		}
	}
	
	function clearNewMedico(){
		var string  = $("#newmed_nome").val();
		if(string.length == 0){
			$("#newmed_codigo").val("");
		}
	}
	
	function clearUsr(){
		var string  = $("#usr_nome").val();
		if(string.length == 0){
			$("#usr_codigo").val("");
		}
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Agendamento Externo por medico e especialidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
	echo $form->hiddenForm("med_codigo", "$med_codigo");
	echo $form->inputText("med_nome",$esp_nome,"Unidade de atendimento",50,null,"onchange=\"clearUnidade()\"");

	echo $form->hiddenForm("esp_codigo", "$esp_codigo");
	echo $form->inputText("esp_nome",$esp_nome,"Especialidade",50,null,"onchange=\"clearEspecialidade()\"");
		
	echo $form->hiddenForm("newmed_codigo", "$newmed_codigo");
	echo $form->inputText("newmed_nome",$newmed_codigo,"Medico Destino",50,null,"onchange=\"clearNewMedico()\"");	
	
	echo $form->hiddenForm("usr_codigo", "$usr_codigo");
	echo $form->inputText("usr_nome",$usr_codigo,"Medico Solicitante",50,null,"onchange=\"clearUsr()\"");

		$arr = array("A"=>"Agendado","1"=>"Cancelado","2"=>"Entregue","3"=>"Espera","4"=>"Falta","5"=>"Não Loc. Pac.");
		echo $form->inputSelect("agee_situacao",$arr,"Situacao",null,null,null,null,null,'TODOS');
		
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputCheckboxRadio("tp_rel",null,"Tipos de Relat&oacute;rios",null,$arrayTipoRelatorio,"radio");
		
		echo "<div style='clear:both'>";
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaDados()\"");
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar","../rel_index.php?id_login=$id_login#tabs-1","voltar.png");
					echo"</div>";
				echo"</div>";
		
		
		echo "</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>
	</body>
</html>