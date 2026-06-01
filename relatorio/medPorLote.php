<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();

//$pro_codigo = $_GET["pro_codigo"];
//$set_codigo = ;
//$lote = $_GET["lote"];

if( $_GET["lote"] != ""){
	
	$lote = "AND ite_lote = '$_GET[lote]'";
	//die("merda".$lote);
}
if($_GET["set_codigo"] != "0"){
	$set_codigo_condicao = "AND set_saida = $_GET[set_codigo]";
}
if($_GET["pro_codigo"] != "0"){
	$pro_codigo_condicao = "AND pro_codigo = $_GET[pro_codigo]";
}


cabecario_rel("Medicamentos Dispensados por Lote",$data_inicial,$data_final);


	$sql = "select to_char(mov_data,'dd/mm/yyyy') as dtdisp,usr.usr_nome,mov_saida,usu_nome, us.usr_nome as med_nome,ITE_QUANTIDADE,ITE_LOTE,MOV_DATA,u.USU_CODIGO 
	FROM itens_movimento im 
	JOIN movimento m on m.mov_codigo = im.mov_codigo 
	JOIN usuario u on u.usu_codigo=m.usu_codigo 
	left JOIN usuarios us ON us.usr_codigo = m.med_codigo_interno
	LEFT JOIN usuarios usr ON usr.usr_codigo = m.usr_codigo
	WHERE 1 = 1 $lote  $set_codigo_condicao $pro_codigo_condicao and mov_tipo = 'S' order by mov_data desc";
	           

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
				echo "  <tr>
						  <th>Data da dispensacao</th>
						  <th>Paciente</th>
						  <th>Lote</th>
						  <th>Usuario que dispensou</th>
						  <th>Medico Solicitante</th>
						  <th>Quantidade</th>
						  </tr>";
	
		while($r = pg_fetch_array($query)){
			
			echo "<tr>";
			echo "  <td>{$r['dtdisp']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['ite_lote']}</td>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['med_nome']}</th>";
			echo "  <td>{$r['ite_quantidade']}</td>";
			echo "</tr>";
			$total += $r[ite_quantidade];
		}	
			echo "<tr>";
			echo "  <td colspan=5 align=right><b>Total</b></td>";
			echo "  <td><b>$total</b></td>";
			echo "</tr>";
		
	echo "</table>";
}
rodape_rel();