<script>
	function validaOrientacoes(){
		var ori_exa_orientacoes = document.getElementById("ori_exa_orientacoes").value;
		if(ori_exa_orientacoes == ""){
			alert("Preencha o campo orientacoes !");
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
		
	echo $common->menuTab(array("Cadastro de Orienta&ccedil;&otilde;es"));
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
				echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form&busca_usuario=$busca_usuario","adicionar.png"),
											 $common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),
											 $form->inputText("palavra","$palavra",null,null,null,null,null,null,"S"),
											 $form->inputCheckboxRadio("ativo",$ativo,null,null,array("S"=>"<b>SOMENTE ATIVOS"),"checkbox")
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
				$where = "WHERE ".(is_numeric($palavra) ? "ori_exa_codigo = $palavra" : "ori_exa_orientacoes ILIKE '%$palavra%' $codicaoInativo")."";
			}
		}
		
		if($ativo == "N"){
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."ori_exa_status <> 'A'";
		}else{
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."ori_exa_status = 'A'";
		}
		
		$where .= $codicaoInativo;
		$sql = "SELECT ori_exa_codigo,
					   ori_exa_orientacoes,
					   ori_exa_status
				  FROM orientacoes_exames
				  $where";
		$querySql = pg_query($sql);
		echo $common->divisoria("Orienta&ccedil;&otilde;es");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Orienta&ccedil;&atilde;o","A&ccedil;&otilde;es"),null,array(1,1,2),"S");
			while($linhas = pg_fetch_array($querySql)){
				if($linhas[2] == "A"){
					$ativarDesativar = $common->commonButton("Desativar","$PHP_SELF?acao=del&ori_exa_status=I&ori_exa_codigo=$linhas[0]&ori_exa_orientacoes=$linhas[1]","desvincular.png");
				}else{
					$ativarDesativar = $common->commonButton("Ativar","$PHP_SELF?acao=del&ori_exa_status=A&ori_exa_codigo=$linhas[0]&ori_exa_orientacoes=$linhas[1]","vincular.png");
				}
				echo $table->criaLinha(array($linhas[0],
											 $linhas[1],
											 $common->commonButton("editar","$PHP_SELF?acao=form&ori_exa_codigo=$linhas[0]","editar_on.png"),
											 $ativarDesativar),
									   array(1,500,10,10)
										);
			}
		echo $table->closeTable();
	}
	
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","post","orientacoes");
			if($ori_exa_codigo == ""){
				echo $form->hiddenForm("acao", "add");
			}else{
				echo $form->hiddenForm("acao", "edit");
				echo $form->hiddenForm("ori_exa_codigo", "$ori_exa_codigo");
			}
			$sqlGeral = "SELECT ori_exa_codigo,
					   			ori_exa_orientacoes
				  		   FROM orientacoes_exames
				  		  WHERE ori_exa_codigo = $ori_exa_codigo";
			$queryGeral = pg_query($sqlGeral);
			$registros = pg_fetch_array($queryGeral);
			echo $form->inputText("ori_exa_orientacoes",$registros[1],"Orienta&ccedil;&otilde;es",60);
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
		$ori_exa_orientacoes = $_POST[ori_exa_orientacoes];
		$stmt = "INSERT
				   INTO orientacoes_exames(
				   						   ori_exa_orientacoes)
				   					VALUES('$ori_exa_orientacoes')";
		$msg = "inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	if($acao == "edit"){
		$ori_exa_orientacoes = $_POST[ori_exa_orientacoes];
		$ori_exa_codigo = $_POST[ori_exa_codigo];
		$stmt = "UPDATE orientacoes_exames
					SET ori_exa_orientacoes = '$ori_exa_orientacoes'
				  WHERE ori_exa_codigo = $ori_exa_codigo";
		$msg = "alerado com sucesso";
		$msgErr = "Erro ao editar";
	}
	if($acao == "del"){
		$ori_exa_orientacoes = $_GET[ori_exa_orientacoes];
		$ori_exa_codigo = $_GET[ori_exa_codigo];
		$ori_exa_status = $_GET[ori_exa_status];
		echo $common->modalConfirm("Deseja realmente inativar a orienta&ccedil;&atilde;o $ori_exa_orientacoes", "$PHP_SELF?acao=del&acaoDel=delete&ori_exa_status=$ori_exa_status&ori_exa_codigo=$ori_exa_codigo","$PHP_SELF");
		if($acaoDel == "delete"){
			$ori_exa_codigo = $_GET[ori_exa_codigo];
			$ori_exa_status = $_GET[ori_exa_status];
			$sql = "UPDATE orientacoes_exames
					   SET ori_exa_status = '$ori_exa_status'
					 WHERE ori_exa_codigo = $ori_exa_codigo";
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