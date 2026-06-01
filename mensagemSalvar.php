<?
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	$common = new commonClass();
	echo $common->incJquery();
	echo $common->modalMsg('OK','Exames Coletados com Sucesso!',"exa_pedidoexame.php");
?>