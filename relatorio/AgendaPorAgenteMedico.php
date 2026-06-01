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
$usr_codigo = $_GET["usr_codigo"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];

if($usr_codigo > 0){
	$andUsr = " AND usr.usr_codigo = $usr_codigo";
}

if($uni_codigo > 0){
	$andUni = " AND age.uni_codigo = $uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age.age_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= age.age_data";
}



if($tp_rel == 0){
	$sql = "SELECT usr.usr_nome,
				   COUNT(age.age_codigo) AS total
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			  JOIN usuario as usu
		        ON usu.usu_codigo = age.usu_codigo
			 WHERE age.age_atendido='T'
			 	   $andUsr
			 	   $andUni
			       $andData
		  GROUP BY usr.usr_nome
		  ORDER BY usr_nome";
} else {
	$sql = "SELECT usr.usr_nome,
				   usu.usu_nome,
				   age_horario,
				   usu_fone,
				   TO_CHAR(age.age_data,'DD/MM/YYYY') as age_data
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			  JOIN usuario as usu
		        ON usu.usu_codigo = age.usu_codigo
			 WHERE age.age_atendido='T'
			 	   $andUsr
			 	   $andUni
			       $andData
		  ORDER BY usr_nome,
		           age.age_data,
		           usu_nome";
	
}
//echo $sql;
cabecario_rel("Agendamentos Remarcados e Transferências",$data_inicial,$data_final);

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {

	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Médico</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} else {
		while($r = pg_fetch_array($query)){
			if($r['usr_nome'] != $usr_nome){
				if($usr_nome){
					echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
				}
				
				$usr_nome = $r['usr_nome'];
				echo "<tr>
				  <th colspan=\"2\">$usr_nome</th>
				</tr>";
				echo "  <tr>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>Data/Hora</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['age_data']}/".substr($r['age_horario'],0,5)."</td>";
			echo "</tr>";
		}	
	}
	
	echo "</table>";
}
rodape_rel();