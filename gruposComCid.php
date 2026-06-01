<?php
	header("Content-Type: text/html; charset=ISO-8859-1", true);
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
	
	$gd_codigo = $_GET[gd_codigo];
	$gd_descricao = $_GET[gd_descricao];
	$acao = $_GET[acao];
	$codigo = $_GET[cd10_codigo];
	
	if($acao == "acao"){
		$delete = "DELETE
					 FROM grupos_cid
					WHERE gc_codigo = $codigo";
		$queryDelete = pg_query($delete);
		if($queryDelete){
			echo "<script>
					alert('Apagado com Sucesso');
				  </script>";
		}
	}
	$sql = "SELECT * 
			  FROM grupos_cid AS gc
			  JOIN cid10 as c
			    ON c.cd10_codigo = gc.cd10_codigo 
			 WHERE gd_codigo = $gd_codigo";
	$query = pg_query($sql);
	$numLinhas = pg_num_rows($query);
	echo $table->openTable("lista");
	echo $common->divisoria("Cid's relacionados ao grupo  <font color='#FFFFFF'><em> $gd_descricao </em></font>");
	echo $table->criaLinha(array("Codigo","Cid","A&ccedil;&otilde;es"),null,null,"S");
	while($res = pg_fetch_array($query)){
		echo $table->criaLinha(array($res[gc_codigo],
									 $res[cd10_descricao],
									 $common->commonButton("apagar",null,"apagar.png","onClick=\"chamaVinculacoes('$res[gd_descricao]','$res[gd_codigo]','acao','$res[gc_codigo]')\"")));
	}
	echo $table->closeTable();
	echo "|".$numLinhas;

?>