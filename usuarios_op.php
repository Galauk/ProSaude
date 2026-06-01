<?php
/**
 * arquivo ajax do usuarios.php
*/

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";


if( $acao == 'verifica' )
{
    
    $stmt = "SELECT COUNT(usr_codigo) FROM usuarios WHERE ".
            "(usr_login = '{$usr_login}' OR usr_email = '{$usr_email}') ".
            ( ! empty($usr_codigo) ? "AND usr_codigo <> $usr_codigo " : "" ) ;
            
    if( (int)db_get($stmt) > 0 )
    {
        print "({ 'ok': false, 'msg': 'Ja existe um usuario com este login/email !' })";
    }
    else
    {
        print "({ 'ok': true, 'msg': null })";
    }
}
?>