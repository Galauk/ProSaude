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
		url = "AtendimentoUnidadeAnual.php?ano="+ano+"&usr_codigo="+usr_codigo+"&uni_codigo="+uni_codigo+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Quantidade por Atendimentos Anual por Unidade"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlMedico = "SELECT usr_codigo,usr_nome FROM usuarios WHERE usr_tipo_medico = 'M' and usr_codigo not in(1,793) ORDER BY usr_nome";
		$sqlUnidade = "SELECT uni_codigo,uni_desc FROM unidade WHERE cnes_ativo = 'A' ORDER BY uni_desc";
		$arrayTipo = array("--- TODOS ---","2014","2015","2016","2017","2018","2019");
		$arrayTipoRelatorio = array('Por Medico','Por Unidade','Por Ano','Por Unidade/Medico');

		echo $form->inputSelect("usr_codigo",null,"Profissional",$sqlMedico,null,null,null,null,"TODOS");
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,"TODOS");
		echo $form->inputSelect("ano",$arrayTipo,"Ano",null,null,null,null,null,"TODOS");
		echo $form->inputCheckboxRadio("tp_rel", null, "Filtrar Por", null, $arrayTipoRelatorio, "radio");
		
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