<?
// Conexão Banco antigo de atalaia
$db_correto = pg_connect("host='?' port='?' dbname='?' user='?' password='?'") or die ("nao conecta");
// Conexão banco atual de atalaia
$db_incosistente = pg_connect("host='?' port='?' dbname='?' user='?' password='?'") or die ("Não foi possivel conectar ao servidor");
?>
