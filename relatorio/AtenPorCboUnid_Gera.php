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
$uni_codigo = $_GET["uni_codigo"];

cabecario_rel("Atendimento por CBO por Unidade",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= ate.ate_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= ate.ate_data";
}

if($tp_rel == 0){//verifica se é sintético para montar select e tela;
	/*$sql = "SELECT uni.uni_desc,
		       COUNT(age.age_codigo) AS total
		  FROM unidade AS uni
		  JOIN atendimento AS age
		    ON age.uni_codigo=uni.uni_codigo
		  JOIN usuarios AS usr
		    ON usr.usr_codigo=age.med_codigo
		  JOIN usuario AS usu
		    ON usu.usu_codigo=age.usu_codigo
		 WHERE 1=1
		 	   $andData
	           $andUnidade
		 GROUP BY uni.uni_codigo,
		          uni.uni_desc
		 ORDER BY uni.uni_desc";*/
$sql = "select esp_nome,usr_nome,uni.uni_desc,count(*) as total from atendimento as ate 
join unidade as uni on uni.uni_codigo = ate.uni_codigo
join usuarios as usr on usr.usr_codigo = ate.med_codigo 
join medico_especialidade as mes on mes.med_codigo = usr.usr_codigo
join especialidade as esp on mes.esp_codigo = esp.esp_codigo 
where 1=1
		 	   $andData
	           $andUnidade
group by uni_desc,usr_nome,esp_nome
order by uni_desc

";	           
			   
	//	die($sql);	   
			   
} else {
$sql = "select usr_nome,uni.uni_desc,count(*) as total from atendimento as ate 
join unidade as uni on uni.uni_codigo = ate.uni_codigo
join usuarios as usr on usr.usr_codigo = ate.med_codigo 
join medico_especialidade as mes on mes.med_codigo = usr.usr_codigo
join especialidade as esp on mes.esp_codigo = esp.esp_codigo 
where 1=1
		 	   $andData
	           $andUnidade
			   and esp.esp_codigo in (2145,1054)
group by uni_desc,usr_nome
order by uni_desc

";	
}
//die($sql);
$query=pg_query($sql);
if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		while($r = pg_fetch_array($query)){
			if($r['uni_desc'] != $uni_desc){
				if($uni_desc){
					echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
				}
				
				$uni_desc = $r['uni_desc'];
				echo "<tr>
				  <th colspan=\"5\">$uni_desc</th>
				</tr>";
				echo "  <tr>
						  <th>CBO</th>
						  <th>Profissional</th>
						  <th>Total</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['esp_nome']}</td>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td width=110>{$r['total']}</td>";
			echo "</tr>";
		}	
	} else {
		$c = 0;
		while($r = pg_fetch_array($query)){

		if($r['uni_desc'] != $uni_desc){
			if($r['uni_desc'] != $uni_desc && $c > 0){
					echo "<tr><td style=\"border:none;\" colspan=\"3\"><font size=3><b>Total: $c</b></font></td></tr>";
					echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
					 $c = 0;
			}				
				$uni_desc = $r['uni_desc'];
				echo "<tr>
				  <th colspan=\"5\">$uni_desc</th>
				</tr>";
				echo "  <tr>
						  <th>Profissional</th>
						  <th>Total</th>
						</tr>";
						
			}
			
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td width=110>{$r['total']}</td>";
			echo "</tr>";
			
		    $c = $c + $r['total'];
			
		}
		if($c > 0){
			echo "<tr>";
			echo "  <td align=right><font size=3><b>Total:</td>";
			echo "  <td >$c</b></font></td>";
			echo "</tr>";		
		    $c = 0;
		}			
	}
	
	echo "</table>";
}
rodape_rel();