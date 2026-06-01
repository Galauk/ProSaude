<?
session_start();
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();

	echo $common->modalMsg("ERRO","Por Favor Informe o Nome do Paciente !","geralHiperdia.php");

?>