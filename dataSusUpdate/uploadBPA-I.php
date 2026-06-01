<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";


$tipos_aceitos = array("application/msword");

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

if ($arquivo['size'] > 2097152){
	echo "O arquivo enviado &eacute; muito grande";
	exit();
}
if (array_search($arquivo['type'], $tipos_aceitos) === FALSE) {
	echo "Este tipo de arquivo n&atilde;o &eacute; suportado. Envie apenas arquivos: \".doc\"";
	exit();
}

$f = file_get_contents($arquivo['tmp_name']);
preg_match_all("/([0-9]{10})/",$f, $out);
$procedimentos = $out[0];

$sql = "SELECT proc_codigo_sus FROM procedimento;";
$res = pg_query($sql);

// todos os precedimentos são 'C'
pg_query("UPDATE procedimento SET proc_bpa_tipo='C';");

$sql = "UPDATE procedimento
           SET proc_bpa_tipo='I' 
         WHERE proc_codigo_sus IN ('".implode("','",$procedimentos)."')";


$result = pg_query($sql);
$total = pg_affected_rows($result);

echo "<script>
			alert('Os dados foram atualizados com sucesso!\\n\\n$total registros atualizados!\\n".count($procedimentos)." procedimentos informados');
			window.location = 'importacaoBPA-I.php';
	  </script>";
