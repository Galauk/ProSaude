<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script>
	function validaDados(){
		var di = document.getElementById("data_inicial").value;
		var df = document.getElementById("data_final").value;
		var separaData = di.split('/');
		var dataInvertidaInicial = separaData[2]+separaData[1]+separaData[0];
		
		var separaDataFinal = df.split('/');
		var dataInvertidaFinal = separaDataFinal[2]+separaDataFinal[1]+separaDataFinal[0];
		if(di != null && di != ""){

			if(dataInvertidaInicial > dataInvertidaFinal){
				alert('A data final e menor que a data inicial');
				return false;
			}
		}
		abreRelatorio(di,df);
		
	}
	function abreRelatorio(di,df){
		var uni_codigo = document.getElementById("uni_codigo").value;
		url = "relVacinasAplicadas.php?di="+di+"&df="+df+"&uni_codigo="+uni_codigo;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>

<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";	
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
$data= date("d/m/Y");
echo $common->incJquery();

echo $common->menuTab(array("Relat&oacute;rio Geladeira"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","relGeladeira");
		echo $form->hiddenForm("acao","gerar");
		$sqlUnidade = "select * from unidade where uni_cnes is not null";
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade);
		
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo "<div style='clear:both'>";
		echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaDados()\"");
		echo "</div>";
	echo $form->closeForm();
echo $common->closeTab();
?>