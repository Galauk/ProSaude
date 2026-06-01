<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
 	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
 	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

	$common = new commonClass();
	echo $common->incJquery();
	$form = new classForm();
	$table = new tableClass();
?>
<script>
	var dtInicial
	var dtFinal
	var codSetor
	var codProduto
	
	function VerData() {
	
		dtInicial = document.frm_PosEstLocalEstoq.dt_inicial.value;
		dtFinal   = document.frm_PosEstLocalEstoq.dt_fim.value;
		codSetor  = document.frm_PosEstLocalEstoq.set_codigo.value;
		codGrupo  = document.frm_PosEstLocalEstoq.gru_codigo.value;
	
		if (dtFinal == '') {
			alert ("Data Estoque INV&Aacute;LIDO!");
			document.frm_PosEstLocalEstoq.dt_fim.focus();
			return false;
		}
		if (codGrupo == ''){
			alert("Escolha o grupo de produtos!");
			document.frm_PosEstLocalEstoq.gru_codigo.focus();
			return false;
		}
		if (codSetor == ''){
			alert("Escolha o setor!");
			document.frm_PosEstLocalEstoq.set_codigo.focus();
			return false;
		}
		window.open('relatorio/EstoqueInventario.php?' +
	                                     	'dt_final=' + dtFinal     +
	                                      	'&set_codigo=' + codSetor    +
	                                      	'&gru_codigo=' + codGrupo    
	                                      , null 
	                                      ,"height=400,width=750,status=yes,resizable=yes, toolbar=no,menubar=no,location=no,scrollbars=yes");
	}
</script>

<?
$data = date("d/m/Y");

$stmt = "SELECT uni_codigo 
		   FROM usuarios 
		  WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

echo " <link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo $common->menuTab(array("Lista para Contagem de Produtos do Invent&aacute;rio"));
echo $common->bodyTab(1);

	echo $form->openForm("$PHP_SELF", "POST", "frm_PosEstLocalEstoq");
		echo $form->hiddenForm("id_login", $id_login);
		echo $form->hiddenForm("dt_inicial", "01-01-1901");
		echo $form->inputText("dt_fim", $data, "Data", 12, 10, "onKeypress='return Ajusta_Data(this, event);'");
		$query = "SELECT gru_codigo, 
						 gru_nome 
					FROM grupo 
				   ORDER BY gru_nome";
		echo $form->inputSelect("gru_codigo", null, "Grupo de Produto", $query, null, null, $gru_codigo, "style=\"width:252px;\"");			
		$sql = "SELECT set_codigo, 
					   set_nome
				  FROM Setor
				 WHERE set_estoque = 'S' "
	             .($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
			   " ORDER BY set_nome";
		echo $form->inputSelect("set_codigo", null, "Setor", $sql, null, null, $set_codigo, "style=\"width:252px;\"");
		echo "<div style=clear:both>";
			echo $table->openTable(null);
				$arrayBotoes = array($common->commonButton("Voltar", "../inventario.php?id_login=$id_login", "voltar.png"), $common->commonButton("Gerar Relat&oacute;rio", null, "report.png", "onClick=\"VerData();\""));
				echo $table->criaLinha($arrayBotoes);
			echo $table->closeTable();
		echo "</div>";
	echo $form->closeForm();
echo $common->closeTab();
