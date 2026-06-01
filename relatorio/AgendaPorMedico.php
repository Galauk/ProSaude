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
	$sql = "SELECT  usr.usr_nome,
					CASE age_atendido 
					 WHEN 'P' THEN  'Pr� - consulta'
					 WHEN 'S' THEN  'Recepcionado'
					 WHEN 'A' THEN  'Atendido'
					 WHEN 'N' THEN  'Agendado'
					 WHEN 'T' THEN  'Transferido'
					 WHEN 'F' THEN  'Faltoso'
					 WHEN 'E' THEN  'Em atendimento'	
					END as age_atendido,
					uni.uni_desc,					
					COUNT(age.age_codigo) AS total 
			  FROM agendamento AS age 
			  JOIN usuarios AS usr 
			    ON usr.usr_codigo = age.med_codigo 
			  JOIN usuario as usu 
			    ON usu.usu_codigo = age.usu_codigo
			  JOIN unidade uni
    			ON uni.uni_codigo = age.uni_codigo  
			 WHERE 1=1
				   $andUsr
			 	   $andUni
			       $andData 				
		     GROUP BY uni.uni_desc,
		     		  usr.usr_nome,
		     		  age_atendido
		     ORDER BY uni.uni_desc,usr_nome";
			       //echo $sql;
/*	$sql = "SELECT usr.usr_nome,
				   COUNT(age.age_codigo) AS total
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			  JOIN usuario as usu
		        ON usu.usu_codigo = age.usu_codigo
			 WHERE age_atendido='N'
			 	   $andUsr
			 	   $andUni
			       $andData
		  GROUP BY usr.usr_nome
		  ORDER BY usr_nome";
			 echo $sql;*/
} else {
	$sql = "SELECT usr.usr_nome,
				   usu.usu_nome,
				   usu_fone,
				   CASE age_atendido 
					 WHEN 'P' THEN  'Pr� - consulta'
					 WHEN 'S' THEN  'Recepcionado'
					 WHEN 'A' THEN  'Atendido'
					 WHEN 'N' THEN  'Agendado'
					 WHEN 'T' THEN  'Transferido'
					 WHEN 'F' THEN  'Faltoso'
					 WHEN 'E' THEN  'Em atendimento'	
					END as age_atendido,
					uni.uni_desc,
				   TO_CHAR(age.age_data,'DD/MM/YYYY') as age_data,age_hora,
	  (select at.ate_hora from atendimento at where at.age_codigo = age.age_codigo order by at.ate_hora desc limit 1) as ate_hora,
	  (select count(*) from atendimento at2 where at2.age_codigo = age.age_codigo) as qtde 
			  FROM agendamento AS age
			  JOIN usuarios AS usr
			    ON usr.usr_codigo = age.med_codigo
			  JOIN usuario as usu
		        ON usu.usu_codigo = age.usu_codigo
		      JOIN unidade uni
    			ON uni.uni_codigo = age.uni_codigo  
			 WHERE 1=1
			 	   $andUsr
			 	   $andUni
			       $andData
		  ORDER BY uni.uni_desc,
		  		   usr_nome,
		           age.age_data,
				   age_hora,
				   ate_hora,
		           usu_nome ";
		// echo $sql;
	
}
//		 echo $sql;exit;

cabecario_rel("Agenda por Medico",$data_inicial,$data_final);
 //die($sql);
if(isset($_GET['sql'])) die($sql);
$query=pg_query($sql) or die(pg_last_error());
$numRows = pg_num_rows($query);

if(!$numRows){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>M�dico</th>				  
				  <th>Status</th>
				  <th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			if($r['uni_desc'] != $unidade){
			echo "<tr>
				  	<th colspan=3>{$r['uni_desc']}</th>	  
				  </tr>";
			echo "<tr>";
			}
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['age_atendido']}</td>";
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
			$unidade = $r['uni_desc'];
		}
	} else {
		echo "  <tr>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>M�dico</th>
						  <th>Status</th>
						  <th>Data</th>
						  <th>Hora Age.</th>
						  <th>Hora Ate.</th>
						  <th>Qtde de atendimento</th>
						</tr>";
		while($r = pg_fetch_array($query)){
			
			/*if($r['usr_nome'] != $usr_nome){
				if($usr_nome){
					echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
				}
				
				$usr_nome = $r['usr_nome'];
				echo "<tr>
				  <th colspan=\"2\">$usr_nome</th>
				</tr>";
				
			}*/
			if($r['uni_desc'] != $unidade){
				echo "<tr>
					  	<th colspan=8>{$r['uni_desc']}</th>	  
					  </tr>";
				echo "<tr>";
			}
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['age_atendido']}</td>";
			echo "  <td>{$r['age_data']}</td>";
			echo "  <td>{$r['age_hora']}</td>";
			echo "  <td>".($r[ate_hora] == '' ? 'N�o atendido' : $r[ate_hora])."</td>";
			echo "  <td>{$r['qtde']}</td>";
			echo "</tr>";
			$unidade = $r['uni_desc'];
		}	
	}
	
	echo "</table>";
}
	
rodape_rel();