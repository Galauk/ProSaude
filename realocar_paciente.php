<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";


pg_query("update agendamento set age_atendido='".$_REQUEST['age_atendido']."' where age_codigo ='".$_REQUEST['age_codigo']."'");

?>

