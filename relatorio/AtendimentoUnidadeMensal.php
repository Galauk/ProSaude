<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();
$tp_rel = $_GET["tp_rel"];



if($_REQUEST['ano']!=0) {
	$vlr = $_REQUEST['ano'];
	$arr = array("--- TODOS ---","01","02","03","04","05","06","07","08","09","10","11","12");
	$var = $arr[$vlr];
	$and_ano = "AND to_char(ate_data,'mm')='".$var."'";
}

if($_REQUEST['uni_codigo']!=0) {
	$and_uni = " AND ate.uni_codigo = '".$_REQUEST['uni_codigo']."' ";
	$cab = pg_fetch_array(pg_query("select uni_desc from unidade where uni_codigo='".$_REQUEST['uni_codigo']."'"))[0];
}


cabecario_rel("Quantidade de Atendimento Medico Anual <font color=blue>$cab</font>  <font color=red>$cabs</font>",null,null);

if($_REQUEST['tp_rel']==0){
	$sql = "select count(*) as total,to_char(ate_data,'mm/yyyy') as ano,uni_desc,ate_tipo from atendimento as ate
				join unidade as uni on uni.uni_codigo = ate.uni_codigo
				where to_char(ate_data,'yyyy')>='2017' $and_ano $and_uni
				group by to_char(ate_data,'mm/yyyy'),uni_desc,ate_tipo
				order by ano asc";
}
if($_REQUEST['tp_rel']==1){
	$sql = "select count(*) as total,to_char(ate_data,'mm/yyyy') as ano,usr_nome 
				from atendimento as ate
				join unidade as uni on uni.uni_codigo = ate.uni_codigo
				join usuarios as usr on usr.usr_codigo = ate.med_codigo
				where to_char(ate_data,'yyyy')>='2017' $and_ano $and_uni
				group by to_char(ate_data,'mm/yyyy'),usr_nome
				order by ano asc";
}
$query=pg_query($sql) or die(pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
	exit;
} 

		
	echo "<table class=\"lista\">";
	

	if($tp_rel == 0){							
		while($r = pg_fetch_array($query)){
		if($r[ate_tipo]=='V') {
			$tipo = "Visita Domiciliar";
		}
		if($r[ate_tipo]=='P') {
			$tipo = "Procedimento";
		}
		if($r[ate_tipo]=='A') {
			$tipo = "Atendimento Individual";
		}
		if($r[ate_tipo]=='0'||$r[ate_tipo]=='') {
			$tipo = "Atendimento Clinico Realizado pelo Profissional no Prontuario";
		}
		if(($r['ano'] != $ano OR $r['uni_desc'] != $uni_desc)){
				if($ano||$uni_desc){
					echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
				}
				$ano = $r['ano'];
				$uni_desc = $r['uni_desc'];
				echo "<tr>
				  <th colspan=\"2\">$ano - $uni_desc</th>
				</tr>";
		echo "  <tr>
				  <th>Tipo do Atendimento</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		}
				echo "<tr>";
				echo "  <td>$tipo</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
		}
	} 


	if($tp_rel == 1){							
		while($r = pg_fetch_array($query)){
	if($r['ano'] != $ano){
				if($ano){
					echo "<tr><td style=\"border:none;\" colspan=\"2\">&nbsp;</td></tr>";
				}
				$ano = $r['ano'];
		echo "<tr>
				  <th colspan=\"2\">$ano</th>
				</tr>";
		echo "  <tr>
				  <th>Medico</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		}

				echo "<tr>";
				echo "  <td>".$r['usr_nome']."</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
		}
	}

	echo "</table>";

rodape_rel();