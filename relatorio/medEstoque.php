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
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>
	function validaDados(){
		var df = $("#data_final").val();	
	
		if( validarData(df) ){
			abreRelatorio(df);
		} else {
			alert("Informe uma data v�lida");
			return false;
		}
		
	}
	
	function abreRelatorio(df){
		var tp_rel = $("input[name=tp_rel]:checked").val();
		//var zerado = $("input[name=zerado]:checked").val();
		var set_codigo = $("#set_codigo").val();
		var gru_codigo = $("#gru_codigo").val();
		url = "medicamentoEstoque.php?df="+df+"&gru_codigo="+gru_codigo+"&set_codigo="+set_codigo+"&tp_rel="+tp_rel;//+"&zerado="+zerado;
		window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

</script>
<?php 
echo $common->menuTab(array("Posi��o de Estoque por Data"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","","onSubmit=\"validaDados();return false;\"");
	
		$sqlSetor = "SELECT s.set_codigo, 
								   set_nome 
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							ORDER BY set_nome";
		$sqlGrupo = "SELECT gru_codigo, gru_nome FROM grupo ORDER BY gru_nome";
		
		echo $form->inputSelect("set_codigo",null,"Setor",$sqlSetor,null,null,null,null,"TODOS");
		echo $form->inputSelect("gru_codigo",null,"Grupo",$sqlGrupo,null,null,null,null,'Selecione',null,'N','S');
		echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->inputCheckboxRadio("tp_rel", null, "Tipo de Relatorio", null, array("0"=>"Sintetico"), "radio");
		//echo $form->inputCheckboxRadio("zerado", null, "Mostrar itens zerados?", null, $arraySimNao2, "radio");
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