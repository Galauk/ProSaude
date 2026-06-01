<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//echo "<pre>".print_r($_GET,true)."</pre>";


	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();

	$sql2 = "select co_sub_grupo,no_sub_grupo from tb_sub_grupo where co_grupo ='$codigo' ";
/*	$exe_sql = pg_query($sql2);
	$res_exe_sql = pg_fetch_array($exe_sql);
	$codigo = $res_exe_sql['co_grupo'];
	$nome = $res_exe_sql['no_sub_grupo'];*/
	echo $table->criaLinha(array($form->inputSelect('subGProcedimento','','Procedimentos',"$sql2",null,null,null,'style=width:250px')));
	
	

?>