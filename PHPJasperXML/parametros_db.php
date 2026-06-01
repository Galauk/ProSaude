<?
@session_start();
if( file_exists( COMUM . "/library/conf/dbConfig.xml")){
	$arquivoXml =  COMUM . "/library/conf/dbConfig.xml";
	
} elseif( file_exists($_SESSION[root].$_SESSION[comum]."library/conf/dbConfig.xml")) {
	$arquivoXml = $_SESSION[root].$_SESSION[comum]."library/conf/dbConfig.xml";
	
} else {
	die("Arquivo de configuração do banco de dados não encontrado. (session)");
}

$xml = simplexml_load_file($arquivoXml);
$nome = base64_decode($xml->conexao->nome);
$host = base64_decode($xml->conexao->host);
$banco = base64_decode($xml->conexao->dbname);
$usuario = base64_decode($xml->conexao->user);
$porta = base64_decode($xml->conexao->porta);
$senha = base64_decode($xml->conexao->password);

?>