<script>
	function validaOrientacoes(){
		var lei_numero = document.getElementById("lei_numero").value;
		if(lei_numero == ""){
			alert("Preencha o campo Numero do Leito !");
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
		
	echo $common->menuTab(array("Cadastro de Leitos"));
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
				
		if($ativo == "N"){
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."lei_ativo <> 'S'";
		}else{
			$codicaoInativo = ($wh == "" ? "WHERE " : "$wh ")."lei_ativo = 'S'";
		}
		
		$where .= $codicaoInativo;
		$sql = "SELECT *
				  FROM leito
				  $where";
				  //die($sql);
		$querySql = pg_query($sql);
		echo $common->divisoria("Leitos");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Numero do Quarto","Numero do Leito","Temporario","Status"),null,array(1,1,1,1,1),"S");

     while($linhas = pg_fetch_array($querySql)){
     if($linhas[lei_temporario]=="S") { $vn = "SIM"; } else { $vn = "NAO"; }
	 if($linhas[lei_ativo]=="S") { $vn1 = "SIM"; } else { $vn1 = "NAO"; }
			$rr = pg_fetch_array(pg_query("select * from quarto where qua_codigo = '$linhas[qua_codigo]'"));
				if($linhas[2] == "S"){
					$ativarDesativar = $common->commonButton("Desativar","$PHP_SELF?acao=del&ori_exa_status=N&lei_codigo=$linhas[lei_codigo]","desvincular.png");
				}else{
					$ativarDesativar = $common->commonButton("Ativar","$PHP_SELF?acao=del&ori_exa_status=S&lei_codigo=$linhas[lei_codigo]","vincular.png");
				}
				echo $table->criaLinha(array($linhas[lei_codigo],
											 $rr[qua_numero],
											 $linhas[lei_numero],
											 $vn,
											 $vn1,
											 $common->commonButton("editar","$PHP_SELF?acao=form&lei_codigo=$linhas[lei_codigo]","editar_on.png"),
											 $common->commonButton("excluir","$PHP_SELF?acao=del&lei_codigo=$linhas[lei_codigo]","apagar.png")),
									   array(1,500,10,10,10,10,10)
										);
			}
		echo $table->closeTable();
	}
	
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","post","orientacoes");
			if($lei_codigo == ""){
				echo $form->hiddenForm("acao", "add");
			}else{
				echo $form->hiddenForm("acao", "edit");
				echo $form->hiddenForm("lei_codigo", "$lei_codigo");
			}
			$sqlGeral = "SELECT *
				  		   FROM leito
				  		  WHERE lei_codigo = $lei_codigo";
			$queryGeral = pg_query($sqlGeral);
			$registros = pg_fetch_array($queryGeral);
			$sqlQuarto = "select qua_codigo,apt_codigo from quarto";
			
				echo $form->inputSelect("qua_codigo",null,"Quarto",$sqlQuarto,null,null,$registros[qua_codigo],null,"TODOS");
				
				echo $form->inputSelect("lei_temporario",array('S'=>'SIM','N'=>'NAO'),"Leito Temporario",null,null,null,$registros[lei_temporario],null,null,"TODOS");

				echo $form->inputSelect("lei_ativo",array('S'=>'SIM','N'=>'NAO'),"Status do Leito",null,null,null,$registros[lei_ativo],null,null,"TODOS");
				echo $form->inputText("lei_numero",$registros[lei_numero],"Numero do Leito",7);
				echo $form->textArea("lei_observacao",$registros[lei_observacao],"Observa&ccedil;&otilde;es",60,null,null,"text");
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
		$lei_codigo = $_POST[lei_codigo];
		$stmt = "INSERT
				   INTO leito(qua_codigo,lei_observacao,lei_numero,lei_ativo,lei_temporario)
				   					VALUES('$qua_codigo','$lei_observacao','$lei_numero','$lei_ativo','$lei_temporario')";
		$msg = "inserido com sucesso";
		$msgErr = "Erro ao inserir";
	}
	if($acao == "edit"){
		$ori_exa_orientacoes = $_GET[ori_exa_orientacoes];
		$ori_exa_codigo = $_GET[ori_exa_codigo];
		$stmt = "UPDATE leito
					SET qua_codigo='$qua_codigo',lei_observacao='$lei_observacao',lei_numero='$lei_numero',lei_ativo='$lei_ativo',lei_temporario='$lei_temporario'
				  WHERE lei_codigo = $lei_codigo";
		$msg = "alerado com sucesso";
		$msgErr = "Erro ao editar";
	}
	if($acao == "del"){
		echo $common->modalConfirm("Deseja realmente Excluir o Leito?", "$PHP_SELF?acao=del&acaoDel=delete&lei_codigo=$lei_codigo","$PHP_SELF");
		if($acaoDel == "delete"){
				$sql = "DELETE from leito
					 WHERE lei_codigo = $lei_codigo";
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