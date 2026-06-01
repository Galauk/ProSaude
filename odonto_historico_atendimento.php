<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "
<body bgcolor=E6E6E6>
<link href='estilo.css' rel='stylesheet' type='text/css'>";

//------------------------------------------------------------------>

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age['usu_codigo'];

$stmt 	= "select agt_codigo,to_char(age_data,'DD/MM/YYYY') as age_data,age_codigo,med_codigo,age_hora,
			usu_codigo,age_tipo,age_atendido,age_paciente,uni_codigo,age_item,esp_codigo 
			from agendamento 
			where usu_codigo='$usu_codigo' 
			order by to_char(age_data,'YYYY') desc,to_char(age_data,'MM') desc,to_char(age_data,'DD') desc";

$sql 	= pg_query($stmt);

 echo "
	<table width='900' cellspacing='1' cellpadding='4' border='0'>
		<tr bgcolor='CCCCCC'>
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Hora</font></td>
			<td width=100 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Especialidade</font></td>
			<td width=250 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Médico</font></td>
			<td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Unidade</font></td>
        </tr>";
		
while($row=pg_fetch_array($sql)) 
{
	if($row['age_atendido'] == "S") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
	else
	if($row['age_atendido'] == "N") { $bold_font_open="Agendado"; }
	else
	if($row['age_atendido'] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
	else
	if($row['age_atendido'] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
 	$esp=pg_fetch_array(pg_query("select * from especialidade where esp_codigo='$row[esp_codigo]'"));
	$med=pg_fetch_array(pg_query("select * from medico where med_codigo='$row[med_codigo]'"));
	$uni=pg_fetch_array(pg_query("select * from unidade where uni_codigo='$row[uni_codigo]'"));
	
	echo "
	<tr bgcolor=FFFFFF>
         <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
			<a href='#' OnClick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a>
		</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_data]</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_hora]</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$esp[esp_nome]</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$med[med_nome]</td>
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$uni[uni_desc]</td>
	</tr>";
}
echo "</table>";



?>