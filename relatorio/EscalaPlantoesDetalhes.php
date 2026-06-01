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
$uni_codigo = $_GET["uni_codigo"];
$ordenacao = $_GET["ordem"];




cabecario_rel("Escala de Plant&otilde;es",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND escpla.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= escpla_data";
}
if($data_final){+
	$andData .= " AND '$data_final' >= escpla_data";
}

$sql = "SELECT TO_CHAR(escpla_data,'dd/MM/YYYY') AS escpla_data,
			   escpla_data AS data_pura,
	       	   usr_nome,
			   escpla_hora_inicio,
			   escpla_hora_fim,
			   valor_plantao
	  FROM escala_plantao AS escpla
	  JOIN usuarios AS usr
	  	ON escpla.med_codigo=usr.usr_codigo
	 WHERE 1=1
	 	   $andData
           $andUnidade";
if($ordenacao == 0) {//ordena por data/hora
	$sql .= " ORDER BY escpla_data, escpla_hora_inicio, escpla_hora_fim";
} else {//ordena por nome
	$sql .= " ORDER BY usr_nome, escpla_data, escpla_hora_inicio, escpla_hora_fim, valor_plantao";
}
 // var_dump($sql); die;
$query=pg_query($sql);

$imprimiuCabecalho = 0;

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {

	echo "<table class=\"lista\">";

	while($r = pg_fetch_array($query)) {
		// var_dump($r);
		if ($imprimiuCabecalho == 0) {
			echo "<tr>
				<th>Data</th>
				<th>Hor&aacute;rio</th>
				<th>Dia Semana</th>
				<th>M&eacute;dico</th>
				<th>Valor do Plant&atilde;o (R$)</th>
				
			</tr>";
		}

		echo "<tr>";
		echo "  <td>{$r['escpla_data']}</td>";
		echo "  <td>{$r['escpla_hora_inicio']}-{$r['escpla_hora_fim']}</td>";
		echo "  <td>".diasemana($r['data_pura'])."</td>";
		echo "  <td>{$r['usr_nome']}</td>";
		echo "  <td>" .number_format($r['valor_plantao'],2, ".", "") . "</td>";
		echo "</tr>";
		$imprimiuCabecalho += 1;
	}

	echo "</table>";
}
rodape_rel();
