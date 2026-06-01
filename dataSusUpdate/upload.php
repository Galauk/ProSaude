<?php
session_start();
$pastaDestino = $_SESSION[root].$_SESSION[modulo]."dataSusUpdate/update";
//echo "$pastaDestino";
//include_once $_SESSION[root].$_SESSION[modulo]."dataSusUpdate/update/descompacta.php";

$tipos_aceitos = array("application/zip");

$arquivo = $_FILES['arquivo'];
if (($arquivo['error'] != 0) && $arquivo['error'] != 4){
	echo "Erro de upload. O arquivo n&atilde;o foi carregado.<br>";
	switch($arquivo['error']){
		case UPLOAD_ERR_INI_SIZE :
			echo "O arquivo excede o tamanho permitido.<br>";
			break;
		case UPLOAD_ERR_PARTIAL :
			echo "O upload n&atilde;o foi completo. Tente novamente.";
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			echo "N&atilde;o existe um diret&oacute;rio tempor&oacute;rio para armazenar o arquivo.";
			break;
		case UPLOAD_ERR_CANT_WRITE:
			echo "Erro ao salvar arquivo em disco.";
			break;
		default:
			echo "Erro desconhecido.";
	}
	exit();
}
if (!($arquivo['size'] == 0 OR $arquivo['tmp_name'] == NULL)){

	if ($arquivo['size'] > 2097152){
		echo "O arquivo enviado &eacute; muito grande";
		exit();
	}
	if (array_search($arquivo['type'], $tipos_aceitos) === FALSE) {
		echo "Este tipo de arquivo n&atilde;o &eacute; suportado. Envie apenas arquivos: <br />
		ZIP";
		exit();
	}	
	$nome = $arquivo['name'];
	$path_parts = pathinfo("$nome");

	$nome = $path_parts['basename'];
	$ext = $path_parts['extension'];
	$nome = str_replace(".".$ext, "", $nome);
	//$nome = removeAcentos($nome);
	$nomeArquivo = $nome.".$ext";
	$destino = "$pastaDestino/".$nomeArquivo;
	if(move_uploaded_file($arquivo['tmp_name'], $destino)){
		echo "<script>
				window.location = 'Importacao/descompacta.php?nomeArquivo=$nomeArquivo';
			  </script>";
		
	}else{
		echo "<script>
				alert('Arquivo nao foi Enviado')
			  </script>";
	}
	

}else{
}
?>