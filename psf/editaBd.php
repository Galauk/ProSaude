<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$endereco = $_GET['endereco'];
$codigo_ficha_familia = $_GET['codigo_ficha_familia'];
$psf_dia = $_GET['psf_dia'];
$psf_mes = $_GET['psf_mes'];
$psf_ano = $_GET['psf_ano'];
$data_cadastro_fam  = $psf_dia."-".$psf_mes."-".$psf_ano;
$endereco_fam   = $_GET['endereco_fam'];
$numero_fam  = $_GET['numero_fam'];
$bairro_fam   = $_GET['bairro_fam'];
$cep_fam  = $_GET['cep_fam'];
$municipio_fam   = $_GET['municipio_fam'];
$segmento   = $_GET['segmento'];
$area_fam  = $_GET['area_fam'];
$micro_area_fam  = $_GET['micro_area_fam'];
$tipo_casa_fam   = $_GET['tipo_casa_fam'];
$destino_lixo_fam   = $_GET['destino_lixo_fam'];
$tratamento_agua_fam   = $_GET['tratamento_agua_fam'];
$abastecimento_agua_fam   = $_GET['abastecimento_agua_fam'];
$destino_fezes_fam   = $_GET['destino_fezes_fam'];
$plano_fam   = $_GET['plano_fam'];
$plano_nome_fam   = $_GET['plano_nome_fam'];
$procura_unidade_fam   = $_GET['procura_unidade_fam'];
$comunicacao_meios_fam   = $_GET['comunicacao_meios_fam'];
$grupo_fam   = $_GET['grupo_fam'];
$transporte_meios_fam   = $_GET['transporte_meios_fam'];
$tipo_forro_fam  = $_GET['tipo_forro_fam'];
$tipo_cobertura_fam  = $_GET['tipo_cobertura_fam'];
$tipo_piso_fam  = $_GET['tipo_piso_fam'];
$renda_fam  = $_GET['renda_fam'];
$animais_fam  = $_GET['animais_fam'];
$qnt_animais_fam  = $_GET['qnt_animais_fam'];
$cond_criacao_fam  = $_GET['cond_criacao_fam'];
$qnt_comodos_fam = $_GET['qnt_comodos_fam'];
$conservacao_dom_fam  = $_GET['conservacao_dom_fam'];
$ativo_fam   = $_GET['ativo_fam'];
$tel = $_GET['tel'];
$ddd = $_GET['ddd'];
$tel_fam = $tel.$ddd;
$energia_fam   = $_GET['energia_fam'];
$bolsa_fam  = $_GET['bolsa_fam'];
$tipo_logradouro_fam  = $_GET['tipo_logradouro_fam'];



$familia = "select * from psf where codigo_ficha_familia = $codigo_ficha_familia and numero_fam = $numero_fam";
$qryFam = pg_query($familia);
$umaLinha = pg_fetch_array($qryFam);
$codigo_fam = $umaLinha['codigo_fam'];




$stmt = "UPDATE SET psf ( 
	codigo_ficha_familia = $codigo_ficha_familia, 
	data_cadastro_fam = $data_cadastro_fam, 
	endereco_fam = UPPER('$endereco_fam'), 
	numero_fam = $numero_fam, 
	bairro_fam = UPPER('$bairro_fam'), 
	cep_fam = $cep_fam, 
	municipio_fam = UPPER('$municipio_fam'),, 
	segmento = $segmento, 
	area_fam = $area_fam, 
	micro_area_fam = $micro_area_fam, 
	ativo_fam = $ativo_fam, 
	tipo_logradouro_fam = $tipo_logradouro_fam
	where codigo_fam = $codigo_fam
	";
	$qry = pg_query($stmt);
	echo $stmt;



?>