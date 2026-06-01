<?php
/**
 * @version Eduardo (dudu@g1ti.com.br) 2007-07-04 BRT 09:43:43
 * Arquivo para busca dinâmica das cidades
*/

	require_once 'json.inc.php';
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";



if( empty($_GET['acao']) )
{	
	
	$grupo 	= $_GET['grupo_codigo'];

	$resp = array( array ("pro_codigo" => "-1", "pro_nome" => "-----Todos-----") );

	$sql = "SELECT pro_codigo,pro_nome FROM produto WHERE gru_codigo='{$grupo}' ORDER BY 2";
	
	$qry = db_query($sql,false);	
	
//	$resp[] .= array();
	while( $row = pg_fetch_array($qry) )
	{
		$resp[] = $row;
	}

	$JSON = new Services_JSON();
	
	echo $JSON->encode( $resp );
	
}
