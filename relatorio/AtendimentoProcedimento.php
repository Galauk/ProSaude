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
$esp_codigo = $_GET["esp_codigo"];
$tp_rel = $_GET["tp_rel"];
$uni_codigo = $_GET['uni_codigo'];

if($proc_codigo > 0){
	$andProc = " AND proc.proc_codigo = $proc_codigo";
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
	$sql = " SELECT proc_nome,
			        count(pat.pat_codigo) AS total
			   FROM unidade AS uni
			   JOIN agendamento AS age
			     ON age.uni_codigo=uni.uni_codigo
			   LEFT JOIN pre_consulta AS pc
			     ON pc.age_codigo=age.age_codigo
			   LEFT JOIN atendimento AS ate
			     ON ate.age_codigo=age.age_codigo
			   LEFT JOIN posto_enfermagem AS pe
			     ON pe.ate_codigo=ate.ate_codigo
			   JOIN procedimento_atendimento AS pat
			     ON (pat.pc_codigo=pc.pc_codigo
			     OR pat.ate_codigo=ate.ate_codigo
			     OR pat.pe_codigo=pe.pe_codigo)
			   JOIN procedimento AS proc
			     ON proc.proc_codigo=pat.proc_codigo
			  WHERE 1=1
			        $andProc
			        $andUni
			        $andData
			  GROUP BY proc.proc_codigo
			  ORDER BY proc.proc_nome";
} else {
	$sql = " SELECT proc_nome,
			        usu_nome,
			        TO_CHAR(age_data, 'DD/MM/YYYY') as age_data
			   FROM unidade AS uni
			   JOIN agendamento AS age
			     ON age.uni_codigo=uni.uni_codigo
			   LEFT JOIN pre_consulta AS pc
			     ON pc.age_codigo=age.age_codigo
			   LEFT JOIN atendimento AS ate
			     ON ate.age_codigo=age.age_codigo
			   LEFT JOIN posto_enfermagem AS pe
			     ON pe.ate_codigo=ate.ate_codigo
			   JOIN procedimento_atendimento AS pat
			     ON (pat.pc_codigo=pc.pc_codigo
			     OR pat.ate_codigo=ate.ate_codigo
			     OR pat.pe_codigo=pe.pe_codigo)
			   JOIN procedimento AS proc
			     ON proc.proc_codigo=pat.proc_codigo
			   JOIN usuario AS usu
			     ON usu.usu_codigo=age.usu_codigo
			  WHERE 1=1
			        $andProc
			        $andUni
			        $andData
			  GROUP BY proc.proc_codigo,
			           usu_nome,
			           age_data
			  ORDER BY proc.proc_nome,
			           usu_nome,
			           age_data";	
}


cabecario_rel("Procedimentos Realizados",$data_inicial,$data_final,$linhaUnidade[uni_desc]);
	
if(isset($_GET['sql'])) die($sql);

$query=pg_query($sql) or die($sql);
echo "<table class=\"lista\">";

if($tp_rel == 0){
	echo "  <tr>
			  <th>Procedimento</th>
			  <th>Quantidade</th>
			</tr>";
	
	while($r = pg_fetch_array($query)){
		echo "<tr>";
		echo "  <td>{$r['proc_nome']}</td>";
		echo "  <td class=\"d\">{$r['total']}</td>";
		echo "</tr>";
	}
} else {
	while($r = pg_fetch_array($query)){
		if($r['proc_nome'] != $proc_nome){
			if($proc_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
			}
			
			$proc_nome = $r['proc_nome'];
			echo "<tr>
			  <th colspan=\"2\">$proc_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Paciente</th>
					  <th>Data</th>
					</tr>";
		}
		
		echo "<tr>";
		echo "  <td>{$r['usu_nome']}</td>";
		echo "  <td>{$r['age_data']}</td>";
		echo "</tr>";
	}	
}

echo "</table>";
	
rodape_rel();