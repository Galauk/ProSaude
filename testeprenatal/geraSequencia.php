<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
$common = new commonClass();
$table = new tableClass();
$form = new classForm();
echo $common->incJQuery();

echo $common->menuTab(array("Sequencia"));
	echo $common->bodyTab("1");
	if($acao == ""){
		echo $form->openForm("$PHP_SLEF","POST","gera_seq");
			echo $form->hiddenForm("acao","gerar");
			echo $form->inputText("ngest_descricao","$ngest_descricao","Descric&atilde;o",40);
			echo $form->inputText("ngest_numero_inicial","$ngest_numero_inicial","Sequencia inicial");
			echo $form->inputText("ngest_numero_final","$ngest_numero_final","Sequencia final");
			echo "<div style=\"clear:both;\">";
				echo $common->commonButton("Gerar Sequencia",null,"Export.png","onClick=\"document.gera_seq.submit();\"");
			echo "</div>";
		echo $form->closeForm();
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Descri&ccedil;&atilde;o","Numero inicial","Numero Final", "Numero Atual"),null,array(1,1,1,2),"S");
			$sqlSeq = "SELECT *
					     FROM numero_gestacao";
			$querySeq = pg_query($sqlSeq);
			while($linha = pg_fetch_array($querySeq)){
				echo $table->criaLinha(array("$linha[ngest_descricao]",
											 "$linha[ngest_numero_inicial]",
											 "$linha[ngest_numero_final]",
											 50,
											 $common->commonButton("apagar","geraSequencia.php?acao=apagar&ngest_codigo=$linha[ngest_codigo]&ngest_descricao=$linha[ngest_descricao]","apagar.png")
											 ),
									   array(null,null,null,250)
									   );				
			}
		echo $tabel->closeTable();
	}
	if($acao == "gerar"){
		$sql = "INSERT 
				  INTO numero_gestacao(ngest_descricao,
					   ngest_numero_inicial,
					   ngest_numero_final)
			    VALUES (".($ngest_descricao == "" ? "null" : "UPPER('$ngest_descricao')").",
			 		   $ngest_numero_inicial,
			 		   $ngest_numero_final)";
		if($query = pg_query($sql)){
			echo $common->modalMsg("OK","Salvo com sucesso !","geraSequencia.php");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar!","geraSequencia.php");
		}
	}
	if($acao == "apagar"){
		if($acao2 == ""){
			echo $common->modalConfirm("Deseja mesmo apagar o registro $ngest_descricao ?","geraSequencia.php?acao=apagar&acao2=confirm&ngest_codigo=$ngest_codigo","geraSequencia.php");
		}
		if($acao2 == "confirm"){
			$sql = "DELETE FROM numero_gestacao WHERE ngest_codigo = $ngest_codigo";
			$query = pg_query($sql);
			echo $common->modalMsg("OK","Registro apagado com sucesso!","geraSequencia.php");
		}
	}
	echo $common->closetab();


