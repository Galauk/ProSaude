<?php

require_once '../global.php';
	echo "<script src='".LINKSAUDE."/lib/jquery-1.4.4.js'></script>";
?>
<script>
	function validaForm(){
		if($("#proc_codigo").val())
			document.form_add.submit();
	}

</script>
<?php 
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
	echo $common->menuTab(array("Configura&ccedil;&otilde;es de Laudos"));
	echo $common->bodyTab("1");
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST","busca");
			echo $form->hiddenForm("buscar","buscar");
				echo $table->openTable();
					echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form&busca_usuario=$busca_usuario","adicionar.png"),$common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),$form->inputText("palavra","$valor",null,null,null,null,null,null,"S")),array(130));
				echo $table->closeTable();
			echo $form->closeForm();
		echo $table->openTable(lista);
		if($palavra != ""){
			$where = "WHERE proc_nome ILIKE '%$palavra%' ".(is_numeric($palavra) ? "OR p.proc_codigo = $palavra" : "")."";
		}
			$sqlLinhas = " select t.proc_codigo,
							      p.proc_nome,
							      t.txa_codigo  
							 from tipodeexame as t
							 join procedimento as p
							   on p.proc_codigo = t.proc_codigo
						   $where";
			$queryLinhas = pg_query($sqlLinhas);
				echo $table->criaLinha(array("C&oacute;digo","Procedimento","Op&ccedil;&otilde;es"),null,array(1,1,3),"S");
			while($linhas = pg_fetch_array($queryLinhas)){
				echo $table->criaLinha(array($linhas[proc_codigo],
											 $linhas[proc_nome],
											 $common->commonButton("Editar","$PHP_SELF?acao=form&txa_codigo=$linhas[txa_codigo]","editar_on.png"),
											 $common->commonButton("apagar","$PHP_SELF?acao=delete&txa_codigo=$linhas[txa_codigo]&proc_nome=$linhas[proc_nome]","apagar.png"),
											 $common->commonButton("Itens de Analise","itensAnalise.php?txa_codigo=$linhas[txa_codigo]&proc_nome=$linhas[proc_nome]","visualizar.png")
											 ),
									   null,
									   null,
									   "N");
			}

		echo $table->closeTable();
		echo "<div id=hide style=\"clear:both; display:none;\"></div>";
	}
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","POST","form_add",null,"form_add");
			if($txa_codigo == ""){
				echo $form->hiddenForm("acao", "insert");
			}else{
				echo $form->hiddenForm("acao", "update");
				echo $form->hiddenForm("txa_codigo", "$txa_codigo");
			}
			$sqlAllTipoExame = "SELECT * FROM tipodeexame WHERE txa_codigo = $txa_codigo";
			$queryAllTipoExame = pg_query($sqlAllTipoExame);
			$regAllTipoExame = pg_fetch_array($queryAllTipoExame);
			
			$sqlProcedimento = "SELECT * FROM PROCEDIMENTO order by proc_nome";
			echo $form->inputSelect("proc_codigo",$proc_codigo,"Procedimento",$sqlProcedimento,null,null,$regAllTipoExame[proc_codigo],null,null,"style=\"width:350px;\"");
			$sqlTipoDeMetodos= "SELECT * FROM tipodemetodos";
			echo $form->inputSelect("tpm_codigo",$tpm_codigo,"Tipo de Metodos",$sqlTipoDeMetodos,null,null,$regAllTipoExame[tpm_codigo]);
			$sqlTipoDeMaterial = "SELECT * FROM tipodematerial";
			echo $form->inputSelect("tma_codigo",$tma_codigo,"Tipo de Material",$sqlTipoDeMaterial,null,null,$regAllTipoExame[tma_codigo]);
			$sqlCategoriaDeExames = "SELECT * FROM categoriadeexames";
			echo $form->inputSelect("cte_codigo",$tma_codigo,"Categoria de exames",$sqlCategoriaDeExames,null,null,$regAllTipoExame[cte_codigo]);
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("Salvar", null, "salvar.gif", "onClick=\"validaForm()\"");
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar","$PHP_SELF?busca_usuario=$busca_usuario","voltar.png");
					echo"</div>";
				echo"</div>";
		echo $form->closeForm();
	}
	if($acao == "insert"){
		$insert = "INSERT INTO tipodeexame (proc_codigo,
											tma_codigo,
											cte_codigo)
									VALUES ($proc_codigo,
											$tma_codigo,
											$cte_codigo);";
		if($queryInsert = pg_query($insert)){
			echo $common->modalMsg("OK","Registro salvo com sucesso!","$PHP_SELF");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar o registro!","$PHP_SELF",$insert);
		}
	}
	if($acao == "update"){
		$update = "UPDATE tipodeexame 
					  SET proc_codigo = $proc_codigo, 
					  	  tma_codigo = $tma_codigo,
					  	  tpm_codigo = $tpm_codigo, 
					  	  cte_codigo = $cte_codigo
					WHERE txa_codigo = $txa_codigo";
		if($queryUpdate = pg_query($update)){
			echo $common->modalMsg("OK","Registro salvo com sucesso!","$PHP_SELF");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar o registro!","$PHP_SELF",$update);
		}
	}
	if($acao == "delete"){
		if($acao2 == "del"){
			$sqlDelete = "DELETE FROM tipodeexame WHERE txa_codigo = $txa_codigo";
			if($queryDelete = pg_query($sqlDelete)){
				echo $common->modalMsg("OK","Registro deletado com sucesso!","$PHP_SELF");
			}else{
				echo $common->modalMsg("ERRO","Erro ao deletar o registro!","$PHP_SELF","$sqlDelete");
			}
		}else{
			echo $common->modalConfirm("Deseja apagar o tipo de exame do procedimento $proc_nome","$PHP_SELF?acao=delete&acao2=del&txa_codigo=$txa_codigo");
		}
	}
	echo $common->closeTab();
	
?>