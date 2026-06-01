<?
// Conex�o Banco antigo de atalaia
$db_correto = pg_connect("host='ibiserver.ibitech.com.br' port='5432' dbname='db_limpo' user='postgres' password='DbManIbi2017'") or die ("nao conecta");
// Conex�o banco atual de atalaia
$db_incosistente = pg_connect("host='ibiserver.ibitech.com.br' port='5432' dbname='db_corumbatai' user='corumbatai' password='CrumIbi2017'") or die ("Não foi possivel conectar ao servidor");
?>
