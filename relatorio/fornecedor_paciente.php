<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$med_codigo = $_GET["med_codigo"];
$tp_rel = $_GET["tp_rel"];

cabecario_rel("Relatorio de gasto com fornecedor por Paciente",$data_inicial,$data_final);

if($for_codigo > 0){
	$andFornecedor = " AND cp.for_codigo=$for_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= cp.comp_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= cp.comp_data";
}

if($tp_rel == 0){//verifica se é sintético para montar select e tela;
	$sql = "SELECT f.for_nome,f.for_codigo,
			SUM(cpi.compi_valor*cpi.compi_quantidade) as total
	from fornecedor as f 
	 JOIN compra_produto as cp 
	ON f.for_codigo = cp.for_codigo 
	 JOIN usuario as u 
	on u.usu_codigo = cp.usu_codigo  
	 JOIN compra_produto_itens as cpi 
	ON cpi.comp_codigo = cp.comp_codigo 
	WHERE 1=1 
			 $andData
	         $andFornecedor
	GROUP BY f.for_nome,f.for_codigo
	ORDER BY f.for_nome";           
} else {
	$sql = " SELECT distinct f.for_codigo,
					for_nome
			   from compra_produto as cp 
			   JOIN fornecedor as f 
				 ON f.for_codigo = cp.for_codigo 
			  WHERE 1=1 
			  $andData
			  $andFornecedor
			  ORDER BY f.for_codigo";
}
$query=pg_query($sql);
//die($sql);
if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Fornecedor</th>
				  <th>Valor Gasto</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			  $total = (empty($r['total']))?"0.00":$r['total'];
			  
			echo "<tr>";
			echo "  <td>{$r['for_nome']}</td>";
			echo "  <td class=\"d\">{$total}</td>";
			echo "</tr>";
		}
	} else {
		while($regFor = pg_fetch_array($query)){
			echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
			echo "
			<tr>
				<th colspan=\"3\">$regFor[for_nome]</th>
			</tr>";
			$sqlPorForn = "select *,to_char(comp_data,'dd/mm/yyyy') as data
							  from compra_produto cp
							  join usuario u
							    on u.usu_codigo = cp.usu_codigo
							 where 1=1
							 $andData
							 and for_codigo = $regFor[for_codigo]
							 order by cp.comp_data ";
			//die($sqlPorForn);
			$queryPorForn = pg_query($sqlPorForn);
			$totalPorForn = 0;
			while($regPorForn = pg_fetch_array($queryPorForn)){				
				echo "
				<tr>
					<th>Paciente</th>
					<th>Data</th>
					<th>Valor</th>
				</tr>
				<tr>
					<td>$regPorForn[usu_nome]</td>
					<td>$regPorForn[data]</td>
					<td>&nbsp;</td>
				</tr>";
				$sqlItens = "SELECT * 
							   FROM compra_produto_itens cpi
							   JOIN compra_produto cp
							     on cpi.comp_codigo = cp.comp_codigo
							  WHERE cpi.comp_codigo = $regPorForn[comp_codigo]
							  $andData
							  $andFornecedor";
				//die($sqlItens);
//echo $sqlItens."<br/>";
				$queryItens = pg_query($sqlItens) or die($sqlItens);
				while($regItens = pg_fetch_array($queryItens)){
					echo "<tr>
							<td>";
								echo "<font color=red>".$regItens[pro_nome];
					echo "  </td>
							<td>
								$regItens[compi_quantidade] &nbsp;
							</td>
							<td>";
								echo "".$regItens[compi_valor];
					echo "	</td>
						  </tr>";
					$totalPorForn += $regItens[compi_valor];
					//echo $regItens[compi_valor]."<br/>";
				}
				
			}
			echo "<tr><td colspan=3 align=right><b>Total Por Fornecedor: $totalPorForn </b></td></tr>";
			$totalPorForn = 0;
		}
		
	}
	
	echo "</table>";
}
rodape_rel();