<?
	$arquivoXml =  "../../WebSocialComum/library/conf/dbConfig.xml";

	$xml = simplexml_load_file($arquivoXml);
	$nome = base64_decode($xml->conexao->nome);
	$host = base64_decode($xml->conexao->host);
	$banco = base64_decode($xml->conexao->dbname);
	$usuario = base64_decode($xml->conexao->user);
	$porta = base64_decode($xml->conexao->porta);
	$senha = base64_decode($xml->conexao->password);

	$db = pg_connect("host=$host dbname=$banco user=$usuario port=$porta password=$senha") or die(pg_last_error());


	pg_query("SET CLIENT_ENCODING=LATIN1");
	pg_query("SET datestyle = 'European, DMY'");

?>