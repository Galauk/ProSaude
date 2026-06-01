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

cabecario_rel("Procedimentos Profissionais por Unidade",$data_inicial,$data_final);

if($uni_codigo > 0){
	$andUnidade = " AND age.uni_codigo=$uni_codigo";
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= ate.ate_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= ate.ate_data";
}


$sql = "select usr_nome,proc_nome,count(ate.ate_codigo) as total from agendamento as age
left join atendimento as ate on ate.age_codigo = age.age_codigo
left join pre_consulta as pre on age.age_codigo = pre.age_codigo
left join procedimento_atendimento as pat on pat.ate_codigo = ate.ate_codigo or pat.pc_codigo = pre.pc_codigo
join usuarios as usr on usr.usr_codigo=pat.usr_codigo
join procedimento as proc on proc.proc_codigo = pat.proc_codigo
where 1=1
$andData
$andUnidade
group by proc_nome,usr_nome
order by usr_nome

";	           
			   
	//	die($sql);	   
			   

//die($sql);
$query=pg_query($sql);
if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
		$c = 0;
		while($r = pg_fetch_array($query)){

		if($r['usr_nome'] != $usr_nome){
			if($r['usr_nome'] != $usr_nome && $c > 0){
					echo "<tr><td style=\"border:none;\" colspan=\"3\"><font size=3><b>Total: $c</b></font></td></tr>";
					echo "<tr><td style=\"border:none;\" colspan=\"3\">&nbsp;</td></tr>";
					 $c = 0;
			}				
				$usr_nome = $r['usr_nome'];
				echo "<tr>
				  <th colspan=\"5\">$usr_nome</th>
				</tr>";
				echo "  <tr>
						  <th>Profissional</th>
						  <th>Total</th>
						</tr>";
						
			}
			
			echo "<tr>";
			echo "  <td>{$r['proc_nome']}</td>";
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
	
	
	echo "</table>";
}
rodape_rel();