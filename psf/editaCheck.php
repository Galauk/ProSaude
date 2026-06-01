<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$familia = "select * from psf where codigo_ficha_familia = $codigo_ficha_familia and numero_fam = $numero_fam";
$qryFam = pg_query($familia);
$umaLinha = pg_fetch_array($qryFam);
$codigo_fam = $umaLinha['codigo_fam'];
$selecionadas = $_GET['selecionadas'];
$usu_codigo = $_GET['usu_codigo'];
$array_separado = explode(" ",$selecionadas);
$gestante = $_GET['gestante'];



foreach($array_separado as $valor){
	$sql = "select * from doenca where nome_doenca = '$valor'";
	$query = pg_query($sql);
	$linha = pg_fetch_array($query);
	$cod_doenca = $linha['cod_doenca'];
	
	$verifica = "select * from doenca_usuario where usu_codigo = $usu_codigo";
	$qryVerifica = pg_query($verifica);
	$fetch = pg_num_rows($qryVerifica);
	
	if($fetch == ''){
	   $stmt = "insert into doenca_usuario (cod_doenca,usu_codigo)
										values ($cod_doenca,$usu_codigo)";
	}else{
		$stmt = "update doenca_usuario set cod_doenca = $cod_doenca where usu_codigo = $usu_codigo";
	}
	$queryStmt = pg_query($stmt);
	$cod ="update integrantes_familia set doenca_integrante = 'S' where usu_codigo = $usu_codigo";
    $vai = pg_query($cod);
}
	if ($gestante == 'N' || $gestante == ''){
    $sqlGest = "update integrantes_familia set gestante = 'N' where usu_codigo = $usu_codigo";
	}else{
	$sqlGest = "update integrantes_familia set gestante = 'S' where usu_codigo = $usu_codigo";
	}
	$qryGest = pg_query($sqlGest);
?>