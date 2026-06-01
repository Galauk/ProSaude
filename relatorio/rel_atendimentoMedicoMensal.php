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
	$andUsr = " AND age.med_codigo = $usr_codigo";
}

if($uni_codigo > 0){
	$andUni = " AND age.uni_codigo = $uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= age_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= age_data";
}



#	$sql = "select to_char(age_data,'mm/yyyy') as data,count(age_codigo) as total from agendamento where 1=1 $andUsr $andData group by data,to_char(age_data,'yyyy') order by to_char(age_data,'yyyy') asc";
			       //echo $sql;
	/*$sql = "SELECT usr.usr_nome,
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
			*/

$sql = "SELECT usr.usr_nome, uni.uni_desc,COUNT(age.age_codigo) AS total 
FROM agendamento AS age 
JOIN atendimento as ate on ate.age_codigo = age.age_codigo
JOIN unidade as uni on uni.uni_codigo = age.uni_codigo
JOIN usuarios AS usr ON usr.usr_codigo = age.med_codigo 
WHERE usr_tipo_medico in ('M')
$andUsr  $andUni $andData
GROUP BY usr.usr_nome,uni.uni_desc ORDER BY uni_desc";			 
			// die( $sql); 
			 
		// echo $sql;exit;

cabecario_rel("Quantidade por Atendimentos Mensal por Medico",$data_inicial,$data_final);
 //die($sql);
//if(isset($_GET['sql'])) die($sql);
$query=pg_query($sql) or die(pg_last_error());
$numRows = pg_num_rows($query);

if(!$numRows){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";

		echo "  <tr>
				  <th>Medico</th>				  
				  <th>Quantidade de Atendimentos</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
if($r['uni_desc'] != $uni_desc){			
			if($uni_desc){
					echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
				}
				$uni_desc = $r['uni_desc'];
				echo "<tr>
				  <th colspan=\"2\">$uni_desc</th>
				</tr>";

	}
				echo "  <td>{$r['usr_nome']}</td>";
			echo "  <td>{$r['total']}</td>";
			echo "</tr>";
		}
	echo "</table>";
}
	
rodape_rel();
