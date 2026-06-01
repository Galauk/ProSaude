<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

$Json = new Services_JSON;

/**
 * Busca o médico dependendo da especialidade escolhida
 * Depende das variáveis:
 * - esp_codigo
*/ 
if( $acao == 'busca_medico' )
{
	$esp_codigo = intval($_GET['esp_codigo']);
	
	$stmt = "SELECT med_codigo, med_nome FROM medico WHERE med_codigo IN ( SELECT med_codigo FROM medico_especialidade WHERE esp_codigo={$esp_codigo} ) or med_codigo = '2256' AND med_codigo IN ( SELECT med_codigo FROM view_qtde_grade WHERE gra_data >= CURRENT_DATE - 30 ) ORDER BY med_nome";
	
	$resp = array();
	$qry = db_query($stmt);
	while( $row = pg_fetch_array($qry) )
		$resp[] = array( 'med_codigo' => $row[0], 'med_nome' => $row[1] );
	
	
	$output = $Json->encode( $resp );
	
	echo $output;

}
/**
 * Busca as demais informações do agente
 * Depende das variáveis:
 * - agt_codigo
*/
else if( $acao == 'busca_agente' )
{
	$agt_codigo = intval($_GET['agt_codigo']);
	
	$stmt = "SELECT agt_numero, agt_responsavel FROM agente ".
		"WHERE agt_codigo={$agt_codigo} ";
	
	$row = db_getRow( $stmt );
	
	if( empty($row) )
		$resp = array( 'agt_numero' => '...', 'agt_responsavel' => '...' );
	else
		$resp = array( 'agt_numero' => $row[0], 'agt_responsavel' => $row[1] );
		
	$output = $Json->encode( $resp );
	
	echo '('. $output . ')';

}
/**
 * Mostra os horários disponíveis
 * Depende das variáveis:
 * - med_codigo
 * - uni_codigo
 * - esp_codigo
 * - data
*/
else if( $acao == 'busca_horario' )
{
	$sql = "SELECT
				b.gra_data,
				b.gra_hora_ini,
				COALESCE(
					(SELECT a.qtde FROM view_qtde_grade as a
						WHERE a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo'
						and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini
						ORDER by gra_data limit 1),
					0) -
				COALESCE(
					(SELECT qtde FROM view_qtde_medico as c
						WHERE c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo'
							and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),
					0 ) as qtdegeral
			FROM view_qtde_grade as b
			WHERE b.med_codigo = '$med_codigo'
				and b.uni_codigo = '$uni_codigo'
				and b.gra_data = '$data'
				and b.esp_codigo = '$esp_codigo'
				and
					COALESCE(
						(SELECT a.qtde FROM view_qtde_grade as a
							WHERE a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo'
							and a.gra_data >= b.gra_data ORDER BY gra_data LIMIT 1),
						0) -
					COALESCE(
						(SELECT qtde FROM view_qtde_medico as c
							WHERE c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo'
							and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),
						0) != 0
			ORDER by b.gra_data , b.gra_hora_ini";
	
	$resp = array();
	$qry = db_query($sql);
	
	while( $row = pg_fetch_array($qry) )
		$resp[] = $row;
	
	$output = $Json->encode( $resp );
	
	echo $output;
	
}
/**
 * Se for pega_pac_dados, buscar as infos pelo usu_codigo (via selecionar do popup)
 * Se for busca_pac_prontuario, buscar as infos pelo codigo do prontuario
 * Depende das variáveis:
 * - usu_prontuario OU usu_codigo
*/
else if( $acao == 'pega_pac_dados' || $acao = 'busca_pac_prontuario' )
{
	//var_dump($_GET);
	
	$campo = ( $acao == 'pega_pac_dados' ?
				'usu_codigo' :
				'usu_prontuario' );
	
	$busca = ( $acao == 'pega_pac_dados' ?
				intval($_GET['usu_codigo']) :
				("'".$_GET['usu_prontuario']."'") );
	
	$stmt = "SELECT u.usu_codigo, u.usu_prontuario, (c.cid_nome || ' - ' || c.uf_sigla) AS cidade, ".
				"usu_mae, usu_nome, TO_CHAR(usu_datanasc,'dd/mm/yyyy') as data ".
			"FROM usuario AS u ".
			"LEFT JOIN cidade AS c ON u.muni_cd_cod_ibge_resid = c.cid_codigo_ibge ".
			"WHERE {$campo} = {$busca}";
			
	$row = db_getRow($stmt);
	
	// arrumando prontuário...
	$row['usu_prontuario'] = (  $acao == 'busca_pac_prontuario' ? $usu_prontuario : $row['usu_prontuario'] );
	
	$output = '( {
				"ok" : '. (	empty($row['usu_nome']) ? 'false' : 'true' ) .',
				"usu_codigo" : "'.$row['usu_codigo'].'",
				"usu_prontuario" : "'.$row['usu_prontuario'].'",
				"usu_mae" : "'.$row['usu_mae'].'",
				"usu_nome" : "'.$row['usu_nome'].'",
				"cidade" : "'.$row['cidade'].'",
				"data" : "'.$row['data'].'"
				} )';
				
	echo $output;
}

