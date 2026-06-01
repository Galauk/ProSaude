<?php
/**
 * @version Eduardo (dudu@g1ti.com.br) 2007-07-04 BRT 09:43:43
 * Arquivo para busca dinâmica das cidades
*/

require_once '../json.inc.php';
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

if( empty($_GET['acao']) )
{
	$resp 	= array( 'id' => $_GET['id'], 'total' => 0, 'dados' => array() );
	
	$uf 	= $_GET['uf'];
	$campo	= ( empty($_GET['campo']) ? 'cid_codigo_ibge' : $_GET['campo'] );
	
	//echo
	$sql = "SELECT {$campo} as cid_codigo, cid_nome FROM cidade WHERE uf_sigla='{$uf}' ORDER BY 2";
	
	$qry = db_query($sql,false);
	
	$resp[ 'total' ] = pg_num_rows($qry);
	
	while( $row = pg_fetch_array($qry) )
	{
		$resp[ 'dados' ][] = $row;
	}
	
	$JSON = new Services_JSON();
	echo $JSON->encode( $resp );
	
}
