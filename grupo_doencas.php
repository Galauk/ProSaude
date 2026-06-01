<script>
	function validaObservacoes(){
		var gd_descricao = document.getElementById("gd_descricao").value;
		if(gd_descricao == ""){
			alert("Preencha o campo observacoes !");
			return false;
		}else{
			document.grupos.submit();
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
		
	echo $common->menuTab(array("Cadastro de Grupos de Doencas"));
	echo $common->bodyTab("1");
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST","busca");
			echo $form->hiddenForm("busca","busca");
			echo $form->hiddenForm("buscaEfetuada", "S");
			echo $table->openTable();
				echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form&busca_usuario=$busca_usuario","adicionar.png"),
											 $common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),
											 $form->inputText("palavra","$palavra",null,null,null,null,null,null,"S")
											 ),
									   array(130)
									   );
			echo $table->closeTable();
		echo $form->closeForm();
		if($busca == "busca"){
			$busca = $_POST[busca];
			$palavra = $_POST[palavra];
			$inativo = $_POST[inativo];
			//echo "<pre>".print_r($_POST,true)."</pre>";
			if($palavra != ""){
				$wh = "AND";
				$where = "WHERE ".(is_numeric($palavra) ? "gd_codigo = $palavra" : "gd_descricao ILIKE '%$palavra%' $codicaoInativo")."";
			}
		}
		$where .= $codicaoInativo;
		$sql = "SELECT gd_codigo,
					   gd_descricao
				  FROM grupo_doencas
				  $where";
		$querySql = pg_query($sql);
		echo $common->divisoria("Grupos");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Grupos","A&ccedil;&otilde;es"),null,array(1,1,2),"S");
			while($linhas = pg_fetch_array($querySql)){
				echo $table->criaLinha(array($linhas[0],
											 $linhas[1],
											 $common->commonButton("editar","$PHP_SELF?acao=form&gd_codigo=$linhas[0]","editar_on.png"),
											 $common->commonButton("Apagar","$PHP_SELF?acao=del&gd_codigo=$linhas[0]&gd_descricao=$linhas[1]","apagar.png")),
									   array(1,500,10,10)
										);
			}
		echo $table->closeTable();
	}
	
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","post","grupos");
			if($gd_codigo == ""){
				echo $form->hiddenForm("acao", "add");
			}else{
				echo $form->hiddenForm("acao", "edit");
				echo $form->hiddenForm("gd_codigo", "$gd_codigo");
			}
			$sqlGeral = "SELECT gd_codigo,
					   			gd_descricao
				  		   FROM grupo_doencas
				  		  WHERE gd_codigo = $gd_codigo";
			$queryGeral = pg_query($sqlGeral);
			$registros = pg_fetch_array($queryGeral);
			echo $form->inputText("gd_descricao",$registros[1],"Descri&ccedil;&atilde;o",60);
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"validaObservacoes();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","$PHP_SELF?busca_usuario=$busca_usuario","voltar.png");
				echo"</div>";
			echo"</div>";
		echo $form->closeForm();
	}
	if($acao == "add"){
		$stmt = "INSERT
				   INTO grupo_doencas(gd_descricao)
				   					VALUES('$gd_descricao')";
		$msg = "Inserido com sucesso";
		$msgErr = "Erro ao inserir";
		//echo $stmt;
		//exit();
	}
	if($acao == "edit"){
		$stmt = "UPDATE grupo_doencas
					SET gd_descricao = '$gd_descricao'
				  WHERE gd_codigo = $gd_codigo";
		$msg = "Alterado com sucesso";
		$msgErr = "Erro ao editar";
	}
	if($acao == "del"){
		$gd_descricao = $_GET[gd_descricao];
		$gd_codigo = $_GET[gd_codigo];
		echo $common->modalConfirm("Deseja realmente apagar o grupo $gd_descricao", "$PHP_SELF?acao=del&acaoDel=delete&gd_codigo=$gd_codigo","$PHP_SELF");
		if($acaoDel == "delete"){
			$gd_codigo = $_GET[gd_codigo];
			$sql = "DELETE from grupo_doencas
					 WHERE gd_codigo = $gd_codigo";
			//echo $sql;exit();
			$query = pg_query($sql);
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