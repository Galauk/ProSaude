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

cabecario_rel("Quantidade de hiperdia por unidade",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUni = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= h.hiper_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= h.hiper_data";
}

if($tp_rel == 0){
	$sql = "SELECT count(h.usu_codigo) as total,
					uni.uni_desc 
				FROM hiperdia as h 
				LEFT JOIN unidade as uni 
				ON h.uni_codigo = uni.uni_codigo 
				WHERE 1=1
				$andUni
				$andData
				GROUP BY uni_desc";
} 
if($tp_rel == 1) {
	$sql = " SELECT count(h.usu_codigo) as total,
						uni.uni_desc,
						usu_nome,
						to_char(hiper_data,'DD/MM/YYYY') as data
					FROM hiperdia as h 
					LEFT JOIN unidade as uni
					ON h.uni_codigo = uni.uni_codigo 
					JOIN usuario as u 
					ON u.usu_codigo = h.usu_codigo 
					WHERE 1=1
					$andUni
					$andData
					GROUP BY uni_desc,usu_nome,hiper_data
					ORDER BY u.usu_nome";
					
}
if($tp_rel == 2) {
	if($data_inicial){
		$andData2 = " AND '$data_inicial' <= h.hiperac_data_consulta";
	}
	if($data_final){
		$andData2 .= " AND '$data_final' >= h.hiperac_data_consulta";
	}
	
	$sql = "SELECT count(h.hiperac_codigo) as total,
					uni.uni_desc 
				FROM unidade as uni 
				LEFT JOIN hiperdia_acompanhamentos as h 
				ON h.uni_codigo = uni.uni_codigo 
				WHERE 1=1
				$andUni
				$andData2
				GROUP BY uni_desc";
}
if($tp_rel == 3) {
	if($data_inicial){
		$andData2 = " AND '$data_inicial' <= ha.hiperac_data_consulta";
	}
	if($data_final){
		$andData2 .= " AND '$data_final' >= ha.hiperac_data_consulta";
	}
	
	$sql = "SELECT uni.uni_desc,
				       usu_nome,
				       usr_nome ,
				       to_char(hiperac_data_consulta,'DD/MM/YYYY') as data 
				  FROM hiperdia_acompanhamentos ha
				  JOIN hiperdia as h 
				    ON h.hiper_codigo = ha.hiper_codigo
				  JOIN unidade as uni 
				    ON ha.uni_codigo = uni.uni_codigo 
				  JOIN usuario as u 
				    ON u.usu_codigo = h.usu_codigo
				  JOIN usuarios usr
				    ON usr.usr_codigo = ha.usr_codigo 
				 WHERE 1=1
				$andUni
				$andData2
			  ORDER BY data,u.usu_nome";
}
//echo "<pre>".print_r($sql,1);

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Unidade</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['uni_desc']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} 
	if($tp_rel == 1) {
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
						  <th>Data</th>
						  <th>Paciente</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['data']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "</tr>";
		}	
	}
	if($tp_rel == 2){
		echo "  <tr>
				  <th>Unidade</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['uni_desc']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} 
	if($tp_rel == 3) {
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
						  <th>Data</th>
						  <th>Paciente</th>
						  <th>Realizado por:</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['data']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "</tr>";
		}	
	}
	echo "</table>";
}
rodape_rel();