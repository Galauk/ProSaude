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
	function abreRelatorio(){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		url = "faixaEtaria.php?&tp_rel="+tp_rel;

		// url = "QtdConsultasPorMedico.php?di="+di+"&df="+df+"&agt_codigo="+agt_codigo+"&tp_rel="+tp_rel;

		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Faixa etaria dos Pacientes"));	
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
		
		$sqlUnidade = "SELECT uni_codigo, uni_desc 
					     FROM unidade 
						 WHERE uni_cnes is not null
						ORDER BY uni_desc";

		echo "<p>Gerar Relatorio de idades : </p> <br>";
		echo "<div style='clear:both'>";

		echo "<div style='clear:both; width:400px; border:solid 0px;' id='teste'>";
			echo"<div style='float:right; width:205px; margin-top: -2.4%; position: absolute; margin-left: 12%;'>";		
				echo $common->commonButton(" gerar relatorio","","report.png","onClick=\"abreRelatorio()\"");
		echo"</div>";
		echo"<div style='float:right; position:absolute;margin-left:25%;margin-top:-2.4%'>";
			echo $common->commonButton("voltar","../rel_index.php?id_login=$id_login#tabs-1","voltar.png");
			echo"</div>";
		echo"</div>";
		echo "</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>
	</body>
</html>