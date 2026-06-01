<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$form  = new classForm();
	$common =  new commonClass();
	echo $common->incJquery();
	
	echo $common->modalConfirm("Deseja deletar Esse Registro?","atualizaStat.php?tub_codigo=$tub_codigo&usu_codigo=$usu_codigo","geralTuberculose.php?usu_codigo=$usu_codigo");

?>