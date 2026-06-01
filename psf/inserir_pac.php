<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$codigo_integrantes_fam = $_GET['codigo'];
	$nome = $_GET['nome'];
	$nascimento = $_GET['nascimento'];
	$mae = $_GET['mae'];
	$cidade = $_GET['cidade'];
	$idade = $_GET['idade'];
	$codigo_ficha_familia = $_GET['codigo_ficha_familia'];
	$numero_fam = $_GET['numero_fam'];
	$codigo_ficha_familia = $_GET['codigo_ficha_familia'];
	$familia = "select * from psf where codigo_ficha_familia = $codigo_ficha_familia and numero_fam = $numero_fam";
	$qryFam = pg_query($familia);
	$umaLinha = pg_fetch_array($qryFam);
	$codigo_fam = $umaLinha['codigo_fam'];
	
		$stmt = "INSERT INTO integrantes_familia ( 
			codigo_fam, 
			usu_codigo
			 ) VALUES ( 
			'$codigo_fam', 
			'$codigo_integrantes_fam'
			 )";
		//echo $stmt;
	   $qry = pg_query($stmt);

?>