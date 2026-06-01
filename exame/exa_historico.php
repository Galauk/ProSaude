<SCRIPT LANGUAGE="JavaScript">
function hotkey(eventname) {

  if(eventname.keyCode == 113) {
      window.history.go(-1);
    }
}
</script>
<body onkeydown='hotkey(event)' topmargin=0 leftmargin=0>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$sql=pg_query("select agt_codigo,to_char(age_data,'DD/MM/YYYY') as age_data,age_codigo,med_codigo,age_hora,usu_codigo,age_tipo,age_atendido,age_paciente,uni_codigo,age_item,esp_codigo, age_falta_medico from agendamento where usu_codigo='$usu_codigo' order by to_char(age_data,'YYYY') desc,to_char(age_data,'MM') desc,to_char(age_data,'DD') desc");
$usu = pg_fetch_array(pg_query("select *from usuario where usu_codigo = '$usu_codigo'"));
$usu_nome = $usu[usu_nome];
$Pac=pg_fetch_array(pg_query("select * from usuario where usu_codigo='$usu_codigo'"));
echo " 
       <font color=blue size=3> <b>Codigo Sistema: " . $Pac[usu_codigo] . "<br>" . $Pac[usu_nome] . "<br>Prontuário " . $Pac[usu_prontuario] . "</b></font>"; 

//-------------------------------------------------------------------------------------------------------------
//HISTORICO DE EXAMES
//-------------------------------------------------------------------------------------------------------------
echo "</table><br><br>
<font color=red size=3><b>Exames Agendados</b></font>";
 $query = pg_query("select *from agendamento_exame as ex left join medico as m on 
		    m.med_codigo = ex.med_codigo_responsavel where usu_codigo = $usu_codigo");

 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[usu_codigo]</td>
	<tr bgcolor=CCCCCC>
	 <td width=80 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Laboratorio</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Usuario Cad.</font></td>
	</tr>
	";

$bold_font_open = '';
  $data_atual = date("d/m/Y");
while($rr=pg_fetch_array($query)) {
	   $rw = pg_fetch_array(pg_query("select to_char(agexl_dt_cadastro,'DD/MM/YYYY') as dt_cad,usr_codigo_cad,agexl_status,to_char(agexl_data,'DD/MM/YYYY') as agexl_data from agendamento_exame_lista where agex_codigo = '$rr[agex_codigo]'"));
	   $usu =  pg_fetch_array(pg_query("select *from usuarios where usr_codigo = $rw[usr_codigo_cad]"));
           if($rw[agexl_status] == "R") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
           if($rw[agexl_status] == "A") { $bold_font_open="Agendado"; }
           if($rw[agexl_status] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
           if($rw[agexl_status] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
   echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>";
if($data_atual == $rw[dt_cad]) {
   echo "<a href=$PHP_SELF?usu_codigo=$usu_codigo&acao=delexame&agex_codigo=$rr[agex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_on.jpg border=0></a>&nbsp;&nbsp;";
}
   echo "<a href='#' OnClick=\"window.open('../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=$rr[agex_codigo]&usu_codigo=$usu_codigo&lab=$med_codigo','na','height=400,width=750,status=yes,toolbar=no,menubar=no,location=no');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>

	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rr[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rw[agexl_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$usu[usr_nome]</td>
	</tr>";
}
echo "</table><br><br>";
////---------------------Liberados---------------------------------------
echo "</table><br><br>
<font color=red size=3><b>Exames Liberados</b></font>";
 $queryLib = pg_query("select *from liberacao_exame as ex left join medico as m on 
		    m.med_codigo = ex.med_codigo_responsavel where usu_codigo = $usu_codigo");

 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[usu_codigo]</td>
	<tr bgcolor=CCCCCC>
	 <td width=80 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Laboratorio</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Usuario Cad.</font></td>
	</tr>";
$bold_font_open = '';
  $data_atualLib = date("d/m/Y");
while($rrLib=pg_fetch_array($queryLib)) {
	$select = "select usr_codigo_cad,libexl_status,to_char(libexl_dt_cadastro,'DD/MM/YYYY') as libexl_data from liberacao_exame_lista where libex_codigo =  '$rrLib[libex_codigo]'";
	//echo $select;
	   $rwLib = pg_fetch_array(pg_query($select));
	   $usu =  pg_fetch_array(pg_query("select *from usuarios where usr_codigo = $rw[usr_codigo_cad]"));
           if($rwLib[libexl_status] == "R") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
           if($rwLib[libexl_status] == "A") { $bold_font_open="Agendado"; }
           if($rw[libexl_status] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
           if($rwLib[libexl_status] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
   echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>";
if($data_atualLib == $rwLib['libexl_data']) {
	$lib_cod = $rrLib['libex_codigo'];
   echo "<a href=$PHP_SELF?usu_codigo=$usu_codigo&acao=delib&teste=$lib_cod><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_on.jpg border=0></a>&nbsp;&nbsp;";
}

			
   echo "<a href='#' OnClick=\"window.open('../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=$rr[agex_codigo]&usu_codigo=$usu_codigo&lab=$med_codigo','na','height=400,width=750,status=yes,toolbar=no,menubar=no,location=no');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>

	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rrLib[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rwLib[libexl_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$usu[usr_nome]</td>
	</tr>";
}
echo "</table><br><br>";
///---------------------Consultas ---------------------------------------



echo "</table>
<font color=red size=3><b>Consultas Agendadas</b></font>";
 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
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
  if($row[age_falta_medico] == "M") { $bold_font_open="<font color=brow><b>Falta do Medico</font></b>"; }
 $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo='$row[esp_codigo]'"));
 $med=pg_fetch_array(pg_query("select *from medico where med_codigo='$row[med_codigo]'"));
 $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$row[uni_codigo]'"));
  echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='#' OnClick='window.open(\"../print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_hora]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$esp[esp_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$med[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$uni[uni_desc]</td>
	</tr>";
}
echo "</table><br><br>
<font color=red size=3><b>Atendimento Emergenciais (PAM)</b></font>";
$query = pg_query("select to_char(ate_data,'DD/MM/YYYY') as ate_data,ate_hora,uni_codigo from atendimento where usu_codigo='$usu_codigo'");

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


echo "<br><br><a href='#' onclick='window.close();'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;<a href='../imprimi_infopaciente.php?id_login=$id_login&tpbusca=n&usu_nome=$usu_nome&acao=busca&usu_codigo=$usu_codigo'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg border=0></a>";

if($acao=="delexame") {
   $sql = pg_query("delete from agendamento_exame_lista where agex_codigo = '$agex_codigo'") or die(pg_last_error());
   $query = pg_query("delete from agendamento_exame where agex_codigo = '$agex_codigo'") or die(pg_last_error());

             echo "<SCRIPT LANGUAGE=\"JavaScript\">
               setTimeout(\"location='exa_historico.php?id_login=$id_login&usu_codigo=$usu_codigo'\", 1);
		alert('Exame Excluido com Sucesso');
               </SCRIPT>";
}
if($acao=="delib") {	
	$teste = $_GET['teste'];
   $sqlLib = pg_query("delete from liberacao_exame_lista where libex_codigo = '$teste'");
   $queryLib = pg_query("delete from liberacao_exame where libex_codigo = '$teste'");
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
               setTimeout(\"location='exa_historico.php?id_login=$id_login&usu_codigo=$usu_codigo'\", 1);
		alert('Liberacao Excluida com Sucesso');
               </SCRIPT>";
}

?>
