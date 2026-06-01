<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";	
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";	
	
	$unidadeOrigem = $_GET['unidadeOrigem'];
	
	$path = "arquivos/";
	$quebra = chr(13).chr(10);//quebra de linha
	$nome = "PacientesPorUnidade";
	
	$seleciona = " SELECT usu_nome,
						  usu_end_rua,
						  usu_end_nr,
						  usu_end_bairro,
						  usu_end_cep,
						  usu_fone,
						  usu_celular
					 FROM usuario
					WHERE uni_origem = $unidadeOrigem ";
	$executa = pg_query($seleciona);
	
	$sql = "SELECT uni_desc
			  FROM unidade
			 WHERE uni_codigo = $unidadeOrigem";
	$exec = pg_query($sql);
	$row = pg_fetch_array($exec);
	$msg = "LISTANDO PACIENTES DA UNIDADE: ".$row['uni_desc'];
	$msg .= $quebra."==================================================================".$quebra.$quebra;
	while ($linha = pg_fetch_array($executa)){
		$msg .= "Nome: ".$linha['usu_nome'].$quebra;
		$msg .= "Endereço: ".$linha['usu_end_rua'].", ".$linha['usu_end_nr'].$quebra;
		$msg .= "Bairro: ".$linha['usu_end_bairro'].$quebra;
		$msg .= "CEP: ".$linha['usu_end_cep'].$quebra;
		$msg .= "Fone: ".$linha['usu_fone'].$quebra;
		$msg .= "Celular: ".$linha['usu_celular'].$quebra;
		$msg .= $quebra."==================================================================".$quebra.$quebra;
	}
	echo criaArquivo($nome, $msg, $path, ".txt");
?>