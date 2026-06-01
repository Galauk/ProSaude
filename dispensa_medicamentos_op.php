<?php
/**
 * Arquivo OP (AJAX) do dispensa_medicamentos
*/

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";


if( $acao == 'verificar_medicamento' )
{
	$usu_codigo = intval( $_GET['usu_codigo'] );
	$sql = "SELECT COUNT(*) ".
			"FROM movimento ".
			"WHERE mov_data + 30 > CURRENT_DATE AND usu_codigo = {$usu_codigo}";
	
	//echo $sql;
	
	$total = (int) db_get( $sql );
	
	if( $total == 0 )
		$resp = array( 'ok' => true, 'msg' => null );
	else
		$resp = array( 'ok' => false, 'msg' => 'Este paciente ja retirou algum medicamento nos ultimos 30 dias' );
		
	$JSON = new Services_JSON();
	echo $JSON->encode( $resp );
}