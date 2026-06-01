<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
#require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";


	#$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	#echo $common->incJquery();
	
	$procedimento = $_GET['procedimento'];

	$sql2 = "SELECT c.cd10_codigo,c.cd10_codigo_cid || ' - ' || c.cd10_descricao
			   FROM cid10 AS c
			   JOIN rl_procedimento_cid AS rl
			     ON rl.co_cid=c.cd10_codigo_cid
			   JOIN procedimento AS p
			     ON p.proc_codigo_sus=rl.co_procedimento
			    AND p.proc_codigo=$procedimento
			  ORDER BY c.cd10_descricao";
	
	echo $table->criaLinha(array($form->inputSelect('cid','','<abbr title="Classificação Internacional de Doenças">CID</abbr>',"$sql2",null,null,null,'style=width:250px')));
	
	

	
?>