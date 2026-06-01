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
	echo $common->menuTab(array("Valores de Referencia"));
	echo $common->bodyTab("1");
	
	if($acao == ""){
		echo $form->openForm($PHP_SELF,"POST","valor");
			if($vlr_codigo == ""){
				echo $form->hiddenForm("acao", "inserir");
			}else{
				echo $form->hiddenForm("acao", "atualiza");
				echo $form->hiddenForm("vlr_codigo", "$vlr_codigo");
			}
			echo $form->hiddenForm("txa_codigo", "$txa_codigo");
			echo $form->hiddenForm("ite_codigo", "$ite_codigo");
			/*$verificaSubExame = "SELECT sex_codigo,
										sex_subexame 
								   FROM subexame 
								  WHERE txa_codigo = $txa_codigo";
			$queryVerificaSubExame = pg_query($verificaSubExame);
			$numLinhasVerificaSubExame = pg_fetch_array($queryVerificaSubExame);*/
			
/*			if($numLinhasVerificaSubExame > 0){
				$sqlItenSub = "SELECT * FROM itensanalise WHERE ite_codigo = $ite_codigo";
				$queryItenSub = pg_query($sqlItenSub);
				$regItenSub = pg_fetch_array($queryItenSub);
				
				echo $form->inputSelect("sex_codigo", $valor,"SubExames",$verificaSubExame,null,null,$regItenSub[sex_codigo]);
			}*/
				$selectValor = "SELECT * FROM valoresdereferencia WHERE vlr_codigo = $vlr_codigo";
				$queryValor = pg_query($selectValor);
				$regValor = pg_fetch_array($queryValor);
				
				echo $form->inputText("vlr_faixa_etaria_inicio",$regValor[vlr_faixa_etaria_inicio],"Faixa Etaria Inicial");
				echo $form->inputText("vlr_faixa_etaria_fim",$regValor[vlr_faixa_etaria_fim],"Faixa Etaria Final");
				$sexo = array("M"=>"Masculino","F"=>"Feminino");
				echo $form->inputSelect("vlr_sexo", $sexo,"Sexo",null,null,null,$regValor[vlr_sexo]);
				echo $form->textArea("vlr_valordereferencia", $regValor[vlr_valordereferencia],"Valores");
				echo "<div style='clear:both; width:400px; border:solid 0px;'>";
						echo"<div style='float:right; width:205px;'>";		
							echo $common->commonButton("Salvar", null, "salvar.gif", "onClick=\"document.valor.submit()\"");
						echo"</div>";
						echo"<div style='float:right'>";
							echo $common->commonButton("voltar","itensAnalise.php?txa_codigo=$txa_codigo","voltar.png");
						echo"</div>";
					echo"</div>";
		echo $form->closeForm();
	}
	
	if($acao == "atualiza"){
		$sql = "UPDATE valoresdereferencia 
				   SET vlr_faixa_etaria_inicio = ".($vlr_faixa_etaria_inicio == "" ? "null" : "$vlr_faixa_etaria_inicio").",
				   	   vlr_faixa_etaria_fim = ".($vlr_faixa_etaria_fim == "" ? "null" : "$vlr_faixa_etaria_fim").",
				   	   vlr_sexo = ".($vlr_sexo == "" ? "NULL" : "'$vlr_sexo'").",
				   	   vlr_valordereferencia = '$vlr_valordereferencia'
				 WHERE vlr_codigo = $vlr_codigo";
		if($querySql = pg_query($sql)){
			echo $common->modalMsg("OK","Registro Alterado Com Sucesso","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo");			
		}else{
			echo $common->modalMsg("ERRO","Erro ao atualizar o registro","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo","$sql");
		}
	}
	
	if($acao == "deletar"){
		echo $common->modalConfirm( "Deseja realmente deletar esse registro?","$PHP_SELF?acao=deletar&acao2=del&txa_codigo=$txa_codigo&vlr_codigo=$vlr_codigo&ite_codigo=$ite_codigo","$PHP_SELF");
		if($acao2 == "del"){
			$sqlDeletar = "DELETE FROM valoresdereferencia WHERE vlr_codigo = $vlr_codigo";
			if($queryDeletar = pg_query($sqlDeletar)){
				echo $common->modalMsg("OK","Registro deletado com sucesso!","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo");
			}else{
				echo $common->modalMsg("ERRO","Erro ao deletar registro","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo","$sqlDeletar");
			}
		}
	}
	if($acao == "inserir"){
		$sql = "INSERT INTO valoresdereferencia(ite_codigo,
  						    txa_codigo,
  						    sex_codigo,
  						    vlr_sexo,
  						    vlr_faixa_etaria_inicio,
  						    vlr_faixa_etaria_fim,
  						    vlr_valordereferencia)
  					 VALUES ($ite_codigo,
  					 		 $txa_codigo,
  					 		".($sex_codigo == "" ? "NULL" : "$sex_codigo").",
  					 		 ".($vlr_sexo == "" ? "NULL" : "'$vlr_sexo'").",
  					 		".($vlr_faixa_etaria_inicio == "" ? "NULL" : "$vlr_faixa_etaria_inicio").",
  					 		".($vlr_faixa_etaria_fim == "" ? "NULL" : "$vlr_faixa_etaria_fim").",
  					 		'$vlr_valordereferencia')";
		if($querySql = pg_query($sql)){
			echo $common->modalMsg("OK","Registro adicionado com sucesso!","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo");
		}else{
			echo $common->modalMsg("OK","Registro deletado com sucesso!","itensAnalise.php?txa_codigo=$txa_codigo&ite_codigo=$ite_codigo","$sql");
		}
	}
	echo $common->closeTab();