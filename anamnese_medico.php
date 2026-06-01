<?php
/**
 Cadastro da ANAMNESE
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."anamnese.inc.php";
$commom = new commonClass();
Cabecario( $hotkey = false );


//verauth($id_login);

	$Anamnese = & new Anamnese( $id_login );

	$Anamnese->id_tipo 		= 1;
	$Anamnese->tabela 		= 'medico_anamnese';
	$Anamnese->fk_nome 		= 'age_codigo';
	$Anamnese->action		= '&age_codigo='.$age_codigo;
	$Anamnese->auto_insert 	= 0;
	$Anamnese->fk 			= $age_codigo; 
	$Anamnese->form();


	if( $Anamnese->passo == 2 )
	{
		$Anamnese->sql_form();
		echo $commom->modalMsg("OK","prontuario.php?pagina=99&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data","prontuario.php?pagina=99&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");		
	//print "<p class='aviso ok'>Anamnese Inserida...</p>";
	}
?>