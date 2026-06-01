<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$data_final = $_GET["df"];
$tp_rel = $_GET["tp_rel"];
$zerado = $_GET["zerado"];
$set_codigo = $_GET['set_codigo'];
$gru_codigo = $_GET["gru_codigo"];
$where = "WHERE 1=1";

if($set_codigo != 0){
	$where_set_entrada .= "AND set_entrada = $set_codigo";
	$where_set_saida .= "AND set_saida = $set_codigo";
}

if($gru_codigo != ""){
	$where_grupo .= "AND pro.gru_codigo= $gru_codigo ";
}

if($data_final != ""){
	$where .= "AND (mov.mov_data <= '$data_final')";
}

if($tp_rel == 0){
	
	$sql = "SELECT DISTINCT 
				pro.pro_nome,
				pro.pro_codigo,	
			(SELECT 
				SUM(ite_quantidade) 
			FROM 
				movimento m 
			JOIN 
				itens_movimento i ON i.mov_codigo = m.mov_codigo 
			WHERE 
				mov_data <= '$data_final' AND 
				(mov_tipo = 'E' OR mov_tipo = 'T')AND
				i.pro_codigo = pro.pro_codigo
				$where_set_entrada
				$where_grupo) as entrada, 
			(SELECT 
				SUM(ite_quantidade) 
			FROM 
				movimento m 
			JOIN 
				itens_movimento i ON i.mov_codigo = m.mov_codigo 
			WHERE 
				mov_data <= '$data_final' AND 
				(mov_tipo = 'S' OR mov_tipo = 'T') AND
				i.pro_codigo = pro.pro_codigo
				$where_set_saida
				$where_grupo) as saida
			FROM 
				produto AS pro
		  WHERE pro_situacao = 'A'
			ORDER BY 
				pro_nome ASC";
	// die($sql);
} 
// Pega Setor
if ($set_codigo) {
	// die("aqui");
    $sqlSetor = "SELECT 
				setor.set_codigo, setor.set_nome  
			FROM 
				setor
            WHERE 
				setor.set_codigo = $set_codigo";
    $querySetor=pg_query($sqlSetor);
    $rowSetor=pg_fetch_array($querySetor);
	$SetNome=$rowSetor["set_nome"];
	
} else {
	$SetNome = "TODOS";
}
 // Pega Grupo
if ($gru_codigo) {
    $sqlGrupo = "SELECT 
				grupo.gru_codigo, 
				grupo.gru_nome 
            FROM 
				grupo
            WHERE 
				grupo.gru_codigo = $gru_codigo";
    $queryGrupo=pg_query($sqlGrupo);
    $rowGrupo=pg_fetch_array($queryGrupo);
    $GruNome=$rowGrupo["gru_nome"];
}


cabecario_rel("Posi��o de Estoque por Data",$data_inicial,$data_final);
$query=pg_query($sql) or die($sql);
//echo $sql;

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 
$total = 0;
echo "<table class=\"lista\">";
if($tp_rel == 0){
	if($SetNome){
		echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
	}
	echo "<tr>
			<th colspan=\"2\">$SetNome</th>
		</tr>";
	echo"<tr>
			<th>Produto</th>
			<th>Quantidade</th>
		</tr>";	
	while($r = pg_fetch_array($query)){
		$resultado = ($r[entrada] - $r[saida]);
		if ($resultado > 0) {
			echo "<tr>";
			echo "  <td>{$r['pro_nome']} </td>";
			echo "  <td class=\"d\">".number_format($resultado,0,",",".")."</td>";
			echo "</tr>";
			$total += $resultado;
		}
	}
	echo "<tr>";
	echo "  <td><b>Total</b></td>";
	echo "  <td class=\"d\"><b>".number_format($total,0,",",".")."</b></td>";
	echo "</tr>";
}
// FOI COMENTADA A PARTE DO RELAT�RIO ANALITICO
/* else {
	while($r = pg_fetch_array($query)){
		
		if(($zerado == 0 && number_format($r['total'],0) == 0) || number_format($r['total'],0) < 0)
		   continue;
		   
		if($r['set_nome'] != $set_nome){
			if($set_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
			}
			
			$set_nome = $r['set_nome'];
			echo "<tr>
			  <th colspan=\"4\">$set_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Produto</th>
					  <th>Lote</th>
					  <th>Validade</th>
					  <th>Quant.</th>
					</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td>{$r['sal_lote']}</td>";
		echo "  <td>{$r['sal_validade']}</td>";
		echo "  <td class=\"d\">".number_format($r['total'],0,",",".")."</td>";
		echo "</tr>";
		$total += $r['total'];
	}
	echo "<tr>";
	echo "  <td  colspan=\"3\"><b>Total</b></td>";
	echo "  <td class=\"d\"><b>".number_format($total,0,",",".")."</b></td>";
	echo "</tr>";	
}
*/
echo "</table>";
	
rodape_rel();