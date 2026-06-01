<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$codigo = $_GET['codigo'];
$nome = $_GET['nome'];
$id = $_GET['id'];
echo $nome."/".$id;


?>