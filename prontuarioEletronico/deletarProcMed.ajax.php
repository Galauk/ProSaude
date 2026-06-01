<?php

session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$pat_codigo = $_GET['pat_codigo'];
$res = pg_query("DELETE FROM procedimento_atendimento WHERE pat_codigo='$pat_codigo';");