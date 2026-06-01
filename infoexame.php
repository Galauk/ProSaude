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
if ($acao=='form_add') {
   echo "<center><h3> Historico de Exames por Paciente </center></h3>";
   $usu=pg_fetch_array(pg_query("select usu_nome from usuario where usu_codigo='$usu_codigo'"));
   echo "<center><h3> Paciente: $usu[0] </h3></center>";
   $sql=pg_query("select distinct to_char(agexl_data,'DD/MM/YYYY') as agexl_data, agexl_data as agexl_data2, agex_data_cad, 
                      agex.agex_codigo, agexl.med_codigo, agexl.usu_codigo,
		      agex.agt_codigo,agex.esp_codigo_responsavel, lab.med_nome, usu_nome, uni_desc, esp_nome,
		      agex.med_codigo_responsavel, med.med_nome as medresp
              from agendamento_exame_lista as agexl, agendamento_exame as agex, medico as lab, 
	           usuario, agente, unidade, medico as med, especialidade
	      where agex.agex_codigo = agexl.agex_codigo
	      and   agexl.med_codigo = lab.med_codigo
	      and   agex.agt_codigo  = agente.agt_codigo
	      and   agente.uni_codigo  = unidade.uni_codigo
	      and   agexl.usu_codigo = usuario.usu_codigo
	      and   agex.esp_codigo_responsavel = especialidade.esp_codigo
	      and   agex.med_codigo_responsavel = med.med_codigo
	      and   agexl.usu_codigo       = $usu_codigo
	      order by agexl_data2"); 

    echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
         	<tr bgcolor=CCCCCC>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Laboratorio</font></td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Unid. Agend.</font></td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Especialidade</font></td>
         	 <td width=250 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Médico</font></td>
         	</tr>";
         while($row=pg_fetch_array($sql)) {
         //  if($row[age_atendido] == "S") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
         //  if($row[age_atendido] == "N") { $bold_font_open="Agendado"; }
         //  if($row[age_atendido] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
         //  if($row[age_atendido] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
         // $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo='$row[esp_codigo]'"));
         // $med=pg_fetch_array(pg_query("select *from medico where med_codigo='$row[med_codigo]'"));
         // $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$row[uni_codigo]'"));
         //	 echo "<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>";
           echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='#' OnClick='window.open(\"agendar_exame_print.php?usu_codigo=$row[usu_codigo]&agex_codigo=$row[agex_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a> </td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='infoexame.php?id_login=$id_login&usu_codigo=$usu_codigo&agex_codigo=$row[agex_codigo]&med_codigo=$row[med_codigo]&acao=listaexame'> <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/veritens_on.jpg border=0> </a></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[agexl_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[uni_desc]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[esp_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[medresp]</td>
	</tr>";
}
echo "</table>";
echo "<br><br><a href='list_pacientes.php?id_login=$id_login&tpbusca=n&palavra_chave=$usu_nome&acao=busca'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;";
} //fim do if ($acao = 'form_add')

if ($acao == 'listaexame') {
   echo "<center><h3> Listagem de Procedimentos por Agendamento de Exames </center></h3>";
   $med=pg_fetch_array(pg_query("select med_nome from medico where med_codigo='$med_codigo'"));
   $usu=pg_fetch_array(pg_query("select usu_nome from usuario where usu_codigo='$usu_codigo'"));
   echo "<center><h3> Laboratorio: $med[0]            Paciente: $usu[0] </h3></center>";
   

   $sql=pg_query("select distinct to_char(agexl_data,'DD/MM/YYYY') as agexl_data, agexl_data as agexl_data2,
                  proc_nome, proc.proc_codigo, agexl_status
              from agendamento_exame_lista as agexl, procedimento as proc 
	      where agexl.proc_codigo      = proc.proc_codigo
	      and   agexl.usu_codigo       = $usu_codigo
	      and   agexl.agex_codigo      = $agex_codigo
	      and   agexl.med_codigo       = $med_codigo
	      order by proc_nome"); 
    echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
         	<tr bgcolor=CCCCCC>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Procedimento</font></td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
         	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Status</font></td>
         	</tr>";
         while($row=pg_fetch_array($sql)) {
           if($row[agexl_status] == "R") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
           if($row[agexl_status] == "A") { $bold_font_open="Agendado"; }
           if($row[agexl_status] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
           if($row[agexl_status] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
           echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[proc_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[agexl_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	</tr>";
}
echo "</table>";
echo "<br><br><a href='infoexame.php?id_login=$id_login&acao=form_add&usu_codigo=$usu_codigo'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;<a href='agendar_exame_print.php?id_login=$id_login&usu_codigo=$usu_codigo'&agex_codigo=$agex_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg border=0></a>";
} //fim do if ($acao == 'listaexame') 

?>
