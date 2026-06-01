<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
$sql = "SELECT * from quarto q
		  JOIN medico m
		    ON q.med_codigo = m.med_codigo
		 WHERE m.prestador_servico = 'H'
";

//echo $table->criaLinha(array($form->inputSelect('subGProcedimento','','Procedimentos',"$sql",null,null,null,'style=width:250px')));
//echo $table->criaLinha(array($form->inputSelect('subGProcedimento','','Procedimentos',"$sql2",null,null,null,'style=width:250px')));

?>