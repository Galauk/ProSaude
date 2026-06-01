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
$medico = $_GET['medico'];

if($medico){
	$tp_medico = explode("|",$medico);
	if($tp_medico[1] == 1){
		$join = "join usuarios usr on usr.usr_codigo = a.usr_codigo_medico";
		$andUsr = "AND usr_codigo_medico = $tp_medico[0]";
		$col = "usr_nome";
	}else if($tp_medico[1] == 0){
		$join = "join medico med on med.med_codigo = a.med_codigo";
		$andUsr = "AND med.med_codigo = $tp_medico[0]";
		$col = "med_nome as usr_nome";
	}
	$group = "usr_nome, proc_nome";
}else{
	$col = "(case when usr_codigo_medico is null 
			     then med_nome when a.med_codigo is null 
			     then usr_nome end) as usr_nome";
	$join = "left join usuarios usr on usr.usr_codigo = a.usr_codigo_medico 
	         left join medico med on med.med_codigo = a.med_codigo";
	$group = "usr_codigo_medico,med_nome,a.med_codigo,usr_nome,proc_nome";
}

if($data_inicial){
	$andData .= " AND '$data_inicial' <= ai.agei_data";
}

if($data_final){
	$andData .= " AND '$data_final' >= ai.agei_data";
}




if($tp_rel == 0){
	$sql = " select $col,
					proc_nome,
					COUNT(col_codigo) AS total 
		       from agenda_itens ai
		       join coleta c
		         on c.agei_codigo = ai.agei_codigo
		       join agenda a
		         on a.age_codigo = ai.age_codigo
		       join convenio_itens ci
		         on ci.coni_codigo = ai.coni_codigo
		       join procedimento p
		         on p.proc_codigo = ci.proc_codigo
		       $join
			  WHERE 1=1
				   $andUsr
			       $andData
			  GROUP BY $group
			  ORDER BY usr_nome,
			           proc_nome";
       //die($sql);
			       
} else {
	$sql = " select $col,
					proc_nome,
					TO_CHAR(col_data_coleta,'DD/MM/YYYY') AS agexl_data,
					p.proc_codigo,
					COUNT(col_codigo) AS total 
		       from agenda_itens ai
		       join coleta c
		         on c.agei_codigo = ai.agei_codigo
		       join agenda a
		         on a.age_codigo = ai.age_codigo
		       join convenio_itens ci
		         on ci.coni_codigo = ai.coni_codigo
		       join procedimento p
		         on p.proc_codigo = ci.proc_codigo
		       $join
			  WHERE 1=1
				   $andUsr
			       $andData
			  GROUP BY $group,agexl_data,p.proc_codigo
			  ORDER BY usr_nome,
			           proc_nome,
			           agexl_data";
			       
			       
	
}



cabecario_rel("Procedimentos Coletados",$data_inicial,$data_final);

$query=pg_query($sql) or die ("<pre>".print_r($sql,1));
echo "<table class=\"lista\">";
list($medCodigo,$medTipo) = explode('|', $medico);
if ($medTipo==1) {
	$sqlUsr = "SELECT
				 UPPER(usr.usr_nome) AS med_nome
			   FROM 
				 usuarios AS usr
			   WHERE
				 usr.usr_codigo = '".$medCodigo."'";
	$queryUsr = pg_query($sqlUsr);
	$rowUsr = pg_fetch_array($queryUsr);
} else {
	$sqlUsr = "SELECT
				UPPER(med.med_nome) AS med_nome 
			  FROM
				medico AS med
			   WHERE 
				med.med_codigo = '".$medCodigo."'";
	$queryUsr = pg_query($sqlUsr);
	$rowUsr = pg_fetch_array($queryUsr);
}
if($tp_rel == 0){	
	$total = 0;
	$usr_nome = $rowUsr["med_nome"];
	echo"<tr>
			<th colspan=\"2\">$usr_nome</th>
		</tr>";
	echo"<tr>
			<th>Procedimento</th>
			<th>Total</th>
		</tr>";
	while($r = pg_fetch_array($query)){
		echo "<tr>";
		echo "  <td>{$r['proc_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
		$total = $total + $r['total'];	
	}
	echo"<tr>
			<th><span style='float:right'>Total</span></th>
			<th><span style='float:right'>$total</span></th>
		</tr>";
	unset($uni_desc);
} else {
	$total = 0;
	$usr_nome = $rowUsr["med_nome"];
	echo"<tr>
			<th colspan=\"3\">$usr_nome</th>
		</tr>";
	echo"<tr>
			<th>Procedimento</th>
			<th>Total</th>
			<th>Data</th>
		</tr>";
	$proc_codigo = "";
	$cont = 0;
	while($r = pg_fetch_array($query)){
		
		$soma += $r['total'];
		if($proc_codigo != $r['proc_codigo']){
			if($cont >=1){
				echo "<tr><td colspan=3><b>Total: $soma</b></td></tr>";
				$cont = 0;
				$soma = 0;
			}
			
			$proc_codigo = $r['proc_codigo'];
		}
		$cont++;
		echo "<tr>";
		echo "  <td>{$r['proc_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "  <td>{$r['agexl_data']}</td>";
		echo "</tr>";
		
		$total = $total + $r['total'];
		
	}
	echo"<tr>
			<th><span style='float:right'>Total</span></th>
			<th><span style='float:right'>$total</span></th>
			<th></th>
		</tr>";
}
echo "</table>";
	
rodape_rel();