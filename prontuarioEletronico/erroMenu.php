<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$common = new commonClass();
echo $common->incJquery();

echo $common->modalMsg('ERRO', 'Selecione um Paciente Para realizar essa operacao!',"prontuario.php?pagina=1");  
?>