<script>
	function validaObservacoes(){
		var obs_exa_observacoes = document.getElementById("obs_exa_observacoes").value;
		if(obs_exa_observacoes == ""){
			alert("Preencha o campo observacoes !");
			return false;
		}else{
			document.observacoes.submit();
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
		
	echo $common->menuTab(array("Cadastro de Observa&ccedil;&otilde;es"));
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
				$where = "WHERE ".(is_numeric($palavra) ? "obs_exa_codigo = $palavra" : "obs_exa_observacoes ILIKE '%$palavra%' $codicaoInativo")."";
			}
		}
		if($ativo == "N"){
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."obs_exa_status <> 'A'";
		}else{
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."obs_exa_status = 'A'";
		}
		$where .= $codicaoInativo;
		$sql = "SELECT obs_exa_codigo,
					   obs_exa_observacoes,
					   obs_exa_status
				  FROM observacoes_exames
				  $where";
		$querySql = pg_query($sql);
		echo $common->divisoria("Observa&ccedil;&otilde;es");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Observa&ccedil;&atilde;o","A&ccedil;&otilde;es"),null,array(1,1,2),"S");
			while($linhas = pg_fetch_array($querySql)){
				if($linhas[2] == "A"){
					$ativarDesativar = $common->commonButton("Desativar","$PHP_SELF?acao=del&obs_exa_status=I&obs_exa_codigo=$linhas[0]&obs_exa_observacoes=$linhas[1]","desvincular.png");
				}else{
					$ativarDesativar = $common->commonButton("Ativar","$PHP_SELF?acao=del&obs_exa_status=A&obs_exa_codigo=$linhas[0]&obs_exa_observacoes=$linhas[1]","vincular.png");
				}
				echo $table->criaLinha(array($linhas[0],
											 $linhas[1],
											 $common->commonButton("editar","$PHP_SELF?acao=form&obs_exa_codigo=$linhas[0]","editar_on.png"),
											 $ativarDesativar),
									   array(1,500,10,10)
										);
			}
		echo $table->closeTable();
	}
	
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","post","observacoes");
			if($obs_exa_codigo == ""){
				echo $form->hiddenForm("acao", "add");
			}else{
				echo $form->hiddenForm("acao", "edit");
				echo $form->hiddenForm("obs_exa_codigo", "$obs_exa_codigo");
			}
			$sqlGeral = "SELECT obs_exa_codigo,
					   			obs_exa_observacoes
				  		   FROM observacoes_exames
				  		  WHERE obs_exa_codigo = $obs_exa_codigo";
			$queryGeral = pg_query($sqlGeral);
			$registros = pg_fetch_array($queryGeral);
			echo $form->inputText("obs_exa_observacoes",$registros[1],"Observa&ccedil;&otilde;es",60);
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
				   INTO observacoes_exames(
				   						   obs_exa_observacoes)
				   					VALUES('$obs_exa_observacoes')";
		$msg = "inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	if($acao == "edit"){
		$stmt = "UPDATE observacoes_exames
					SET obs_exa_observacoes = '$obs_exa_observacoes'
				  WHERE obs_exa_codigo = $obs_exa_codigo";
		$msg = "alerado com sucesso";
		$msgErr = "Erro ao editar";
	}
	if($acao == "del"){
		$obs_exa_observacoes = $_GET[obs_exa_observacoes];
		$obs_exa_codigo = $_GET[obs_exa_codigo];
		$obs_exa_status = $_GET[obs_exa_status];
		if($acaoDel == ""){
			echo $common->modalConfirm("Deseja realmente inativar a observa&ccedil;&atilde;o $obs_exa_observacoes", "$PHP_SELF?acao=del&acaoDel=delete&obs_exa_status=$obs_exa_status&obs_exa_codigo=$obs_exa_codigo","$PHP_SELF");
		}
		if($acaoDel == "delete"){
			$obs_exa_codigo = $_GET[obs_exa_codigo];
			$obs_exa_status = $_GET[obs_exa_status];
			$sql = "UPDATE observacoes_exames
					   SET obs_exa_status = '$obs_exa_status'
					 WHERE obs_exa_codigo = $obs_exa_codigo";
			if(pg_query($sql)){
				echo $common->modalMsg("OK", "Salvo com sucesso!",$PHP_SELF);
			}else{
				echo $common->modalMsg("ERRO", "Excluído com sucesso!");
			}
		}
	}
	if($stmt){
		if(pg_query($stmt)){
			echo $common->modalMsg("OK", "$msg",$PHP_SELF);
		}else{
			echo $common->modalMsg("ERRO", "$msgErr",$PHP_SELF,$stmt);
		}
	}
	echo $common->closeTab();