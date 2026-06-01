<?php 
session_start();
echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/ajax_motor.js'></script>";
require_once '../global.php';
echo "<script src='".LINKSAUDE."/lib/jquery-1.4.4.js'></script>";
?>
<script>
	function mostraValores(ite_codigo,tr,txa_codigo){
		$(".oculta:not(.oculta"+ite_codigo+")").hide();
		if(!$(".oculta"+ite_codigo).size()){
			$.ajax({
				url:"mostraValores.php?ite_codigo="+ite_codigo+"&txa_codigo="+txa_codigo,
				success:function(retorno){	
					$(tr).after(retorno);
				}
			});
		}else{
			$(".oculta"+ite_codigo).toggle();
		}
	}
</script>
<?
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
	echo $common->menuTab(array("Itens de Analise"));
	echo $common->bodyTab("1");
	
	
	if($acao == ""){
		echo $common->divisoria("$proc_nome");
		echo $table->openTable("lista");
			echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form_add&txa_codigo=$txa_codigo&proc_nome=$proc_nome","adicionar.png"),
										 $common->commonButton("Voltar", "tipoExames.php","voltar.png")));
			$sqlSubexames = "SELECT sex_codigo,
									sex_subexame 
							   FROM subexame
							  WHERE txa_codigo = $txa_codigo";
			$querySubexames = pg_query($sqlSubexames);
			$numLinhasSubexames = pg_num_rows($querySubexames);
			if($numLinhasSubexames > 0){
				while($regSubexames = pg_fetch_array($querySubexames)){
					echo $table->criaLinha(array("$regSubexames[1]"),null,array(4),"S");
					$sqlItensAnalise = "SELECT ite_codigo,
											   ite_itemdoexame 
										  FROM itensanalise
										 WHERE sex_codigo = $regSubexames[0]
										 ORDER BY ite_codigo";
					$queryItensAnalise = pg_query($sqlItensAnalise);
					while($regItensAnalise = pg_fetch_row($queryItensAnalise)){
						array_push($regItensAnalise,
								   $common->commonButton("Editar","$PHP_SELF?acao=form_add&ite_codigo=$regItensAnalise[0]&txa_codigo=$txa_codigo","editar_on.png"),
								   $common->commonButton("Apagar","$PHP_SELF?acao=deletar&$regItensAnalise[0]&ite_itemdoexame=$regItensAnalise[1]&txa_codigo=$txa_codigo&ite_codigo=$regItensAnalise[0]&proc_nome=$proc_nome","apagar.png"));
								   
						echo $table->criaLinha($regItensAnalise,null,null,"N","onClick=\"mostraValores('$regItensAnalise[0]',this,'$txa_codigo')\"");
					}
				}
			}else{
					$sqlItensAnalise = "SELECT ite_codigo,
											   ite_itemdoexame 
										  FROM itensanalise
										 WHERE txa_codigo = $txa_codigo
										 ORDER BY ite_codigo";
					$queryItensAnalise = pg_query($sqlItensAnalise);
					/*while($regItensAnalise = pg_fetch_array($queryItensAnalise)){
						echo $table->criaLinha(array($regItensAnalise[0],
													 $regItensAnalise[ite_itemdoexame],
													 $common->commonButton("Editar","$PHP_SELF?acao=form_add&ite_codigo=$regItensAnalise[0]&txa_codigo=$txa_codigo","editar_on.png"),
													 $common->commonButton("Apagar","$PHP_SELF?acao=deletar&$regItensAnalise[0]&ite_itemdoexame=$regItensAnalise[ite_itemdoexame]&txa_codigo=$txa_codigo&ite_codigo=$regItensAnalise[0]&proc_nome=$proc_nome","apagar.png")));
					}*/
					while($regItensAnalise = pg_fetch_row($queryItensAnalise)){
						array_push($regItensAnalise,
								   $common->commonButton("Editar","$PHP_SELF?acao=form_add&ite_codigo=$regItensAnalise[0]&txa_codigo=$txa_codigo","editar_on.png"),
								   $common->commonButton("Apagar","$PHP_SELF?acao=deletar&$regItensAnalise[0]&ite_itemdoexame=$regItensAnalise[1]&txa_codigo=$txa_codigo&ite_codigo=$regItensAnalise[0]&proc_nome=$proc_nome","apagar.png"));
								   
						echo $table->criaLinha($regItensAnalise,null,null,"N","onClick=\"mostraValores('$regItensAnalise[0]',this,'$txa_codigo')\"");
					}
					
			}
		echo $table->closeTable();
		echo "<div id='valores'>";
		echo "</div>";
	}
	
	if($acao == "form_add"){
		echo $form->openForm($PHP_SELF,"POST","form_add");
			echo $form->hiddenForm("txa_codigo", "$txa_codigo");
			echo $form->hiddenForm("proc_nome", "$proc_nome");
			if($ite_codigo == ""){
				echo $form->hiddenForm("acao", "inserir");
			}else{
				echo $form->hiddenForm("acao","alterar");
				echo $form->hiddenForm("ite_codigo", $ite_codigo);
			}
			$sqlAllItens = "SELECT * 
							  FROM itensanalise 
							 WHERE ite_codigo = $ite_codigo";
			$queryAllItens = pg_query($sqlAllItens);
			$regAllItens = pg_fetch_array($queryAllItens);
			
			echo $form->inputText("ite_itemdoexame",$regAllItens[ite_itemdoexame],"Item do Exame");
			$sqlSubexamesItens = "SELECT sex_codigo,
										 sex_subexame
									FROM subexame
								   WHERE txa_codigo = $txa_codigo";
			$querySubExamesItens = pg_query($sqlSubexamesItens);
			$numLinhasSubExameItens = pg_num_rows($querySubExamesItens);
			if($numLinhasSubExameItens > 0){
				echo $form->inputSelect("sex_codigo",null,"Subexame","$sqlSubexamesItens",null,null,$regAllItens[sex_codigo]);
			}
			echo $form->inputText("ite_tipo_medida",$regAllItens[ite_tipo_medida],"Tipo de Medida");
			echo $form->textArea("ite_observacao", $regAllItens[ite_observacao],"Observa&ccedil;&otilde;es");
			echo $form->inputCheckboxRadio("historico", $regAllItens[historico], "Histórico", null, array("t"=>"Sim", "f"=>"Não"), "radio");
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"document.form_add.submit();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome","voltar.png");
				echo"</div>";
			echo"</div>";
		echo $form->closeForm();
	}
	if($acao == "inserir"){
		$insert = "INSERT 
					 INTO itensanalise(ite_itemdoexame,
					 				   txa_codigo,
					 				   sex_codigo,
					 				   ite_observacao,
					 				   ite_tipo_medida)
					 			VALUES ('$ite_itemdoexame',
					 					$txa_codigo,
					 					".($sex_codigo == "" ? "NULL" : "$sex_codigo").",
					 					'ite_observacao',
					 					'$ite_tipo_medida')";
		if($queryInsert = pg_query($insert)){
			echo $common->modalMsg("OK", "Registro salvo com sucesso !","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar registro !","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome","$insert");
		}
	}
	
	if($acao == "alterar"){
		$update = "UPDATE itensanalise 
					  SET ite_itemdoexame = '$ite_itemdoexame', 
					  	  txa_codigo = $txa_codigo, 
					  	  sex_codigo = ".($sex_codigo == "" ? "NULL" : "$sex_codigo").",
					  	  ite_observacao = '$ite_observacao', 
					  	  ite_tipo_medida = '$ite_tipo_medida',
						  historico = '$historico'
					WHERE ite_codigo = $ite_codigo";
		if($queryUpdate = pg_query($update)){
			echo $common->modalMsg("OK", "Registro salvo com sucesso !","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar registro !","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome","$update");
		}
	}
	
	if($acao == "deletar"){
		if($acao2 == "del"){
			$sqlDelete = "DELETE FROM itensanalise WHERE ite_codigo = $ite_codigo";
			if($queryDelete = pg_query($sqlDelete)){
				echo $common->modalMsg("OK","Registro deletado com sucesso!","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome");
			}else{
				echo $common->modalMsg("ERRO","Erro ao deletar o registro!","$PHP_SELF?txa_codigo=$txa_codigo&proc_nome=$proc_nome","$sqlDelete");
			}
		}else{
			echo $common->modalConfirm("Deseja apagar o item $ite_itemdoexame","$PHP_SELF?acao=deletar&acao2=del&txa_codigo=$txa_codigo&ite_codigo=$ite_codigo&proc_nome=$proc_nome");
		}
	}
	echo $common->closeTab();