<?php

/** Variável global para pegar número de linhas */
$DB_NUM_ROWS = 0;

/** Executa uma query
* @return resource_link
* @param $stmt Parâmetro
*/
function db_query( $stmt, $log = true )
{
	global $DB_NUM_ROWS;
	$resource_result = @ pg_query( $stmt );

	switch( db_parseFirstCommand($stmt) )
	{
		case 'UPDATE':
		case 'DELETE':
		case 'INSERT':
			$DB_NUM_ROWS = @ pg_affected_rows( $resource_result );
			break;
		default :
			$DB_NUM_ROWS = @ pg_num_rows( $resource_result );
	}

	if( $log )
	{
		GLOBAL $LOG_MSG, $id_login;
/*		$stmt_log = "INSERT INTO log ".
			"(usr_codigo, log_msg, log_arquivo, log_qs, log_ip, log_sql) ".
			"VALUES (".
				"". ( intval($id_login) ) .",".
				"'". $LOG_MSG ."',".
				"'". $_SERVER['PHP_SELF'] ."',".
				"'". $_SERVER['QUERY_STRING'] ."',".
				"'". $_SERVER['REMOTE_ADDR'] ."',".
				"'". addslashes($stmt) . "') ";
			
		db_query( $stmt_log, false );
*/
	}

	if( ! $resource_result )
	{
		$err_msg = @ pg_last_error();
		die( "<p>Erro com sql:<pre>$stmt</pre>$err_msg</p>" );
	}
	
	return $resource_result;
}

/** Pega uma 'row' (linha/array) inteiro de uma query
 * @param 	$stmt 	string 
 * @return 	mixed
*/
function db_getRow( $stmt )
{
	$q = db_query( $stmt );
	return pg_fetch_array( $q );
}

/** 'Pega' o valor de uma coluna específica
 * @public
 * @param 	$stmt 	string 
 * @return 	mixed
*/
function db_get( $stmt )
{
	$q = db_query( $stmt );
	$row = pg_fetch_array( $q );
	return $row[0];
}

/** "Tenta" pegar o primeiro comando do Stmt
* @param	$sql String da sql
* @return	String (strtoupper) com o 1o comando
*/
function db_parseFirstCommand( $sql )
{	
	$fc = @ preg_split( "/ /", $sql );
	return strtoupper( $fc[0] );
}