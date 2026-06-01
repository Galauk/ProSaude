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
		var set_codigo = $("#set_codigo").val();
		var pro_codigo = $("#pro_codigo").val();
		var lote = $("#lote").val();
		url = "medPorLote.php?set_codigo="+set_codigo+"&pro_codigo="+pro_codigo+"&lote="+lote;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Medicamentos Dispensados por Lote"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlUnidade = "SELECT s.set_codigo, 
								   set_nome 
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							ORDER BY set_nome";
		
		echo $form->inputSelect("set_codigo",null,"Setor",$sqlUnidade,null,null,null,null,'TODOS');
$sqlMedicamento = "SELECT pro_codigo,
							  pro_nome 
					     FROM produto 
					     WHERE gru_codigo = '99482'
						ORDER BY pro_nome";
		echo $form->inputSelect("pro_codigo",null,"Medicamento",$sqlMedicamento,null,null,null,null,'TODOS');
		
		echo $form->inputText("lote",null,"Lote",null,10,null);
		
		echo "<div style='clear:both'>";
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("gerar relatorio","","report.png","onClick=\"abreRelatorio()\"");
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