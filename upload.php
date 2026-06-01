<?
	/* Defina aqui o tamanho máximo do arquivo em bytes: */
	if($arquivo_size > 1024000) {
		print "<SCRIPT> alert('Seu arquivo não poderá ser maior que 1mb'); window.history.go(-1); </SCRIPT>\n";
		exit;
	}
	
	/* Defina aqui o diretório destino do upload */
	if (!empty($arquivo) and is_file($arquivo)) {
		$caminho = "dataSusUpdate/";
		$caminho = $caminho.$arquivo_name;
		
		/* Defina aqui o tipo de arquivo suportado */
		if ((eregi(".zip$", $arquivo_name)) || (eregi(".txt$", $arquivo_name))){
			copy($arquivo, $caminho);
			echo "<script>alert('Incluso com Sucesso!'); 
							window.location = 'importacaoSigtap.php' </script>";
		}else{
			print "<h1><center>Arquivo não enviado!</center></h1>";
			print "<h2><font color='#FF0000'><center>Caminho ou nome de arquivo Inválido!</center></font></h2>";
		}
	}
?>
