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
$usu_codigo = $_GET["usu_codigo"];

if($usu_codigo > 0){
	$andUsu = " AND vac.usu_codigo=$usu_codigo";
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
	$sql = " SELECT uni.uni_desc,
	                usu.usu_nome,
			        COUNT(usu.usu_codigo) AS total
			   FROM vacina_usuario AS vac
			   JOIN usuario AS usu
			     ON usu.usu_codigo=vac.usu_codigo
			   JOIN controlefracionado AS cont
			     ON cont.cont_codigo=vac.cont_codigo
			   JOIN itens_movimento AS ite
			     ON ite.ite_codigo=cont.ite_codigo
			   JOIN produto AS pro
			     ON pro.pro_codigo=ite.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN movimento AS mov
			     ON mov.mov_codigo=ite.mov_codigo
			   JOIN setor AS set
			     ON set.set_codigo=mov.set_saida
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			  WHERE 1=1
			        $andUni
			        $andUsu
			        $andData
			  GROUP BY uni.uni_desc,
			           usu.usu_nome
			  ORDER BY uni.uni_desc,
			           usu.usu_nome";
} else {
	$sql = " SELECT uni.uni_desc,
	                usu.usu_nome,
			        pro.pro_nome,
			        vac.vac_dose,
			        TO_CHAR(vac.vac_data,'DD/MM/YYYY') AS vac_data
			   FROM vacina_usuario AS vac
			   JOIN usuario AS usu
			     ON usu.usu_codigo=vac.usu_codigo
			   JOIN controlefracionado AS cont
			     ON cont.cont_codigo=vac.cont_codigo
			   JOIN itens_movimento AS ite
			     ON ite.ite_codigo=cont.ite_codigo
			   JOIN produto AS pro
			     ON pro.pro_codigo=ite.pro_codigo
			    AND pro.gru_codigo=100002
			   JOIN movimento AS mov
			     ON mov.mov_codigo=ite.mov_codigo
			   JOIN setor AS set
			     ON set.set_codigo=mov.set_saida
			   JOIN unidade AS uni
			     ON uni.uni_codigo=set.uni_codigo
			  WHERE 1=1
			        $andUni
			        $andUsu
			        $andData
			  ORDER BY uni.uni_desc,
			           pro.pro_nome,
			           usu.usu_nome,
			           vac.vac_dose";	
}


cabecario_rel("Vacinas por Usuários",$data_inicial,$data_final,$linhaUnidade[uni_desc]);
	
$query=pg_query($sql) or die($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	rodape_rel();
	exit;
} 

echo "<table class=\"lista\">";

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
					  <th>Paciente</th>
					  <th>Total</th>
					</tr>";
		}
		echo "<tr>";
		echo "  <td>{$r['usu_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
	}
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
		}
		
		if($r['usu_nome'] != $usu_nome){
			if($usu_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
			}
			
			$usu_nome = $r['usu_nome'];
			echo "<tr>
			  <th colspan=\"3\">$usu_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Produto</th>
					  <th>Dose</th>
					  <th>Data</th>
					</tr>";
		}
		
		if($r['vac_dose'] == 6){
			$var = "_".MD5($r['usu_nome'].$r['pro_nome']);
			$$var++;
			$dose = "{$$var}º reforço";
		} else {
			$dose = $r['vac_dose']."ª dose";
		}
		
		echo "<tr>";
		echo "  <td>{$r['pro_nome']}</td>";
		echo "  <td>$dose</td>";
		echo "  <td>{$r['vac_data']}</td>";
		echo "</tr>";
	}	
}

echo "</table>";
	
rodape_rel();