<?php
/**
 * @brief Arquivo ajax do paciente.php
 * usado pelo paciente.php
*/


session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

if( $acao == 'atualiza_prontuario' )
{
    db_query('begin');
    $pront = db_get( "SELECT nextval('seq_prontuario')" );
    $stmt = "UPDATE usuario SET usu_prontuario = {$pront} WHERE usu_codigo = {$usu_codigo}";
    db_query($stmt);
    db_query('commit');
    
    print "({'ok':true,'usu_prontuario':$pront})";
}