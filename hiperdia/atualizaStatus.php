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
	
	$sql = "update hiperdia set hiper_status = 'I' where hiper_codigo = $hiper_codigo";
	$qry = pg_query($sql);
	echo $common->modalMsg("OK","Registro deletado com Sucesso!","geralHiperdia.php?usu_codigo_deletado=$usu_codigo&acao=deletado");	
?>