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
		if(di == "" || df == ""){
			alert("Data Invalida");
			return false;
		}

		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
	}

	function abreRelatorio(di,df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var ate_tipo = $("input[name=ate_tipo]:checked").val();
		var usr_codigo = $("#usr_codigo").val();
		var url = "abreRelatorioFaixaEtariaFeminina.php?di="+di+"&df="+df+"&usr_codigo="+usr_codigo+"&tp_rel="+tp_rel+"&ate_tipo="+ate_tipo;
		window.open(url,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Relatorio por Faixa Etaria de Idade Feminino"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		?>
		<div align="left" style=" padding: 2px!important; ">
			<label style="height: 20px; border: 0px; width: 180px; float: left;    line-height: 20px; text-align: right;     background-image: url(../imgs/cap03.png); background-repeat: repeat;font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; color: #153854;">Idade Inicial :    
			</label>
			
			<input type="text" name="data_inicial" id="data_inicial" style="background-color: #E8F4FE; border-top: 1px solid #B0CCE5;border-left: 1px solid #B0CCE5; border-bottom: 1px solid #B0CCE5; border-right: 1px solid #B0CCE5;    font-weight: bold; height: 18px;">
		</div>

		<div align="left" style=" padding: 2px!important; ">
			<label style="height: 20px; border: 0px; width: 180px; float: left;line-height: 20px; text-align: right; background-image: url(../imgs/cap03.png); background-repeat: repeat; font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 8pt; color: #153854;">Idade Final :    
			</label>
			<input type="text" name="data_final" id="data_final" style="background-color: #E8F4FE; border-top: 1px solid #B0CCE5;border-left: 1px solid #B0CCE5; border-bottom: 1px solid #B0CCE5; border-right: 1px solid #B0CCE5;    font-weight: bold; height: 18px;">
		</div>
		
		 <?
		//echo $form->inputText("data_inicial",null,"Idade Inicial",null,10,"onKeypress=\"");
		//echo $form->inputText("data_final",null,"Idade Final",null,10,"onKeypress=\"");


		echo "<div style='clear:both'>";
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaDados()\"");
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar","../rel_index.php?id_login=$id_login#tabs-16","voltar.png");
					echo"</div>";
				echo"</div>";
		
		
		echo "</div>";
		
	echo $form->closeForm();
echo $common->closeTab();
?>
	</body>
</html>
