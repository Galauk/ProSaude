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
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];
$pro_codigo = $_GET["pro_codigo"];

if($pro_codigo > 0){
	$andPro = " AND vac.pro_codigo=$pro_codigo";
}

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= vac.vac_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= vac.vac_data";
}

if($tp_rel == 0){
	$sql = " SELECT set.set_nome,
	                pro.pro_nome,
			        COUNT(pro.pro_codigo) AS total
			   FROM vacina_usuario AS vac
			   JOIN controlefracionado AS cont
			     ON cont.cont_codigo=vac.cont_codigo
			   JOIN itens_movimento AS ite
			     ON ite.ite_codigo=cont.ite_codigo
			   JOIN produto AS pro
			     ON pro.pro_codigo=ite.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN setor AS set
			     ON set.set_codigo=cont.set_codigo
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			  WHERE vac.vac_acao='A'
			        $andUni
			        $andPro
			        $andData
			  GROUP BY set.set_nome,
			           pro.pro_nome
			  ORDER BY set.set_nome,
			           pro.pro_nome";
} else {
	$sql = " SELECT set.set_nome,
			        pro.pro_nome,
			        TO_CHAR(vac.vac_data,'DD/MM/YYYY') AS vac_data,
			        TO_CHAR(ite.ite_validade,'DD/MM/YYYY') AS ite_validade,
			        ite.ite_lote,
			        COUNT(pro.pro_codigo) AS total
			   FROM vacina_usuario AS vac
			   JOIN controlefracionado AS cont
			     ON cont.cont_codigo=vac.cont_codigo
			   JOIN itens_movimento AS ite
			     ON ite.ite_codigo=cont.ite_codigo
			   JOIN produto AS pro
			     ON pro.pro_codigo=ite.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN setor AS set
			     ON set.set_codigo=cont.set_codigo
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			  WHERE vac.vac_acao='A'
			        $andUni
			        $andPro
			        $andData
			  GROUP BY set.set_nome,
			           pro.pro_nome,
			           vac.vac_data,
			           ite.ite_lote,
			           ite_validade
			  ORDER BY set.set_nome,
			           pro.pro_nome";	
}


cabecario_rel("Vacinas por Produtos (aplicadas)",$data_inicial,$data_final,$linhaUnidade[uni_desc]);
	
$query=pg_query($sql) or die($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";

if($tp_rel == 0){
	
	while($r = pg_fetch_array($query)){
		if($r['set_nome'] != $set_nome){
			if($set_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$uni_desc = $r['set_nome'];
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
			if($uni_desc){
				echo "<tr><td style=\"border:none;\" colspan=\"5\">&nbsp;</td></tr>";
			}
			
			$set_nome = $r['set_nome'];
			echo "<tr>
			  <th colspan=\"5\">$set_nome</th>
			</tr>
			<tr>
				<th>Produto</th>
				<th>Lote</th>
				<th>Validade</th>
				<th>Aplicado em</th>
				<th>Total</th>
			</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td>{$r['ite_lote']}</td>";
		echo "  <td>{$r['ite_validade']}</td>";
		echo "  <td>{$r['vac_data']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
	}	
}

echo "</table>";
	
rodape_rel();