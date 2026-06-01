<script>
	window.print();
</script>
<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	
set_time_limit(0);
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

$pro = $_GET[pro];
$usu_codigo = $_GET[usu_codigo];
$sqlUsuario = "SELECT * FROM usuario WHERE usu_codigo = $usu_codigo";
$queryUsuario = pg_query($sqlUsuario) or die($sqlUsuario.pg_last_error());
$regUsuario = pg_fetch_array($queryUsuario);
cabecario_rel("$regUsuario[usu_nome]");

		
	echo 
	"<table class=\"lista\">
		<tr>
			<th>
				PRODUTO
			</th>
			<th>
				QUANTIDADE
			</th>
			<th>
				LOTE
			</th>
			<th>
				VALIDADE
			</th>
		</tr>";
	$produtos = explode(",",$pro);
	foreach ($produtos as $prod){
		$dados = explode("|", $prod);
		$sqlProduto = "SELECT * FROM produto WHERE pro_codigo = $dados[0]";
		$queryProduto = pg_query($sqlProduto);
		$regProduto = pg_fetch_array($queryProduto);
		echo "
		<tr>
			<td>$regProduto[pro_nome]</td>
			<td>$dados[3]</td>
			<td>$dados[1]</td>
			<td>$dados[2]</td>
		</tr>";		
	}
	
	echo"	
	</table>";
//echo "<pre>".print_r($_SESSION,1);

//echo $_SESSION[logon]->usr_codigo."b";
$sqlUsr = "SELECT * FROM usuarios WHERE usr_codigo =". $_SESSION[id_login];
$queryUsr = pg_query($sqlUsr);

$regUsr = pg_fetch_array($queryUsr);

echo "
<br /><br /><br /><br /><br />";

echo "
<div>
	<div style='float:left;width:49%'>
		______________________________
		<br/>
		$regUsuario[usu_nome]
	</div>
	<div style='float:left;width:50%'>
		______________________________
		<br/>
		$regUsr[usr_nome] 
	</div>
</div>";
rodape_rel();