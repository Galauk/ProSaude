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
$med_destino = $_GET["newmed_codigo"];
$usr_codigo = $_GET["usr_codigo"];
$tp_rel = $_GET["tp_rel"];
$agee_situacao = $_GET["agee_situacao"];

cabecario_rel("Agendamento Externo por M&eacute;dico e Especialidade",$data_inicial,$data_final);

if($usr_codigo > 0){
	$andUsuarios = " AND agee.usr_codigo=$usr_codigo";
}
if($med_codigo > 0){
	$andPrestador = " AND agee.med_codigo_prestador=$med_codigo";
}
if($esp_codigo > 0){
	$andEspec = " AND agee.esp_codigo=$esp_codigo";
}
if($med_destino){
	$andNewMed = " AND agee.med_codigo=$med_destino";
}
if($agee_situacao != '0') {
	if($agee_situacao == "A"){
			$andSituac = " AND agee_situacao is null";
	} else {
			$andSituac = " AND agee_situacao=$agee_situacao";	
	}
}

if($data_inicial){
	$andData = " AND '$data_inicial' <= agee.agee_data";
}
if($data_final){
	$andData .= " AND '$data_final' >= agee.agee_data";
}

if($tp_rel == 0){//verifica se é sintético para montar select e tela;
$sql = "select DISTINCT
			   med.med_codigo,
			   med.med_nome
	      from agendamento_externo agee 
		  join medico as med 
			on med.med_codigo = agee.med_codigo_prestador 
		 where 1=1 
			$andUsuarios
			$andPrestador
			$andEspec
			$andSituac
			$andData
			$andNewMed
			order by med_nome";
		
           
} else {
$sql = "select usu_fone,to_char(agee_data,'dd/mm/yyyy') as agee_data,count(agee.usu_codigo) as total,esp_nome,med.med_codigo,med.med_nome,agee_situacao,proc.proc_nome,newmed.med_nome as medico,usu_nome, usr.usr_nome as medico_solicitante
			from agendamento_externo agee
			left join procedimento as proc
			on proc.proc_codigo = agee.proc_codigo
			join medico as med
			on med.med_codigo = agee.med_codigo_prestador
			join especialidade as esp
			on esp.esp_codigo = agee.esp_codigo
			left join medico as newmed
			on newmed.med_codigo = agee.med_codigo
			left join usuarios as usr
			on usr.usr_codigo=agee.usr_codigo
			join usuario as usu
			on usu.usu_codigo = agee.usu_codigo
			where 1=1
			$andUsuarios
			$andPrestador
			$andEspec
			$andSituac
			$andData
			$andNewMed
			group by agee_data,esp_nome,med.med_nome,agee_situacao,proc.proc_nome,newmed.med_nome,usu_nome,usr.usr_nome,med.med_codigo,usu_fone
			order by med_nome,usu_nome";
	//die($sql);
           
}
//echo $sql;
$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		while($r = pg_fetch_array($query)){
			$arr = array(""=>"Agendado","1"=>"Cancelado","2"=>"Entregue","3"=>"Espera","4"=>"Falta","5"=>"Não Loc. Pac.");
			$codsit = $r[agee_situacao];
			$n_situacao = $arr[$codsit];
			$print_total = "";
			
			if($med_nome){
				echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
			}
			$med_nome = $r['med_nome'];
			echo "<tr>
			  <th colspan=\"4\">Destino: <font color=\"red\">$med_nome</th>
			</tr>";
			echo "  <tr>
					  <th>Medico Solicitante</th>
					  <th>Medico Destino</th>
					  <th>Especialidade</th>
					  <th>Situacao</th>
					  <th>Qtd</th>
					</tr>";
			
			$sqlItens = "select count(usu_codigo) as total,esp_nome,med.med_codigo,med.med_nome,agee_situacao,proc.proc_nome,newmed.med_nome as medico, usr.usr_nome as medico_solicitante
							from agendamento_externo agee
							left join procedimento as proc
							on proc.proc_codigo = agee.proc_codigo
							join medico as med
							on med.med_codigo = agee.med_codigo_prestador
							join especialidade as esp
							on esp.esp_codigo = agee.esp_codigo
							left join medico as newmed
							on newmed.med_codigo = agee.med_codigo
							left join usuarios as usr
							on usr.usr_codigo=agee.usr_codigo
							where 1=1
							$andUsuarios
							AND agee.med_codigo_prestador=$r[med_codigo]
							$andEspec
							$andSituac
							$andData
							$andNewMed
							group by esp_nome,med.med_codigo,med.med_nome,agee_situacao,proc.proc_nome,newmed.med_nome,usr.usr_nome
							order by med_nome";
			$queryItens=pg_query($sqlItens);
			$total_por_medico = 0;
			while($rItens = pg_fetch_array($queryItens)){
				echo "<tr>";
				echo "  <td>{$rItens['medico_solicitante']}</td>";
				echo "  <td>{$rItens['medico']}</td>";
				echo "  <td>{$rItens['esp_nome']}</td>";
				echo "  <td>{$n_situacao}</td>";
				echo "  <td>{$rItens['total']}</td>";
				echo "</tr>";
				$total_por_medico += $rItens['total'];
			}
			echo "<tr><td colspan=\"5\" align=\"right\">Total: $total_por_medico</td></tr>";
			$total_geral += $total_por_medico;

		}	
		echo "<tr><td colspan=\"5\" align=\"right\"><b>Qtde Total: $total_geral</b></td></tr>";
	} else {
		while($r = pg_fetch_array($query)){
			$arr = array(""=>"Agendado","1"=>"Cancelado","2"=>"Entregue","3"=>"Espera","4"=>"Falta","5"=>"Não Loc. Pac.");
			$codsit = $r[agee_situacao];
			$n_situacao = $arr[$codsit];
			if($r['med_nome'] != $med_nome){
				if($med_nome){
					echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
				}
				$medico_sol = $r['medico_solicitante'];
				$medico = $r['medico'];
				$med_nome = $r['med_nome'];
				echo "<tr>
				  <th colspan=\"5\">
					Destino: <font color=red>$med_nome</font> <br>
					Médico Solicitante: $medico_sol<br />
					Medico Destino: $medico
					</th>
				</tr>";
				echo "  <tr>
						  <th>Data</th>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>Especialidade</th>
						  <th>Situacao</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['agee_data']}</td>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['esp_nome']}</td>";
			echo "  <td>{$n_situacao}</td>";
			echo "</tr>";
		}
	}
	
	echo "</table>";
}
rodape_rel();