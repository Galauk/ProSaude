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
$agee_situacao = $_GET["agee_situacao"];

cabecario_rel("Agendamento Externo por unidade e especialidade",$data_inicial,$data_final);

if($med_codigo > 0){
	$andPrestador = " AND med_codigo_prestador=$med_codigo";
}
if($esp_codigo > 0){
	$andEspec = " AND agee.esp_codigo=$esp_codigo";
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
$sql = "select count(usu_codigo) as total,esp_nome,med_nome,agee_situacao,proc.proc_nome
			from agendamento_externo agee
			left join  procedimento as proc
			on proc.proc_codigo = agee.proc_codigo
			join medico as med
			on med.med_codigo = agee.med_codigo_prestador
			join especialidade as esp
			on esp.esp_codigo = agee.esp_codigo
			where 1=1
			$andPrestador
			$andEspec
			$andSituac
			$andData
			group by esp_nome,med_nome,agee_situacao,proc.proc_nome
			order by med_nome";
           
} else {
$sql = "select usu.usu_fone,agee.agee_hora,count(agee.usu_codigo) as total,esp_nome,med_nome,agee_situacao,proc.proc_nome,usu_nome
			from agendamento_externo agee
			left join  procedimento as proc
			on proc.proc_codigo = agee.proc_codigo
			join medico as med
			on med.med_codigo = agee.med_codigo_prestador
			join especialidade as esp
			on esp.esp_codigo = agee.esp_codigo
			join usuario as usu
			on usu.usu_codigo = agee.usu_codigo
			where 1=1
			$andPrestador
			$andEspec
			$andSituac
			$andData
			group by esp_nome,med_nome,agee_situacao,proc.proc_nome,usu_nome,usu.usu_fone,agee.agee_hora
			order by med_nome";
           
}
//echo $sql;
$query=pg_query($sql);

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		$total = 0;
		while($r = pg_fetch_array($query)){
			$arr = array(""=>"Agendado","1"=>"Cancelado","2"=>"Entregue","3"=>"Espera","4"=>"Falta","5"=>"Não Loc. Pac.");
			$codsit = $r[agee_situacao];
			$n_situacao = $arr[$codsit];
			if($r['med_nome'] != $med_nome){
				if($med_nome){
					echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
				
					
					$med_nome = $r['med_nome'];
					echo "<tr>
					  <th colspan=\"4\">$med_nome</th>
					</tr>";
				}
				echo "  <tr>
						  <th>Especialidade</th>
						  <th>Agravo</th>
						  <th>Situacao</th>
						  <th>Qtd</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['esp_nome']}</td>";
			echo "  <td>{$r['proc_nome']}</td>";
			echo "  <td>{$n_situacao}</td>";
			echo "  <td>{$r['total']}</td>";
			echo "</tr>";
			$total += $r['total'];
		}
		echo "<tr><td colspan=\"4\" align=\"right\">Total: $total</td></tr>";
	} else {
		while($r = pg_fetch_array($query)){
			$arr = array(""=>"Agendado","1"=>"Cancelado","2"=>"Entregue","3"=>"Espera","4"=>"Falta","5"=>"Não Loc. Pac.");
			$codsit = $r[agee_situacao];
			$n_situacao = $arr[$codsit];
			if($r['esp_nome'] != $esp_nome){
				if($esp_nome){
					echo "<tr><td style=\"border:none;\" colspan=\"4\">&nbsp;</td></tr>";
				}
				
				$esp_nome = $r['esp_nome'];
				if($r['med_nome'] != $med_nome){
					
					
					$med_nome = $r['med_nome'];
					echo "<tr>
					  <th colspan=\"4\"><font color=\"red\">$med_nome</font></th>
					</tr>";
				}
				echo "<tr>
				  <th colspan=\"4\"> <br>$esp_nome</th>
				</tr>";
				echo "  <tr>
						  <th>Paciente</th>
						  <th>Fone</th>
						  <th>Agravo</th>
						  <th>Situacao</th>
						</tr>";
			}
			
			echo "<tr>";
			echo "  <td>{$r['usu_nome']}</td>";
			echo "  <td>{$r['usu_fone']}</td>";
			echo "  <td>{$r['proc_nome']}</td>";
			echo "  <td>{$n_situacao}</td>";
			echo "</tr>";
		}
	}
	
	echo "</table>";
}
rodape_rel();