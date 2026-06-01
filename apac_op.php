<?php
// operacao via ajax
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

//var_dump($_GET);

if( $acao == 'atualiza_uni_cnpj' )
{
	if( $apac == 'S' )
		$stmt = "UPDATE apac_unidade SET uni_cnpj = '$cnpj' WHERE uni_codigo = $codigo";
	else
		$stmt = "UPDATE unidade SET uni_cnpj = '$cnpj' WHERE uni_codigo = $codigo";

	//echo $stmt;
	db_query($stmt);
}
