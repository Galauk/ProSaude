<SCRIPT LANGUAGE="JavaScript">
function hotkey(eventname) {

  if(eventname.keyCode == 113) {
      window.history.go(-1);
    }
</script>
<body onkeydown='hotkey(event)' topmargin=0 leftmargin=0>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>


$sql=pg_query("select agt_codigo,to_char(age_data,'DD/MM/YYYY') as age_data,age_codigo,med_codigo,age_hora,usu_codigo,age_tipo,age_atendido,age_paciente,uni_codigo,age_item,esp_codigo from agendamento where usu_codigo='$usu_codigo' order by to_char(age_data,'YYYY') desc,to_char(age_data,'MM') desc,to_char(age_data,'DD') desc");
$Pac=pg_fetch_array(pg_query("select * from usuario where usu_codigo='$usu_codigo'"));
//$Pac="select * from usuario where usu_codigo='$usu_codigo'";
//echo $Pac;
echo " 
       <font color=red size=3> <b>Paciente: " . $Pac[usu_codigo] . " - " . $Pac[usu_nome] . " Prontuário " . $Pac[usu_prontuario] . "</b></font>"; 
 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[usu_codigo]</td>
	<tr bgcolor=CCCCCC>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Hora</font></td>
	 <td width=100 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Especialidade</font></td>
	 <td width=250 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Médico</font></td>
	 <td width=400 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Unidade</font></td>
	</tr>";
while($row=pg_fetch_array($sql)) {
  if($row[age_atendido] == "S") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
  if($row[age_atendido] == "N") { $bold_font_open="Agendado"; }
  if($row[age_atendido] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
  if($row[age_atendido] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
 $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo='$row[esp_codigo]'"));
  //$esp="select * from especialidade where esp_codigo='$row[esp_codigo]'";
 //echo $esp;
 $med=pg_fetch_array(pg_query("select * from medico where med_codigo='$row[med_codigo]'"));
 $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$row[uni_codigo]'"));
  echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='#' OnClick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_hora]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$esp[esp_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$med[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$uni[uni_desc]</td>
	</tr>";
}
//-------------------------------------------------------------------------------------------------------------
//HISTORICO DO PAM
//-------------------------------------------------------------------------------------------------------------
echo "</table><br><br>
<font color=red size=3>Atendimento - <b>PAM</b></font>";
$query = pg_query("select to_char(ate_data,'DD/MM/YYYY') as ate_data,ate_hora,uni_codigo, ate_data as ate_data2 from atendimento where usu_codigo='$usu_codigo' order by ate_data2 desc ");

 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[usu_codigo]</td>
	<tr bgcolor=CCCCCC>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Hora</font></td>
	 <td width=400 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Unidade</font></td>
	</tr>";
while($rr=pg_fetch_array($query)) {
 $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$rr[ate_codigo]'"));
  echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[ate_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[ate_hora]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$uni[uni_desc]</td>
	</tr>";
}
echo "</table>";
//-------------------------------------------------------------------------------------------------------------
//HISTORICO DE EXAMES
//-------------------------------------------------------------------------------------------------------------
echo "</table><br><br>
<font color=red size=3><b>Exames Agendados</b></font>";
$query = pg_query("select to_char(agexl_data,'DD/MM/YYYY') as agexl_data, atendimento.med_codigo, med_nome, atendimento.proc_codigo, proc_nome, agexl_data as agexl_data2, agexl_status
                   from agendamento_exame_lista as atendimento, medico, procedimento
		   where atendimento.med_codigo = medico.med_codigo 
		   and   atendimento.proc_codigo = procedimento.proc_codigo
		   and   usu_codigo='$usu_codigo'
		   order by agexl_data2 desc");

 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[usu_codigo]</td>
	<tr bgcolor=CCCCCC>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Procedimento</font></td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Laboratorio</font></td>
	</tr>";
$bold_font_open = '';
while($rr=pg_fetch_array($query)) {
           if($rr[agexl_status] == "R") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
           if($rr[agexl_status] == "A") { $bold_font_open="Agendado"; }
           if($rr[agexl_status] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
           if($rr[agexl_status] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
  echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[agexl_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[proc_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[med_nome]</td>
	</tr>";
}
echo "</table>";


echo "<br><br><a href='#' OnClick='window.close()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;<a href='imprimi_infopaciente.php?id_login=$id_login&tpbusca=n&usu_nome=$usu_nome&acao=busca&usu_codigo=$usu_codigo'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg border=0></a>";
