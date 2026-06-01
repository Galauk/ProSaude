<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$form = new classForm();
	$table = new tableClass();
	$common = new commonClass();
	echo $common->incJquery();
	
	$codigo = $_GET[proc_ori_codigo];
	$proc_nome = $_GET[proc_nome];
	$acao = $_GET[acao];
	$proc_codigo = $_GET[proc_codigo];
	
	if($acao == "acao"){
		$delete = "DELETE
					 FROM procedimento_observacoes
					WHERE proc_exa_observacoes = $codigo
					  AND proc_codigo = $proc_codigo";
		$queryDelete = pg_query($delete);
	}
		$sql = "SELECT * 
				  FROM procedimento_observacoes as po 
				  JOIN procedimento as p ON p.proc_codigo = po.proc_codigo 
				  JOIN observacoes_exames as oe ON oe.obs_exa_codigo = po.obs_exa_codigo 
				 WHERE po.proc_codigo = $proc_codigo";
		$query = pg_query($sql);
		$numLinhas = pg_num_rows($query);
		
		echo $table->openTable("lista");
		echo $common->divisoria("Observa&ccedil;&otilde;es do Procedimento <font color='#FFFFFF'><em> $proc_nome </em></font>");
		echo $table->criaLinha(array("C&oacute;digo Procedimento","Observa&ccedil;&otilde;es","A&ccedil;&otilde;es"),null,null,"S");
		while($res = pg_fetch_array($query)){
			echo $table->criaLinha(array($res[obs_exa_codigo],
										 $res[obs_exa_observacoes],
										 $common->commonButton("apagar",null,"apagar.png","onClick=\"chamaVinculacoes('$res[proc_exa_observacoes]','$res[proc_nome]','acao','$res[proc_codigo]')\"")));
		}
		echo $table->closeTable();
		echo "|".$numLinhas;
	
?>