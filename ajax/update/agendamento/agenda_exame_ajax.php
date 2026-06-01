<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."funcoes.agendar_exame.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
    
$id_login 	= $_GET['id_login'];    
$data		= $_GET['data'];
$codigo		= $_GET['agexl_codigo'];
$opcao		= $_GET['op'];

if( $opcao == 'upd' )
{
	// recuperando os dados do agendamento
	$stmt = "SELECT a.usu_codigo, a.proc_codigo, a.med_codigo, p.gex_tipo
		FROM agendamento_exame_lista AS a
		LEFT JOIN procedimento AS p ON a.proc_codigo = p.proc_codigo
		WHERE agexl_codigo = $codigo";
		
	$row = db_getRow( $stmt );
	
	$erro = valida_agenda( $row['usu_codigo'], $data, $row['proc_codigo'], $row['med_codigo'], 
		trim($row['gex_tipo']) );
	
	if( $erro > 0 )
	{
		echo "Erro:$erro";
	}
/*	else if( $erro == 2 )
	{
		echo "Erro:2";
	}*/
	else
	{
	
		$stmt = "UPDATE agendamento_exame_lista SET 
			agexl_data = '$data', 
			agexl_dt_atualizacao = CURRENT_DATE,
			usr_codigo_alt = $id_login
			WHERE agexl_codigo = $codigo";
	
		$sql = db_query($stmt);
	
		reglog($id_login,"Atualizando Agenda Exame. Cod.: $gex_codigo, Data: $data");
	
		echo "Atualizado"; 
	}
} else if( $opcao == 'del' )
{
	$stmt = "DELETE FROM agendamento_exame_lista WHERE agexl_codigo = $codigo";
	$sql = db_query($stmt);
	reglog($id_login,"Removendo Agenda Exame. Cod.: $gex_codigo, Data: $data");
	// NAO alterar esse texto
	// o arquivo que o chama, compara para atualizar a pagina !
	echo 'reload';
}
