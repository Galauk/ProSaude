<?php
// Atendimento Odontologico: operacoes do Ajax

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/odonto.inc.php";

cabecario( $hotkey = false );

verauth($id_login);

$stmt0 = "SELECT od_codigo FROM odonto WHERE age_codigo=$age_codigo" ;
$od_codigo = db_get($stmt0);

$stmt = "INSERT INTO odonto_historico ( 
	od_codigo, 
	od_hist_data, 
	dente_num, 
	dente_face, 
	dente_situacao, 
	dente_anotacao,
	od_finalizado
	 ) VALUES ( 
	".intval($od_codigo).", 
	CURRENT_DATE ,
	".intval($dente_num).", 
	'".trim(strtoupper(substr($face,0,10)))."', 
	'".trim(strtoupper(substr($sit,0,3)))."', 
	'".trim(strtoupper($anot))."',
	'". ( ! empty($finalizado) ? 'S' : 'N' ) ."' )";
	
db_query( $stmt );

//echo "<p><strong>Histórico Atualizado</strong></p>";