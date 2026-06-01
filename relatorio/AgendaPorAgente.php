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

cabecario_rel("Agendamento por Unidade de Saude",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND uni.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age.age_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= age.age_data";
}

if($tp_rel == 0){//verifica se � sint�tico para montar select e tela;
	$sql = "SELECT uni.uni_desc,
		       COUNT(age.age_codigo) AS total
		  FROM unidade AS uni
		  JOIN agendamento AS age
		    ON age.uni_codigo=uni.uni_codigo
		  JOIN usuario AS usu
		    ON usu.usu_codigo=age.usu_codigo
		 WHERE 1=1
		 	   $andData
	           $andUnidade AND age.age_tipo = 'AM'
		 GROUP BY uni.uni_codigo,
		          uni.uni_desc
		 ORDER BY uni.uni_desc";
			   
			   
//echo "<pre>";print_r($sql);die();
} else {
	$sql = "SELECT uni.uni_desc,
			       usr.usr_nome,
			       usu.usu_nome,
				   usu.usu_fone,
			       TO_CHAR(age.age_data,'dd/MM/YYYY') AS age_data,
				   age_horario
			  FROM agendamento AS age
			  JOIN unidade AS uni
			    ON age.uni_codigo=uni.uni_codigo
			  LEFT JOIN usuarios AS usr
			    ON usr.usr_codigo=age.med_codigo
			  JOIN usuario AS usu
			    ON usu.usu_codigo=age.usu_codigo
			 WHERE 1=1
			 	   $andData
		           $andUnidade and age_tipo = 'AM'
			 ORDER BY uni.uni_desc,
			          usr.usr_nome,
			          age.age_data,
					  usu.usu_nome";
					  
	//echo "<pre>";print_r($sql);die();
}
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
	} else {
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
						  <th>M�dico</th>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>Data/Hora</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td width=110>{$r['usu_fone']}</td>";
			echo "  <td width=130>{$r['age_data']}/".substr($r['age_horario'],0,5)."</td>";
			echo "</tr>";
		}	
	}
	
	echo "</table>";
}
rodape_rel();