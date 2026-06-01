<script>
function validaGenerico(){
	var generico = document.getElementById("proc_sis_nome_generico").value;
	if(generico == ""){
		alert('Preencha o nome generico');
		return false;
		exit;
	}
}
</script>
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

echo $common->menuTab(array("Exames Prenatal"));
	echo $common->bodyTab("1");
	
	if($acao == ""){
		echo $form->openForm("$PHP_SLEF","POST","vincula");
			echo $form->hiddenForm("acao","vincular");
			$arrayTrue = array("t"=>"Sim","f"=>"Não");
			$sql = "SELECT *
					  FROM procedimento order by proc_nome";
			echo $form->inputSelect("proc_codigo","$proc_codigo","Exame","$sql",null,null,null,"style=\"width:200px;\"",null,"style=\"width:300px;\"");
			echo $form->inputText("proc_sis_nome_generico","$proc_sis_nome_generico","Nome Generico");
			echo $form->inputText("exa_sispn_validade","$exa_sispn_validade","Dias de Validade");
			echo $form->inputSelect("proc_sispn_sisprenatal",$arrayTrue,"Sisprenatal",null,null,null,$arrayTrue);
			echo "<div style=\"clear:both;\">";
				echo $common->commonButton("Gerar Vinculo",null,"Export.png","onClick=\"validaGenerico();document.vincula.submit()\"");
			echo "</div>";
		echo $form->closeForm();
		echo $table->openTable("lista");
			echo $table->criaLinha(array("C&oacute;digo","Nome","Nome Generico","Validade"),null,array(1,1,1,2),"S");
			$sqlSeq = "SELECT *
					     FROM procedimentos_sisprenatal as ps
					     JOIN procedimento as proc
					       ON proc.proc_codigo = ps.proc_codigo
					    ORDER BY proc_nome";
			$querySeq = pg_query($sqlSeq);
			while($linha = pg_fetch_array($querySeq)){
				echo $table->criaLinha(array("$linha[proc_codigo]",
											 "$linha[proc_nome]",
											 "$linha[proc_sis_nome_generico]",
											 "$linha[proc_sispn_validade]",
											 $common->commonButton("apagar","vinculacaoExamesModuloPrenatal.php?acao=apagar&proc_codigo=$linha[proc_codigo]&proc_nome=$linha[proc_nome]","apagar.png")
											 ),
									   array(null,null,null,null,250)
									   );				
			}
		echo $tabel->closeTable();
	}
	if($acao == "vincular"){
		$sql = "INSERT 
				  INTO procedimentos_sisprenatal(proc_codigo,
				  								 proc_sis_nome_generico,
				  								 proc_sispn_validade,
				  								 proc_sispn_sisprenatal)
									     VALUES ($proc_codigo,
									    		 '$proc_sis_nome_generico',
									    		 '$exa_sispn_validade',
									    		 '".($proc_sispn_sisprenatal == "" ? "f" : "$proc_sispn_sisprenatal")."')";
		
		if($query = pg_query($sql)){
			echo $common->modalMsg("OK","Salvo com sucesso !","vinculacaoExamesModuloPrenatal.php");
		}else{
			echo $common->modalMsg("ERRO","Erro ao salvar!","vinculacaoExamesModuloPrenatal.php",$sql);
		}
	}
	if($acao == "apagar"){
		if($acao2 == ""){
			echo $common->modalConfirm("Deseja mesmo apagar o registro $proc_nome ?","vinculacaoExamesModuloPrenatal.php?acao=apagar&acao2=confirm&proc_codigo=$proc_codigo","vinculacaoExamesModuloPrenatal.php");
		}
		if($acao2 == "confirm"){
			$sql = "DELETE FROM procedimentos_sisprenatal WHERE proc_codigo = $proc_codigo";
			$query = pg_query($sql);
			//echo $sql;
			//exit;
			echo $common->modalMsg("OK","Registro apagado com sucesso!","vinculacaoExamesModuloPrenatal.php");
		}
	}
	echo $common->closetab();


