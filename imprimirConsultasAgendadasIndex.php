<?php 

require_once 'global.php';
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';

?>
<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
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
		if(di == "" && df == ""){
			alert("Selecione uma data");
			return false;
		}
		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
		
		
	}
	function abreRelatorio(di,df){
		
		var med_codigo = $("#med_codigo").val();
		var uni_codigo = $("#uni_codigo").val();
		
		url = "imprimirConsultasAgendadas.php?di="+di+"&df="+df+"&med_codigo="+med_codigo+"&uni_codigo="+uni_codigo;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Agenda por Agente"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
		$sqlMedico = "select distinct med_codigo,
							   usr_nome
						  from usuarios u
						  join agendamento a
							on a.med_codigo = u.usr_codigo
						 where usr_tipo_medico in ('M','E','A','D')";
		
		echo $form->inputSelect("med_codigo",null,"M&eacute;dico",$sqlMedico,null,null,null,null,'TODOS');
		
		$sqlUnidade = "select uni_codigo,uni_desc from unidade";
		echo $form->inputSelect("uni_codigo",null,"Unidade",$sqlUnidade,null,null,null,null,'TODOS');
		
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		
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