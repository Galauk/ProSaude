<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();


$gru_codigo = $_GET["gru_codigo"];

cabecario_rel("Relatorio de produtos cadastrados");

if($gru_codigo > 0){
	$andGrupo = " AND p.gru_codigo =$gru_codigo";
}

	$sql = "SELECT pro_nome,gru_nome,pro_codigo,g.gru_codigo 
		  	  FROM produto p
		  	  JOIN grupo g
		    	ON p.gru_codigo = g.gru_codigo
			 WHERE 1=1 
					$andGrupo
		  ORDER BY pro_nome,gru_nome";
//echo $sql;
$query=pg_query($sql);
if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
		echo "  <tr>
				  <th>Produto</th>
				  <th>Grupo</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			  $total = (empty($r['total']))?"0.00":$r['total'];
			  
			echo "<tr>";
			echo "  <td>{$r['pro_nome']}</td>";
			echo "  <td>{$r['gru_nome']}</td>";
			echo "</tr>";
		}
	
	
	echo "</table>";
}
rodape_rel();