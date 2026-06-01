<?php
/** operador ajax */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once  'json.inc.php';


if( empty($_GET['uf']) )
{
	print '[]';
	exit;
}

$result 	= array();
$stmt 	= "SELECT cid_codigo, cid_nome FROM cidade WHERE uf_sigla='{$_GET['uf']}' ORDER BY cid_nome";
$qry		= pg_query($stmt);
while( $row = pg_fetch_array($qry) )
	$result[] = $row;

$json = new Services_JSON();
print $json->encode($result);

