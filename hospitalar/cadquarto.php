<script>
	function validaOrientacoes(){
		var qua_numero = document.getElementById("qua_numero").value;
		if(qua_numero == ""){
			alert("Preencha o campo Numero do Quarto !");
			return false;
		}else{
			document.orientacoes.submit();
		}
		
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
		
	echo $common->menuTab(array("Cadastro de Quartos"));
	echo $common->bodyTab("1");
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST","busca");
			echo $form->hiddenForm("busca","busca");
			echo $form->hiddenForm("buscaEfetuada", "S");
			if($buscaEfetuada == "S"){
				if($ativo == ""){
					$ativo = "N";
				}else{
					$ativo = "S";
				}
			}else{
				$ativo = "S";
			}
			echo $table->openTable();
				echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form&busca_usuario=$busca_usuario","adicionar.png")),
									   array(130)
									   );
			echo $table->closeTable();
		
			
		$sql = "SELECT qua_codigo,qua_numero,qua_andar,apt_codigo
				  FROM quarto";

		$querySql = pg_query($sql);
		echo $common->divisoria("Cadastro de Quartos");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Num. Quarto","Andar","Ala"),null,array(1,1,1,1,1),"S");
			while($linhas = pg_fetch_array($querySql)){
			
				echo $table->criaLinha(array($linhas[0],
											 $linhas[1],
											 $linhas[2],
											 $linhas[3],
											 $linhas[4],
											 $common->commonButton("editar","$PHP_SELF?acao=form&qua_codigo=$linhas[0]","editar_on.png"),
											 $common->commonButton("excluir","$PHP_SELF?acao=del&qua_codigo=$linhas[0]","apagar.png")),
									   array(1,500,50,150,150,100,100)
										);
			}
		echo $table->closeTable();
	}
	
	if($acao == "form"){
				$qua_codigo = $_GET[qua_codigo];
		echo $form->openForm("$PHP_SELF","post","orientacoes");
			if($qua_codigo == ""){
				echo $form->hiddenForm("acao", "add");
			}else{
				echo $form->hiddenForm("acao", "edit");
				echo $form->hiddenForm("qua_codigo", "$qua_codigo");
			}
			$sqlGeral = "SELECT *
				  		   FROM quarto
				  		  WHERE qua_codigo = $qua_codigo";
			$queryGeral = pg_query($sqlGeral);
			$registros = pg_fetch_array($queryGeral);
			echo $form->inputText("qua_numero",$registros[qua_numero],"Numero do Quarto",5);
			$sqlSetor = "select *from setor";
			echo $form->inputText("qua_andar",$registros[qua_andar],"Andar",3);
			echo $form->inputSelect("set_codigo",null,"Setor",$sqlSetor,null,null,$registros[set_codigo],null,"TODOS");
			echo $form->inputText("apt_codigo",$registros[apt_codigo],"Nome do Quarto",20);
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"validaOrientacoes();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","$PHP_SELF?busca_usuario=$busca_usuario","voltar.png");
				echo"</div>";
			echo"</div>";
		echo $form->closeForm();
	}
	if($acao == "add"){
		$stmt = "INSERT
				   INTO quarto(qua_numero,set_codigo,qua_andar,apt_codigo)
				   					VALUES('$qua_numero','$set_codigo','$qua_andar','$apt_codigo')";
		$msg = "inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	if($acao == "edit"){
		$qua_codigo = $_POST[qua_codigo];
		$stmt = "UPDATE quarto
					SET qua_numero = '$qua_numero',
					set_codigo = '$set_codigo',
					qua_andar = '$qua_andar',
					apt_codigo = '$apt_codigo'
				  WHERE qua_codigo = $qua_codigo";
		$msg = "alterado com sucesso";
		$msgErr = "Erro ao editar";
	}
	if($acao == "del"){
		$qua_codigo = $_GET[qua_codigo];
		echo $common->modalConfirm("Deseja realmente excluir este quarto?", "$PHP_SELF?acao=del&acaoDel=delete&qua_codigo=$qua_codigo","$PHP_SELF");
		if($acaoDel == "delete"){
			$sql = "DELETE FROM quarto
					 WHERE qua_codigo = $qua_codigo";
			$query = pg_query($sql) or die(pg_last_error());
			echo "<script>
					location.href=\"$PHP_SELF\";
				 </script>";			
		}
	}
	echo $msg;
	if($stmt){
		if(pg_query($stmt)){
			echo $common->modalMsg("OK", "$msg",$PHP_SELF);
		}else{
			echo $common->modalMsg("ERRO", "$msgErr",$PHP_SELF,$stmt);
		}
	}
	echo $common->closeTab();