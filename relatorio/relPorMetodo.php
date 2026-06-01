<?php 
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$commom = new commonClass();
	$table = new tableClass();
	$form = new classForm();
	echo $commom->menuTab(array("Tipos de Metodos"));
	echo $commom->bodyTab();
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST");
			echo $form->inputText("dt_inicial", null,"Data Inicial");
			echo $form->inputText("dt_final", null,"Data Final");
			$sqlMetodos = "SELECT tpm_codigo,
								  tpm_metodo 
							 FROM tipodemetodos";
			echo $form->inputSelect("filtro",null,"Tipos de Metodos",$sqlMetodos,null,null,null,null,"TODOS OS METODOS",null,"S");
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"validaRua();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","$PHP_SELF?busca_usuario=$busca_usuario","voltar.png");
				echo"</div>";
			echo"</div>";
		echo $form->closeForm();
	}
	echo $commom->closeTab();
?>