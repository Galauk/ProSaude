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

if($ate_tipo=='V') { $n = 'Visitas Domiciliares'; }
if($ate_tipo=='P') { $n = 'Procedimentos'; }
if($ate_tipo=='A') { $n = 'Atendimentos Individuais'; }

cabecario_rel("Quantidade De Atendimentos por Profisional ".$n,$data_inicial,$data_final);

//echo "ajajaj".$_REQUEST['usr_codigo'];
if($usr_codigo > 0){
	$andUsu = " AND ate.med_codigo=$usr_codigo";
}
if($data_inicial){
	$andData = " AND '$data_inicial' <= ate.ate_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= ate.ate_data";
}
//die($tp_rel);
//$tp_rel=0;
if($tp_rel == 0){


	$sql = "
		SELECT usr.usr_nome, loc.no_local_atend,
		       COUNT(ate.ate_codigo) AS total
		  FROM atendimento AS ate
		  JOIN usuarios AS usr
		    ON usr.usr_codigo=ate.med_codigo
	    LEFT JOIN tb_local_atend as loc
	    	ON ate.co_local_atend = loc.co_local_atend
		 WHERE  1=1
		and ate_tipo = '$ate_tipo'
		$andUsu	
	 	$andData
			group by usr_nome, no_local_atend
		 ORDER BY usr.usr_nome";
	 // die ($sql);
} else {
	$sql = "select 
                    usr.usr_nome, 
                    usu.usu_nome, 
                    TO_CHAR(ate.ate_data, 'dd/MM/YYYY') as ate_data, 
                    ate.ate_hora,
                    CASE WHEN ate.ate_tipo = 'V' THEN 'Visita domiciliar' 
                    WHEN ate.ate_tipo = 'P' THEN 'Procedimento' 
                    WHEN ate.ate_tipo = 'A' THEN 'Atendimento Individual'
                    ELSE 'Não informado' END as ate_tipo
            from atendimento ate
            INNER JOIN usuario usu ON usu.usu_codigo = ate.usu_codigo
            INNER JOIN usuarios usr ON usr.usr_codigo = ate.med_codigo
            LEFT JOIN tb_local_atend loc ON ate.co_local_atend = loc.co_local_atend 
			WHERE  ate_tipo = '$ate_tipo'
		           $andUsu	
	 	           $andData
			GROUP BY usr.usr_nome,  
                     usu.usu_nome, 
                     ate.ate_data, 
                     ate.ate_hora,
                     ate.ate_tipo
            ORDER BY usr.usr_nome";
}


//echo $sql;

$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "<tr>
				  <th>Profissional</th>";
	  if($ate_tipo != 'V'){
				  echo '<th>Local</th>';
	  }
		echo "<th>Quantidade</th>
				</tr>";
		
		while($r = pg_fetch_array($query)){
			echo "<tr>";
			echo "  <td>{$r['usr_nome']}</td>";
			echo $ate_tipo != 'V' ? "  <td>{$r['no_local_atend']}</td>" : '';
			echo "  <td class=\"d\">{$r['total']}</td>";
			echo "</tr>";
		}
	} else {
	    $c = 0;
		while($r = pg_fetch_array($query)){
		    if($r['usr_nome'] != $usr_nome){
				
				if($r['usr_nome'] != $usr_nome && $c > 0){
				    echo "<tr>
				  <th  style=\"text-align: right;\" colspan=\"5\">Quantidade: $c</th>
				</tr>";
				    $c = 0;
				}
				//$uni_desc = $r['uni_desc'];
				$usr_nome = $r['usr_nome'];
				echo "<tr>
				  <th colspan=\"5\">$usr_nome</th>
				</tr>";
				echo "  <tr>
						  <th>Paciente</th>
						  <th>Data</th>
						  <th>Hora</th>
                          <th>Tipo</th> 
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td width=110>{$r['ate_data']}</td>";
			echo "  <td width=80>{$r['ate_hora']}</td>";
			echo "  <td width=80>{$r['ate_tipo']}</td>";
			echo "</tr>";
			$c = $c + 1;
		}
		if($c > 0){
		    echo "<tr>
				  <th  style=\"text-align: right;\" colspan=\"5\">Quantidade: $c</th>
				</tr>";
		    $c = 0;
		}
	}
	
	echo "</table>";
}
rodape_rel();
