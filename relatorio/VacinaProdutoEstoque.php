<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];
$pro_codigo = $_GET["pro_codigo"];

if($pro_codigo > 0){
	$andPro = " AND pro.pro_codigo=$pro_codigo";
}

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}


if($tp_rel == 0){
	$sql = " SELECT set.set_nome,
	                pro.pro_nome,
			        sum(sal_qtde) as total     
			   FROM saldo AS sal
			   JOIN produto AS pro
			     ON pro.pro_codigo=sal.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN setor AS set
			     ON set.set_codigo=sal.set_codigo
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			   WHERE sal.sal_qtde > 0
			        $andUni
			        $andPro
			   GROUP BY set_nome,
			            pro_nome
			   ORDER BY set.set_nome,
			            pro.pro_nome";
} else {
	$sql = " SELECT set.set_nome,
	                pro.pro_nome,
			        sal.sal_lote,
			        TO_CHAR(sal.sal_validade,'DD/MM/YYYY') as sal_validade,
			        sal.sal_qtde,
			        CASE WHEN sal.sal_validade <= CURRENT_DATE  
			          THEN 1 
			          ELSE 0
			        END AS vencido        
			   FROM saldo AS sal
			   JOIN produto AS pro
			     ON pro.pro_codigo=sal.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN setor AS set
			     ON set.set_codigo=sal.set_codigo
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			   WHERE sal.sal_qtde > 0
			        $andUni
			        $andPro
			   ORDER BY set.set_nome,
			            pro.pro_nome,
			            sal.sal_validade";	
}


cabecario_rel("Vacinas por Produtos (em estoque)",$data_inicial,$data_final,"Atual");
	
if(isset($_GET['sql']))
	die($sql);
$query=pg_query($sql) or die(pg_last_error().$sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";

if($tp_rel == 0){
	
	while($r = pg_fetch_array($query)){
		if($r['set_nome'] != $set_nome){
			if($uni_desc){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$set_nome = $r['set_nome'];
			echo "<tr>
			  <th colspan=\"2\">$set_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Produto</th>
					  <th>Total</th>
					</tr>";
		}
		echo "<tr>";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
	}
} else {
	while($r = pg_fetch_array($query)){	
		if($r['set_nome'] != $set_nome){
			if($set_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
			}
			
			$set_nome = $r['set_nome'];
			echo "<tr>
			  <th colspan=\"4\">$set_nome</th>
			</tr>";
			
			echo "
			<tr>
				<th>Produto</th>
				<th>Lote</th>
				<th>Validade</th>
				<th>Total</th>
			</tr>";
		}
		
		echo "<tr class=\"vencido{$r['vencido']}\">";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td>{$r['sal_lote']}</td>";
		echo "  <td>{$r['sal_validade']}</td>";
		echo "  <td class=\"d\">{$r['sal_qtde']}</td>";
		echo "</tr>";
	}	
}

echo "</table>";
	
rodape_rel();