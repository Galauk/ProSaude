<?
session_start();
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Adicionar Agendamento
//------------------------------------------------------------------>
  if($acao=="addagendamento") {
      reglog($id_login,"Agendando Paciente: $age_paciente");
             $qq = pg_query("select *from agendamento where age_data = '$age_data' and age_hora = '$age_hr' and med_codigo = '$med_codigo' and usu_codigo = '$usu_codigo' and age_item = '$age_tipo' and uni_codigo = '$uni_codigo' and age_tipo = '$age_item' and esp_codigo = '$esp_codigo'");
    if(pg_num_rows($qq)!="0") {
       echo "<font color=red size=2><center><b>Este paciente ja esta agendado para esta data</b></center></font><br><br>";
    } else {
         $sql_qtdmed = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, a.gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.age_tipo='$age_tipo' and a.gra_data >= current_date";
         $query = pg_query($sql_qtdmed);
         $query_grm = pg_query("select *from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and agt_codigo = '$agt_codigo' and age_item='$age_item'");
         $grm_mensal = pg_fetch_array($query_grm);
         $query_age = pg_query("select *from agendamento where agt_codigo = '$agt_codigo'");

            $agemed = pg_fetch_array(pg_query("select a.med_codigo, a.uni_codigo, a.qtde, a.age_hora, a.age_data from  view_qtde_medico  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.age_data = '$row[gra_data]' and a.age_hora = '$row[gra_hora_ini]'"));
$rowVerif=pg_fetch_array(pg_query("select  b.gra_data, b.gra_hora_ini, coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini order by gra_data limit 1),0) -
        coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.esp_codigo = '$esp_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as qtdegeral
from view_qtde_grade as b
where b.med_codigo = '$med_codigo'
and b.uni_codigo = '$uni_codigo'
and b.gra_data = '$age_data'
and b.gra_hora_ini = '$age_hr'
and b.esp_codigo = '$esp_codigo'
and coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data order
by gra_data limit 1),0) - coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.esp_codigo = '$esp_codigo' and c.uni_codigo = '$uni_codigo' 
and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) > 0 order by b.gra_data , b.gra_hora_ini"));

/*echo "select  b.gra_data, b.gra_hora_ini, coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini order by gra_data limit 1),0) -
        coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.esp_codigo = '$esp_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as qtdegeral
from view_qtde_grade as b
where b.med_codigo = '$med_codigo'
and b.uni_codigo = '$uni_codigo'
and b.gra_data = '$age_data'
and b.gra_hora_ini = '$age_hr'
and b.esp_codigo = '$esp_codigo'
and coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data order
by gra_data limit 1),0) - coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.esp_codigo = '$esp_codigo' and c.uni_codigo = '$uni_codigo' 
and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) > 0 order by b.gra_data , b.gra_hora_ini";*/

/** Alterado por Dudu @ 16/03/2007 */
/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/
if($rowVerif[qtdegeral]>="1")
{
	/*if( empty($jah_agendado) )
	{
		$nli = pg_fetch_array (pg_query("select nextval('seq_age_codigo')"));
		$age_codigo = $nli[0];
		$sql = pg_query("insert into agendamento (age_codigo,age_data,med_codigo,age_hora,usu_codigo,age_tipo,age_paciente,uni_codigo,age_item,esp_codigo,agt_codigo,usr_codigo_cad,dt_cadastro) values
                         ('$age_codigo','$age_data','$med_codigo','$age_hr','$usu_codigo','$age_item','$age_paciente','$uni_codigo','$age_tipo','$esp_codigo','$agt_codigo','$id_login',NOW())");
	}*/
	if($ja_agendado == "false")
	{
		if($gravou == "true")
		{
			$stmt = "INSERT INTO agendamento 
			( usu_codigo, agt_codigo, med_codigo, esp_codigo, uni_codigo, age_data, age_tipo, age_hora, age_atendido, age_item, dt_cadastro, age_timestamp, usr_codigo_cad,age_emergencia)
			VALUES
			( '$usu_codigo', '$agt_codigo', '$med_codigo', '$esp_codigo', '$uni_codigo', CURRENT_DATE, '$escolha', '$horario', 'S' , 'GE', now(), now(), $id_login,'$emergencia')";
		} else {
			$stmt = "INSERT INTO agendamento 
			( usu_codigo, agt_codigo, med_codigo, esp_codigo, uni_codigo, age_data, age_tipo, age_hora, age_atendido, age_item, dt_cadastro, age_timestamp, usr_codigo_cad,age_emergencia)
			VALUES
			( '$usu_codigo', '$agt_codigo', '$med_codigo', '$esp_codigo', '$uni_codigo', CURRENT_DATE, '$escolha', '$horario', 'S' , 'PC', now(), now(), $id_login,'$emergencia')";
		}
			
		db_query($stmt);
		$select = "select age_codigo from agendamento where usu_codigo = '$usu_codigo' and agt_codigo = '$agt_codigo' and med_codigo = '$med_codigo' and uni_codigo = '$uni_codigo' and esp_codigo = '$esp_codigo' and age_data = CURRENT_DATE and age_tipo = '$escolha' and age_hora = '$horario'";
		$exec_select = pg_query($select);
		$rrr = pg_fetch_array($exec_select);
		$age_codigo = $rrr[0];
	}
	echo "
		<script>
		window.open(\"print_guia.php?uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&agt_codigo=$agt_codigo&usu_codigo=$usu_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo\",null,\"height=500,width=750,status=yes,toolbar=no,menubar=no,location=no\");
		function limpar()
		{
			parent.document.agendamento.pac_codigo.value = '';
			parent.document.agendamento.pac_nome.value = '';
			parent.document.agendamento.pac_nascimento.value = '';
			parent.document.agendamento.pac_mae.value = '';
			parent.document.agendamento.pac_cidade.value = '';
			parent.fazer.location.href = 'agendamento_atd_fazer.php?id_login=$id_login';
			parent.atendimento.location.href = 'agendamento_atd_balcao.php?id_login=$id_login';
		}
		</script>";
	

#           echo "<SCRIPT LANGUAGE=\"JavaScript\">
#                  setTimeout(\"location='$PHP_SELF?age_codigo=$age_codigo&id_login=$id_login&grm_mensal=$grm_mensal&id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&agt_codigo=$agt_codigo&age_tipo=$age_tipo&age_data=$age_data&age_vaga=$age_vaga&usu_codigo=$usu_codigo&age_paciente=$age_paciente&age_hr=$age_hr&acao='\", 0);
#                 </SCRIPT>";
} else {
   //echo "<font color=red size=2><center><b>ERRO:<br></font><font color=000000>Este Médico Não Possui mais vagas diponívels.</font></center></b><br><br>";
	if($ja_agendado == "false")
	{   
		if($gravou == "true")
		{
			$stmt = "INSERT INTO agendamento 
			( usu_codigo, agt_codigo, med_codigo, esp_codigo, uni_codigo, age_data, age_tipo, age_hora, age_atendido, age_item, dt_cadastro, age_timestamp, usr_codigo_cad,age_emergencia)
			VALUES
			( '$usu_codigo', '$agt_codigo', '$med_codigo', '$esp_codigo', '$uni_codigo', CURRENT_DATE, '$escolha', '$horario', 'S' , 'GE', now(), now(), $id_login,'$emergencia')";
		} else {
			$stmt = "INSERT INTO agendamento 
			( usu_codigo, agt_codigo, med_codigo, esp_codigo, uni_codigo, age_data, age_tipo, age_hora, age_atendido, age_item, dt_cadastro, age_timestamp, usr_codigo_cad,age_emergencia)
			VALUES
			( '$usu_codigo', '$agt_codigo', '$med_codigo', '$esp_codigo', '$uni_codigo', CURRENT_DATE, '$escolha', '$horario', 'S' , 'PC', now(), now(), $id_login,'$emergencia')";
		}
			
		db_query($stmt);
		
		$select = "select age_codigo from agendamento where usu_codigo = '$usu_codigo' and agt_codigo = '$agt_codigo' and med_codigo = '$med_codigo' and uni_codigo = '$uni_codigo' and esp_codigo = '$esp_codigo' and age_data = CURRENT_DATE and age_tipo = '$escolha' and age_hora = '$horario'";
		$exec_select = pg_query($select);
		$rrr = pg_fetch_array($exec_select);
		$age_codigo = $rrr[0];
	}
	echo "
		<script> window.open(\"print_guia.php?uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&agt_codigo=$agt_codigo&usu_codigo=$usu_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo\",null,\"height=500,width=750,status=yes,toolbar=no,menubar=no,location=no\");
		function limpar()
		{
			parent.document.agendamento.pac_codigo.value = '';
			parent.document.agendamento.pac_nome.value = '';
			parent.document.agendamento.pac_nascimento.value = '';
			parent.document.agendamento.pac_mae.value = '';
			parent.document.agendamento.pac_cidade.value = '';
			parent.fazer.location.href = 'agendamento_atd_fazer.php?id_login=$id_login';
			parent.atendimento.location.href = 'agendamento_atd_balcao.php?id_login=$id_login';
		}
		</script>";
	

}

 }
}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

if($action=="delage") {
  $sql = pg_query("delete from agendamento where age_codigo = '$age_codigo'");
           echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=&usu_codigo=$usu_codigo'\", 0);
                 </SCRIPT>";
}
$sql=pg_query("select to_char(dt_cadastro,'YYYY-MM-DD') as dt_cadastro,usr_codigo_alt,usr_codigo_cad,agt_codigo,to_char(age_data,'DD/MM/YYYY') as age_data,age_codigo,med_codigo,age_hora,usu_codigo,age_tipo,age_atendido,age_paciente,uni_codigo,age_item,esp_codigo from agendamento where usu_codigo='$usu_codigo' order by to_char(age_data,'YYYY') desc,to_char(age_data,'MM') desc,to_char(age_data,'DD') desc");

 echo "<table width=1500 cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td colspan=2 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
	 <td width=20 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Data</font></td>
	 <td width=20 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Hora</font></td>
	 <td width=100 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td width=700 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Especialidade</font></td>
	 <td width=250 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Médico</font></td>
	 <td width=400 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Unidade</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Usu. Cad.</font></td>
	</tr>";
while($row=pg_fetch_array($sql)) {
  if($row[age_atendido] == "S") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
  if($row[age_atendido] == "N") { $bold_font_open="Agendado"; }
  if($row[age_atendido] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
  if($row[age_atendido] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
 $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo='$row[esp_codigo]'"));
 $med=pg_fetch_array(pg_query("select *from medico where med_codigo='$row[med_codigo]'"));
 $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$row[uni_codigo]'"));
 $pacCad = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$row[usr_codigo_cad]'"));
 $data_hoje = date('Y-m-d');
   echo "<tr bgcolor=FFFFFF>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='#' OnClick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]\",null,\"height=500,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>";
if($row[dt_cadastro]==$data_hoje) {
   echo "<td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>".ChmodBtn($id_login,'delpront','agendamento_atendimento.php?usu_codigo='.$usu_codigo.'&action=delage&age_codigo='.$row[age_codigo])."</td>";
} else {
   echo "<td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_off.jpg></td>";
}
   echo "<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_data]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row[age_hora]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$bold_font_open</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$esp[esp_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$med[med_nome]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$uni[uni_desc]</td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$pacCad[usr_nome]</td>
	</tr>";
}
echo "</table>";
