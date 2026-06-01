<script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	//verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//reglog($id_login,"Acessando Agendamento de Exames");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

//$sql_qtdmed = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, to_char(a.gra_data,'DD/MM/YYYY') as gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.gra_data >= current_date";

// $query = pg_query($sql_qtdmed);

 $sql_qtdmed = pg_query("select *from agendamento where age_tipo='EX' and usu_codigo = '$usu_codigo' and age_data >= current_date");

 $query_grm = pg_query("select *from grade_exame where proc_codigo='$proc_codigo'");
 $grm_mensal = pg_fetch_array($query_grm);

 echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=proc_codigo value=$proc_codigo>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=age_paciente value=$age_paciente>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	<input type=hidden name=age_tipo value=EX>
	<input type=hidden name=age_data value=$age_data>
	<input type=hidden name=acao value=add>
	<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>It.Age.</font></td>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Semana</font></td>
	</tr>";
echo "<tr bgcolor=FFFFFF>";
 echo "<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg></td>";
$sel_proc = pg_query("select *from procedimento order by proc_nome");
echo "<td width=300 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><select name=procedimento class=box>";
while($ra=pg_fetch_array($sel_proc)) {
  echo "<option value='$ra[proc_codigo]'>$ra[proc_nome]</option>";

}
 echo "</select></td>
	 <td width=125 align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>"; 
$dd = date("d");
$mm = date("m");
$yy = date("Y");

echo "<select name=dia>";
    for($i=1;$i<=31;$i++) {
	echo ($i==$dd)?"<option value='$i' selected>$i</option>":"<option value='$i'>$i</option>";
    }
echo "</select>";

echo "<select name=mes>";
    for($i=1;$i<=12;$i++) {
	echo ($i==$mm)?"<option value='$i' selected>$i</option>":"<option value='$i'>$i</option>";
    }
echo "</select>";

echo "<select name=ano>";
    for($i=2006;$i<=2010;$i++) {
	echo ($i==$yy)?"<option value='$i' selected>$i</option>":"<option value='$i'>$i</option>";
    }
echo "</select>";

echo "</td>";
 $verage = pg_query("select *from agendamento where med_codigo='$med_codigo' and usu_codigo='$usu_codigo' and age_item='$age_item' and esp_codigo='$esp_codigo' and agt_codigo='$agt_codigo' and age_data = current_date");
 $v = "select *from agendamento where med_codigo='$med_codigo' and usu_codigo='$usu_codigo' and age_item='$age_item' and esp_codigo='$esp_codigo' and agt_codigo='$agt_codigo' and age_data = current_date";

echo "</form></tr>";
   echo "</table><br><br>";
if($acao=="add") {
   $sel = pg_query ("select *from agendamento where age_tipo ='EX' and esp_codigo = '$procedimento' and age_data = current_date");
   $grade_exame = pg_fetch_array(pg_query("select *from grade_exame where proc_codigo = '$procedimento'"));

if(pg_num_rows($sel)>=$grade_exame[gex_qtde]) {
   echo "<center><font color=red>Você Não pode mais Agendar nesta Especialidade</font><br><a href=javascript:history.back(1)>voltar</a><center>";
//reglog($id_login,"Tentando Gravar em Agenda Cheia de Exames Dta: $age_data Paciente: $age_paciente Proc. $procedimento");
exit;
} else {
//reglog($id_login,"Marcado Exame Dta: $age_data Paciente: $age_paciente Proc. $procedimento");
   $age_data = $ano."-".$mes."-".$dia;
   $sql = pg_query("insert into agendamento (age_data,usu_codigo,age_tipo,age_paciente,esp_codigo,med_codigo,uni_codigo) values 
	   ('$age_data','$usu_codigo','EX','$age_paciente','$procedimento','1745','257')");
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
             setTimeout(\"location='$PHP_SELF?id_login=$id_login&proc_codigo&id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&agt_codigo=$agt_codigo&age_tipo=$age_tipo&age_data=$age_data&age_vaga=$age_vaga&usu_codigo=$usu_codigo&age_paciente=$age_paciente&age_hr=$age_hr&acao=age'\", 0);
           </SCRIPT>";

 }
}
//if($acao=="age") {

  echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td width=600 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>It.Age.</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Semana</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=10>&nbsp;</td>
	</tr>";
while($rv=pg_fetch_array($sql_qtdmed)) {
 $proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = '$rv[esp_codigo]'"));
 $dta = explode("-",$rv[age_data]);
 $data = $dta[2]."/".$dta[1]."/".$dta[0]; 
 echo "<tr bgcolor=ffffff>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>$proc[proc_nome]</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>$data</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>".ChmodBtn($id_login,'apagar','agendamento_exame.php?acao=delagendamento&esp_codigo='.$rv[esp_codigo])."</td>
	</tr>";

 }
 echo "</table>";
//}

