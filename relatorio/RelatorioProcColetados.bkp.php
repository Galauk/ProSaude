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


if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo = $uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= agexl.agexl_data";
}

if($data_final){
	$andData = " AND '$data_final' >= agexl.agexl_data";
}



if($tp_rel == 0){
	$sql = " SELECT uni_desc,
					proc_nome,
			        COUNT(agexl_codigo) AS total
			   FROM agendamento_exame_lista AS agexl
			   JOIN unidade AS uni
			     ON uni.uni_codigo=agexl.uni_codigo
			   JOIN procedimento AS proc
			     ON proc.proc_codigo=agexl.proc_codigo
			  WHERE 1=1
			 	   $andUni
			       $andData
			  GROUP BY uni_desc, 
			           proc_nome
			  ORDER BY uni_desc,
			           proc_nome";
			       
} else {
	$sql = " SELECT uni_desc,
					proc_nome,
					TO_CHAR(agexl_data,'DD/MM/YYYY') AS agexl_data,
			        COUNT(agexl_codigo) AS total
			   FROM agendamento_exame_lista AS agexl
			   JOIN procedimento AS proc
			     ON proc.proc_codigo=agexl.proc_codigo
			   JOIN unidade AS uni
			     ON uni.uni_codigo=agexl.uni_codigo
			  WHERE 1=1
			 	   $andUni
			       $andData
			  GROUP BY uni_desc,
			           proc_nome,
			           agexl_data
			  ORDER BY uni_desc,
			           proc_nome,
			           agexl_data";
			       
	
}

cabecario_rel("Procedimentos Coletados",$data_inicial,$data_final);

$query=pg_query($sql) or die($sql."<br />".pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";
$total = 0;

if($tp_rel == 0){	
	while($r = pg_fetch_array($query)){
		if($r['uni_desc'] != $uni_desc){
			if($uni_desc){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$uni_desc = $r['uni_desc'];
			echo "<tr>
			  <th colspan=\"2\">$uni_desc</th>
			</tr>";
			echo "  <tr>
					  <th>Procedimento</th>
					  <th>Total</th>
					</tr>";
		}
		echo "<tr>";
		echo "  <td>{$r['proc_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
		$total += $r['total'];
	}
	echo "<tr><th class=\"d\">Total</th><td>".number_format($total,0,",",".")."</td></tr>";
	unset($uni_desc);
} else {
	while($r = pg_fetch_array($query)){
		if($r['uni_desc'] != $uni_desc){
			if($uni_desc){
				echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
			}
			
			$uni_desc = $r['uni_desc'];
			echo "<tr>
			  <th colspan=\"3\">$uni_desc</th>
			</tr>";
			echo "  <tr>
					  <th>Procedimento</th>
					  <th>Data</th>
					  <th>Total</th>
					</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['proc_nome']}</td>";
		echo "  <td>{$r['agexl_data']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
		$total += $r['total'];
	}	
	echo "<tr><th colspan=\"2\" class=\"d\">Total</th><td>".number_format($total,0,",",".")."</td></tr>";
}

echo "</table>";
	
rodape_rel();