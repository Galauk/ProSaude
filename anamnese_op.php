<?php
/*
 * Arquivo de Operações Ajax da anamnese.php
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

if( $acao == 'add_rel' )
{
	$desc 	= trim($desc);
	db_query('begin');
	$stmt = "INSERT INTO anamnese_tipo (ana_tp_descricao) VALUES ('$desc')";
	$qry = db_query( $stmt );
	$id = db_get('SELECT MAX(ana_tp_codigo) FROM anamnese_tipo');
	echo $id;
	db_query('commit');
	
}
else if( $acao == 'edit_rel' )
{
	$desc 	= trim($desc);
	$id		= intval($id);
	$stmt = "UPDATE anamnese_tipo SET ana_tp_descricao='$desc' WHERE ana_tp_codigo=$id";
	db_query($stmt);
	echo '';
}
else if( $acao == 'del_rel' )
{
	$stmt1 = "DELETE FROM anamnese_tipo_rel WHERE ana_tp_codigo IN ($id)";
	$stmt2 = "DELETE FROM anamnese_tipo WHERE ana_tp_codigo IN ($id)";
	db_query($stmt1);
	db_query($stmt2);
	echo '';
}