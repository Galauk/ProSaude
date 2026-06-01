<?php
/**
Arquivo comum entre APAC/AIH
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

/** Calcula o proximo numero disponivel para a apac/aih
Retorna 0 quando erro : nao ha mais vagas
*/
function aih_apac_proximo_num( $tipo )
{
	//db_query("BEGIN");
	$stmt = "SELECT codigo, num_fim, num_prox,
		( num_prox - 11 * CEIL( num_prox / 11 ) ) as digito
		FROM aih_apac_numero 
		WHERE tipo = '$tipo' 
		ORDER BY codigo DESC LIMIT 1" ;

	$qry = db_query( $stmt );
	$row = pg_fetch_row( $qry );

	if( $row[1] < $row[2] || pg_num_rows($qry) == 0 ) return 0;

	$R = $row[2] . '-' . ($row[3] == '10' ? '0' : $row[3] );

	//$stmt1 = "UPDATE aih_apac_numero SET num_prox = num_prox + 1
		//WHERE codigo  = $row[0] ";

	//db_query( $stmt1 );

	//db_query("COMMIT");

	return array($R, $row[0]);
}
