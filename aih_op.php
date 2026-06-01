<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	// operacao via ajax
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

if( $acao == 'busca_paci_cpf' )
{
	// 44819366904
	//nome, cod, rg, cpf, datanasc, sexo, nome_mae, telefone, endereco, numero, bairro, municipio, cep
	$stmt = "SELECT usu_nome, usu_codigo, usu_rg, usu_cpf, usu_datanasc, usu_sexo, usu_mae, usu_fone, usu_end_rua,
		usu_end_nr, usu_end_bairro, usu_end_cidade, usu_end_cep, 'N' AS aih , usu_prontuario	 
	 FROM usuario 
		WHERE REGEXP_REPLACE( usu_cpf, '[[:space:]*|[:punct:]*]', '', 'gi' ) = '$cpf'";
	$qry = db_query( $stmt );
	if( pg_num_rows($qry) == 0 )
	{
		echo "NOK;$cpf";
	}
	else
	{
		$row = pg_fetch_row($qry);
		echo join( $row, ';' );
	}
}
/** verifica se tem aih em 15 dias */
elseif( $acao == 'verifica' && ! empty($codigo) )
{

	$campo = ( $aih == 'S' ? 'pac_aih_codigo' : 'usu_codigo' );
	$stmt = "SELECT COUNT(aih_codigo), MAX(aih_codigo)
	FROM aih WHERE $campo = $codigo AND CURRENT_DATE - aih_dt_cadastro < 15 
	GROUP BY aih_codigo";
	
	$val = db_getRow($stmt);
	if( $val[0] > 0 )
		print 'NOK;'.$val[1];
	else
		print 'OK;';
	die;
}
// verfica se tem o cpf/cns
else if( $acao == 'busca_med_doc' )
{
	$campo = ( $op == 'cnes' ? 'med_cnes' : 'med_cpf' );
	$stmt = "SELECT med_codigo, med_nome, $campo FROM medico 
			WHERE REGEXP_REPLACE( $campo, '[[:space:]*|[:punct:]*]', '', 'gi' ) = '$doc' AND (prestador_servico = 'N' OR prestador_servico is null) LIMIT 1";
	$qry = db_query($stmt);
	if( pg_num_rows($qry) == 0 )
	{
		echo "NOK;$doc";
	}
	else
	{
		$row = pg_fetch_row($qry);
		echo join( $row, ';' );
	}
}
