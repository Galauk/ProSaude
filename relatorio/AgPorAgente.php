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
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();

		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
		
	}
	
	function abreRelatorio(di,df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var agt_codigo = $("#agt_codigo").val();
		url = "AgendaPorAgente.php?di="+di+"&df="+df+"&agt_codigo="+agt_codigo+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Agenda por Unidade de Saude"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlUnidade = "SELECT uni_codigo,
							  uni_desc 
					     FROM unidade 
						 WHERE uni_cnes is not null
						ORDER BY uni_desc";
		
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,'TODOS');
		
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