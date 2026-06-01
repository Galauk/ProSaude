<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	echo"<pre>".print_r($_GET,1)."</pre>";

	$nome = $_GET['nome'];
	$codigo = $_GET['codigo'];
	$data = $_GET['data'];
	$senha = $_GET['senha'];
	$dia = $_GET['dia'];
	$tamanho = substr($nome,0,1);
	$dataDeHoje = date('d/m/y');
	if($tamanho[0] == 'A' || $tamanho[0] == 'a' )
	{
		$senhaNome = '6b701483362503b8c16b21bf366acadf';
	}else{
		$senhaNome = 'c16b21bf366acadf6b701483362503b8';
	}
	$senhanova = $senhaNome.$codigo.$dia;
	
	if($senha == $senhanova){
	
		$insert="INSERT INTO registro(
		            nome, modulo, validade, dataliberacao, situacao, codigo, senha)
				VALUES('$nome',
					'2',
					'$data',
					'$dataDeHoje',
					'R',
					'$codigo',
					'$senha')";
		$query = pg_query($insert);
			echo "yes".$insert;
			exit();
	}else{
		echo"no";
	}
?>