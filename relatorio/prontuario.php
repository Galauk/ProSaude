<?php 
require_once '../global.php';
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';
?>
<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?php

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script language="JavaScript" type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>

$(function(){
	
	$("#usu_nome").buscar();
});


	function validaDados(){
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();

		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
		
	}
	
	function abreRelatorio(di,df){
		var usu_codigo = $("#usu_codigo").val();
		if(usu_codigo == 0){
			alert("Informe o nome do paciente");
			$("#usu_nome").select();
			return false;
		}

		var op = [];
		var td = [];
		$("input:checked").each(function(){
			op.push(this.value);
		});
		$("input:checkbox").each(function(){
			td.push(this.value);
		});

		op = op.join(",");
		td = td.join(",");

		var opcoes = (op==td?"":"/opcoes/"+op);
		
		if(di != ""){
			di = "/de/"+di.replace(/\//g,"-");
		}
		if(df != ""){
			df = "/ate/"+df.replace(/\//g,"-");
		}
		url = "/WebSocialSaude/zf/relatorio/prontuario/id/"+usu_codigo+di+df+opcoes;
		window.open(url,null,"height=800,width=900,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Prontuário por Paciente"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
	$opcoes = array("alertas","atendimentos","exames","procedimentos","medicamentos","vacinas");
		
		echo $form->hiddenForm("usu_codigo", 0);
		echo $form->inputText("usu_nome",null,"Paciente", 70);
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
?>						<div class="linha">
						    <div class="cL0"></div>
						    <div class="cL1"><img src="/WebSocialSaude/imgs/cap01.png"></div>
						    <div class="cL2"> Opções:</div>

						    <div class="cL3"><img src="/WebSocialSaude/imgs/cap02.png"></div>
						    <div class="cL4"></div>
						    <div class="cL5">
						    	<?php foreach($opcoes as $opcao){
						    	
						    		echo "<input type=\"checkbox\" value=\"$opcao\" name=\"op\" checked=\"checked\" id=\"op_$opcao\" /> <label for=\"op_$opcao\">".ucfirst($opcao)."</label><br />";
						    		
						    	}?>
							</div>
					 </div><?php 
		echo "<div style='clear:both;height:75px;'></div>";
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaDados()\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("Voltar", "../rel_index.php", "salvar.gif", null);
				echo"</div>";
		echo"</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>
	</body>
</html>