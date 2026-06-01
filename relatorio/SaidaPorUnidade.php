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
<script>

	function validaDados(){
	
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var usr_codigo = $("#usr_codigo").val();
		var uni_codigo = $("#uni_codigo").val();
		var ano = $("#ano").val();
		url = "rel_SaidaPorUnidade.php?ano="+ano+"&usr_codigo="+usr_codigo+"&uni_codigo="+uni_codigo+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Quantidade de Saida Por Unidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlUnidade = "SELECT uni_codigo,uni_desc FROM unidade WHERE cnes_ativo = 'A' ORDER BY uni_desc";
		$arrayTipo = array("--- TODOS ---","2014","2015","2016","2017","2018","2019");
		$arrayTipoRelatorio = array('Por Medico','Por Unidade','Por Ano','Por Unidade/Medico');

		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,"TODOS");
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
	//	echo $form->inputCheckboxRadio("tp_rel", null, "Filtrar Por", null, $arrayTipoRelatorio, "radio");
		
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
	</body>
</html>