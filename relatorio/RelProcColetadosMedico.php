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
	function validaCampos(){
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();

		if ($("#medico").val()==0) {
			alert("Campo médico obrigatório!");
			$("#medico").focus();
			return false;
		} else {
			abreRelatorio(di,df);
		}
 	}

	function validaDados(){
		var di = $("#data_inicial").val();
		var df = $("#data_final").val();

		if( validarDatas(di,df) ){
			abreRelatorio(di,df);
		}
		
	}
	
	function abreRelatorio(di,df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		var medico = $("#medico").val();
		url = "RelatorioProcColetadosMedico.php?di="+di+"&df="+df+"&medico="+medico+"&tp_rel="+tp_rel;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}
</script>
	
	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Procedimentos Coletados por Médico Solicitante"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");
	
	$sqlMedicoI = "SELECT u.usr_codigo AS cod,
	  					  UPPER(u.usr_nome) AS nome
					FROM usuarios AS u
				   WHERE 
					u.usr_tipo_medico='M' OR
				    u.usr_tipo_medico='D'
				   ORDER BY  UPPER(u.usr_nome);";
	
	$sqlMedicoE = "SELECT med_codigo AS cod,
	                      UPPER(med_nome) AS nome
	                 FROM medico 
	                WHERE prestador_servico = 'M'
	                  AND med_nome <> ''
	                ORDER BY UPPER(med_nome)";
	$query1 = pg_query($sqlMedicoI);
	$query2 = pg_query($sqlMedicoE);
	
	$selectMedico  = "<select id=\"medico\" class=\"inputForm\">";
	$selectMedico .= "<option value=\"0\">-- SELECIONE --</option>";
	$selectMedico .= "<optgroup label=\"Internos\">";
	while($r = pg_fetch_assoc($query1)){
		$selectMedico .= "<option value=\"".$r['cod']."|1"."\">".$r['nome']."</option>";	
	}	
	$selectMedico .= "</optgroup>";
	$selectMedico .= "<optgroup label=\"Externos\">";
	while($r = pg_fetch_assoc($query2)){
		$selectMedico .= "<option value=\"".$r['cod']."|0"."\">".$r['nome']."</option>";	
	}	
	$selectMedico .= "</optgroup>";
	$selectMedico .= "</select>";
	echo $form->inputLabel("M&eacute;dico")."&nbsp;$selectMedico";
	
		echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputCheckboxRadio("tp_rel", null, "Tipo de Relatorio", null, $arrayTipoRelatorio, "radio");
		
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("gerar relatorio","","report.png","onClick=\"validaCampos()\"");
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