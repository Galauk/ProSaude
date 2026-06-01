<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$txa_codigo = $_GET['txa_codigo'];

// Hemograma tem um formulário proprio
if($txa_codigo === "1"){
	include "valoresReferenciaHemograma.php";
	exit;
}

	$sql = "SELECT ite_itemdoexame,
			       vlr_valordereferencia,
			       ite_tipo_medida,
			       vlr_sexo,
			       vlr_faixa_etaria,
			       proc_nome
			  FROM valoresdereferencia AS v
			  JOIN itensanalise AS i
			    ON i.ite_codigo=v.ite_codigo
			  JOIN tipodeexame AS t
			    ON t.txa_codigo=v.txa_codigo
			  JOIN procedimento AS p
			    ON p.proc_codigo=t.proc_codigo
			 WHERE v.txa_codigo=$txa_codigo
			 ORDER BY ite_itemdoexame,
				  vlr_faixa_etaria_inicio,
				  vlr_faixa_etaria_fim";

cabecario_rel("Valores de Referência",false,false,"<span id=\"titulo\"></span>");
	
$query=pg_query($sql) or die($sql);

echo "<table class=\"lista\">";
   
echo "  <tr>
		  <th>Item de Análise</th>
		  <th>Valor de Referência</th>
		</tr>";

$i=0;
while($r = pg_fetch_array($query)){
	
	if(!$i){
		echo "<script>$(\"#titulo\").html('{$r['proc_nome']}');</script>";
	}
	
	echo "<tr>";
	echo "  <td>{$r['ite_itemdoexame']}</td>";
	echo "  <td>{$r['vlr_valordereferencia']}&nbsp;{$r['ite_tipo_medida']}</td>";
	echo "</tr>";
}
echo "</table>";
	
rodape_rel();