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
//echo "<pre>".print_r($_REQUEST,1);
if($uni_codigo > 0){
	$andUni = " AND c.conv_codigo = $conv_codigo";
}
if(!$data_final){
	$data_final = Date("d/m/Y");
}

if($data_inicial && $data_final){
	$andData = " AND col.col_data_coleta between '$data_inicial' and '$data_final'";
}

//if($data_final){
//	$andData = " AND '$data_final' >= col.col_data_coleta";
//}



if($tp_rel == 0){
$sql = "SELECT p.proc_nome,
			   COUNT(ci.proc_codigo) AS total 
		  FROM convenio c
		  JOIN convenio_itens ci
		    ON c.conv_codigo = ci.conv_codigo
		  JOIN procedimento p
		    ON p.proc_codigo = ci.proc_codigo
		  JOIN agenda_itens a
		    ON a.coni_codigo = ci.coni_codigo
		  JOIN coleta col
		    ON col.agei_codigo = a.agei_codigo
		 WHERE 1=1
		 	   $andUni
		       $andData
		 GROUP BY p.proc_nome,
			  ci.proc_codigo
		 ORDER BY proc_nome
			  ";
			  
} else {
	
	$sql = "SELECT 	m.med_nome,
	p.proc_nome,
	TO_CHAR(col.col_data_coleta,'DD/MM/YYYY') AS agexl_data,
	COUNT(col.col_codigo) AS total 
  FROM convenio c
  JOIN convenio_itens ci
    ON c.conv_codigo = ci.conv_codigo
  JOIN procedimento p
    ON p.proc_codigo = ci.proc_codigo
  JOIN agenda_itens a
    ON a.coni_codigo = ci.coni_codigo
  JOIN coleta col
    ON col.agei_codigo = a.agei_codigo
  JOIN medico m
    ON m.med_codigo = c.med_codigo
 WHERE 1=1
 		$andUni
	    $andData
 GROUP BY p.proc_nome,
	  m.med_nome,
	  col_data_coleta
ORDER BY med_nome,
	 proc_nome,
	 col_data_coleta
	";
			       
	
}

//echo "<pre>".print_r($sql,1);
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
