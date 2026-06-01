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
	$arr =  array("","2014","2015","2016","2017","2018","2019");
	$var = $arr[$vlr];
	$and_ano = "AND to_char(age_data,'yyyy')='".$var."'";
}

if($_REQUEST['uni_codigo']!=0) {
	$and_uni = " AND ate.uni_codigo = '".$_REQUEST['uni_codigo']."' ";
	$cab = pg_fetch_array(pg_query("select uni_desc from unidade where uni_codigo='".$_REQUEST['uni_codigo']."'"))[0];
}

if($_REQUEST['usr_codigo']!=0) {
	$and_usr = " AND ate.med_codigo = '".$_REQUEST['usr_codigo']."' ";
	$cabs = pg_fetch_array(pg_query("select usr_nome from usuarios where usr_codigo='".$_REQUEST['usr_codigo']."'"))[0];
}

cabecario_rel("Quantidade de Atendimento Medico Anual <font color=blue>$cab</font>  <font color=red>$cabs</font>",null,null);

if($_REQUEST['tp_rel']==0){
	$sql = "select count(age.age_codigo) as total,to_char(age_data,'yyyy') as ano,usr_nome as nome from agendamento as age 
				join atendimento as ate on age.age_codigo = ate.age_codigo
				join pre_consulta as pre on pre.age_codigo = age.age_codigo
				join usuarios as usr on usr.usr_codigo=age.med_codigo
				where usr_tipo_medico = 'M' and usr.usr_codigo not in(1) $and_ano $and_uni $and_usr
				group by to_char(age_data,'yyyy'),usr_nome
				order by to_char(age_data,'yyyy')";
				$filtro = "Medico";
}
if($_REQUEST['tp_rel']==1){
	$sql = "select count(age.age_codigo) as total,to_char(age_data,'yyyy') as ano,uni_desc as nome from agendamento as age 
				join atendimento as ate on age.age_codigo = ate.age_codigo
				join pre_consulta as pre on pre.age_codigo = age.age_codigo
				join usuarios as usr on usr.usr_codigo=age.med_codigo
				join unidade as uni on uni.uni_codigo=ate.uni_codigo
				where usr_tipo_medico = 'M' and usr.usr_codigo not in(1) $and_ano $and_uni $and_usr
				group by to_char(age_data,'yyyy'),uni_desc
				order by to_char(age_data,'yyyy'),total desc";
				$filtro = "Unidade";
}
if($_REQUEST['tp_rel']==2){
	$sql = "select count(age.age_codigo) as total,to_char(age_data,'yyyy') as ano from agendamento as age 
				join atendimento as ate on age.age_codigo = ate.age_codigo
				join pre_consulta as pre on pre.age_codigo = age.age_codigo
				join usuarios as usr on usr.usr_codigo=age.med_codigo
				join unidade as uni on uni.uni_codigo=ate.uni_codigo
				where usr_tipo_medico = 'M' and usr.usr_codigo not in(1) $and_ano $and_uni $and_usr
				group by to_char(age_data,'yyyy')
				order by to_char(age_data,'yyyy')";
				$filtro = "Ano";
}
if($_REQUEST['tp_rel']==3){
	$sql = "select count(age.age_codigo) as total,to_char(age_data,'yyyy') as ano,uni_desc,usr_nome from agendamento as age 
			join atendimento as ate on age.age_codigo = ate.age_codigo
			join pre_consulta as pre on pre.age_codigo = age.age_codigo
			join usuarios as usr on usr.usr_codigo=age.med_codigo
			join unidade as uni on uni.uni_codigo=ate.uni_codigo
			where usr_tipo_medico = 'M' and usr.usr_codigo not in(1) $and_ano $and_uni $and_usr
			group by to_char(age_data,'yyyy'),uni_desc,usr_nome
			order by to_char(age_data,'yyyy')";
}
       
$query=pg_query($sql) or die(pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){							
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
				  <th>".$filtro."</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		}
				echo "<tr>";
				echo "  <td>{$r['nome']}</td>";
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
				  <th>".$filtro."</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		}
				echo "<tr>";
				echo "  <td>{$r['nome']}</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
		}
	} 

	if($tp_rel == 2){							
		echo "  <tr>
				  <th>".$filtro."</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		while($r = pg_fetch_array($query)){

				echo "<tr>";
				echo "  <td>{$r['ano']}</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
		}
	} 

	if($tp_rel == 3){							
		while($r = pg_fetch_array($query)){
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
				  <th>Medico</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
		}
				echo "<tr>";
				echo "  <td>{$r['usr_nome']}</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
		}
	} 
	echo "</table>";
}
rodape_rel();