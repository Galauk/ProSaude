<?php
/**
 Cadastro da ANAMNESE
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."anamnese.inc.php";

Cabecario( $hotkey = false );

//verauth($id_login);

$Anamnese = & new Anamnese( $id_login );

$Anamnese->id_tipo 	= 17;
$Anamnese->tabela 	= 'odonto_anamnese';
$Anamnese->fk_nome 	= 'od_codigo';
$Anamnese->fk 		= 22;
$Anamnese->edit		= 0;
$Anamnese->form();
if( $Anamnese->passo == 2 )
{
	print "<p class='Aviso'>Anamnese Inserida...</p>";
}

