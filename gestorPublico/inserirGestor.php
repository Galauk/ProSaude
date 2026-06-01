<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$nome_gestor = $_POST['nome_gestor'];
	$endereco_gestor = $_POST['endereco_gestor'];
	$numero_end_gestor = $_POST['numero_end_gestor'];
	$cpf_gestor = $_POST['cpf_gestor'];
	$rg_gestor = $_POST['rg_gestor'];
	$tel_gestor = $_POST['tel_gestor'];
	$sexo = $_POST['sexo'];
	
	
	echo"<script>
			alert('Secretaria Salva');
			location.href='cadastroGestor.php'
		</script>";

?>