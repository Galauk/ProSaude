<script>
	function enviaDados(){
		document.grupos.submit();
	}
</script>
<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";

	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
	echo $common->menuTab(array("Cadastro de Liberação de Laudo"));
	echo $common->bodyTab("1");
	$_SESSION["age_cod_exame"] = $_GET["age_codigo"];
	
	if($acao == "libera_automatico"){
		$stmt = "UPDATE agenda_itens SET usr_codigo_bioquimico = '".$id_login."' WHERE agei_codigo = '".$agei_codigo."'";
		$msg = "Inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","post","grupos");
		echo $form->hiddenForm("acao", "add");
		echo $form->hiddenForm("agei_codigo",$_GET["agei_codigo"],"agei_codigo");
		echo $form->hiddenForm("age_codigo",$_GET["age_codigo"],"age_codigo");
		$sqlBioquimicos = "SELECT usr_codigo, usr_nome FROM usuarios WHERE usr_tipo_medico = 'B'";
		$sqlBioqResp = "SELECT usr_codigo_bioquimico FROM agenda_itens WHERE agei_codigo = '".$_GET["agei_codigo"]."'";
		$queryBioResp = pg_query($sqlBioqResp);
		$rowBioResp = pg_fetch_array($queryBioResp);
		$codBioResp = $rowBioResp["usr_codigo_bioquimico"]; 
		echo $form->inputSelect("usr_codigo_bioquimico","","Bioquímico Responsável",$sqlBioquimicos,null,null,$codBioResp)."<br/>";
			echo "<div style='clear:both; width:406px; height:30px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"enviaDados();\"");
				echo"</div>";
			echo"</div>";
	}
	if($acao == "add"){
		$stmt = "UPDATE agenda_itens SET usr_codigo_bioquimico = '".$_POST["usr_codigo_bioquimico"]."' WHERE agei_codigo = '".$_POST["agei_codigo"]."'";
		$msg = "Inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	if($stmt){
		if(pg_query($stmt)){
			echo "<script type='text/javascript'>window.opener.location.href = 'exa_digitacaoresultado2.php?age_codigo=".$age_codigo."';</script>";
			echo "<script type='text/javascript'>window.close();</script>";
		}else{
			echo $common->modalMsg("ERRO", "$msgErr",$PHP_SELF,$stmt);
		}
	}
	
	