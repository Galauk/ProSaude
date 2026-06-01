<?

session_start();
include('header.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){
    include('parseForm.php');
}

include("db.inc.painel.php");
// die("sfsfbasa");
// require_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";

// echo time();
// include("../../WebSocialComum/library/php/db.inc.php");