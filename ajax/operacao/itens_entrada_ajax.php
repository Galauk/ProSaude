<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

$qtde = abs(intval($qtde));

$stmt 	= "SELECT 
	TO_CHAR(verifica_preco( '$pro_codigo', '$set_entrada', '$mov_data' ), '9999999999.999' ),
	TO_CHAR(verifica_preco( '$pro_codigo', '$set_entrada', '$mov_data' )*$qtde, '9999999999.99' )";

$arr 	= db_getRow($stmt);

print trim($arr[0]);
print ';';
print trim($arr[1]);
