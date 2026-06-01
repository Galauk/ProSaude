<?php
/**
 * Arquico Ajax do 'fazer_agendamento'
*/

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

$Json = new Services_JSON;

/**
 * Busca o médico dependendo da especialidade escolhida
 * Depende das variáveis:
 * - esp_codigo
*/ 
$acao = $_GET['acao'];
if( $acao == 'busca_medico' )
{
	$esp_codigo = intval($_GET['esp_codigo']);
	$stmt = "SELECT usr_codigo, 
					usr_nome 
			   FROM usuarios 
			  WHERE usr_codigo IN ( SELECT med_codigo 
			  						  FROM medico_especialidade 
									 WHERE esp_codigo={$esp_codigo} ) 
				AND usr_codigo IN ( SELECT med_codigo 
									  FROM view_qtde_grade 
									 WHERE gra_data >= CURRENT_DATE) 
			  ORDER BY usr_nome";
	
	$resp = array();
	$qry = db_query($stmt);
	while( $row = pg_fetch_array($qry) )
		$resp[] = array( 'med_codigo' => $row[0], 'med_nome' => $row[1] );
	
	
	die($row);
	$output = $Json->encode( $resp );
	echo $output;

}
/**
 * Busca as demais informacoes do agente
 * Depende das variaveis:
 * - agt_codigo
*/
else if( $acao == 'busca_agente' )
{
	$agt_codigo = intval($_GET['agt_codigo']);
	
	$stmt = "SELECT agt_numero, 
					agt_responsavel 
			   FROM agente 
			  WHERE agt_codigo = {$agt_codigo} ";
	
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
	$sql = "SELECT *
			  FROM (SELECT b.gra_data,
						   b.gra_hora_ini,
						   (COALESCE(
								(SELECT a.qtde 
								   FROM view_qtde_grade as a
								  WHERE a.med_codigo = '$med_codigo' 
								    AND a.uni_codigo = '$uni_codigo'
								    AND a.gra_data >= b.gra_data 
									AND a.gra_hora_ini = b.gra_hora_ini
								  ORDER by gra_data limit 1), 0) -
						    COALESCE(
								(SELECT qtde 
								   FROM view_qtde_medico as c
								  WHERE c.med_codigo = '$med_codigo' 
								    AND c.uni_codigo = '$uni_codigo'
									AND c.age_data = b.gra_data 
									AND c.age_hora = b.gra_hora_ini
								  LIMIT 1), 0 )) as qtdegeral
					  FROM view_qtde_grade as b
					 WHERE b.med_codigo = '$med_codigo'
					   AND b.uni_codigo = '$uni_codigo'
					   AND b.gra_data = '$data'
					   AND b.esp_codigo = '$esp_codigo'
					 ORDER BY b.gra_data , b.gra_hora_ini) AS x
			 WHERE x.qtdegeral > 0";
	
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

