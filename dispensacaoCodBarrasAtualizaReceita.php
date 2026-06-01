<?

session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";


$dados = $_POST["itens"];

foreach($dados as $item){
	list($irec_codigo, $quantidade) = explode('|',$item);
	
	$updateDaQuantidade = "UPDATE itemreceita set irec_quantidade = $quantidade, irec_qtde_pendente = $quantidade where irec_codigo = $irec_codigo";
	$query = pg_query($updateDaQuantidade);
}

?>