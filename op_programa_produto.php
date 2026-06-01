<?php
/** operador ajax */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once  'json.inc.php';


if( empty($_GET['prg']) )
{
	print '[]';
	exit;
}

$result 	= array();
$stmt 	= "SELECT b.prgp_codigo, a.pro_nome FROM produto a, programa_produto b WHERE b.prg_codigo = $_GET[prg] AND b.pro_codigo = a.pro_codigo ORDER BY pro_nome";
$qry		= pg_query($stmt);
while( $row = pg_fetch_array($qry) )
	$result[] = $row;

$json = new Services_JSON();
print $json->encode($result);

?>