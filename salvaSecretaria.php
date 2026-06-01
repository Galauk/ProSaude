<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();         
	
	$nome_secretaria = $_POST['nome_secretaria'];
	$cnes_secretaria = $_POST['cnes_secretaria'];
	$cnpj_secretaria = $_POST['cnpj_secretaria'];
	$endereco_secretaria = $_POST['endereco_secretaria'];
	$numero_end_secretaria = $_POST['numero_end_secretaria'];

	$stmt = "INSERT 
			   INTO saude.secretaria (nome_secretaria, 
									  cnes_secretaria, 
									  cnpj_secretaria, 
									  endereco_secretaria, 
									  numero_end_secretaria
							) VALUES (UPPER('$nome_secretaria'), 
									  UPPER('$cnes_secretaria'), 
									  UPPER('$cnpj_secretaria'), 
									  UPPER('$endereco_secretaria'), 
									  UPPER('$numero_end_secretaria') )";
	$qry = pg_query($stmt);

	echo"<script>
			alert('Secretaria Salva');
			location.href='secretaria.php'
		</script>";
?>