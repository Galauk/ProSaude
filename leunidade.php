<?
//------------------------------------------------------------------>
// -> Rotina para ler a unidade no combo e gravar no edit
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

$sql=pg_query("select uni_codigo, uni_desc, uni_localizacao, uni_responsavel ". 
              "from unidade ".
              "where uni_codigo = {$_GET['unidade']} " .
              "order by uni_codigo ");
              
$row=pg_fetch_row($sql);
echo $row[0]; 
?>

