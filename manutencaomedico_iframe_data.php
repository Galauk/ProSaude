<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$Data = explode("/",$id_dia);
$sql = "select gra_codigo, gra_data, gra_tipo, gra_status, gra_qtde,
               gra_hora_ini, age_item, age_tipo, gra_obs, to_char(gra_bloqueado,'DD/MM/YYYY') as gra_bloqueado
        from grade_medico
        where med_codigo = '$med_codigo'
        and   uni_codigo = '$uni_codigo'
        and   esp_codigo = '$esp_codigo'
        and   gra_data = '$Data[2]-$Data[1]-$Data[0]' order by gra_hora_ini";

$query = pg_query($sql);
//vSQL($sql,"1");

 echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td width=50 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Hora ini.</font></td>
	 <td width=30 align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Qtd</font></td>
	 <td width=130 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Item de Agendamento</font></td>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tipo</font></td>
	 <td width=30 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Observação</font></td>
	 <td width=90 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Bloqueado até</font></td>
	 <td width=30 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Qtd</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	</tr>";

while($row=pg_fetch_array($query)) {
  if($row[age_item]=="CB") { 
     $tp_01="selected"; $tp_02=""; 
  } else { 
     $tp_01=""; $tp_02="selected"; 
  }

  if($row[age_tipo]=="PC") {
     $pc="selected";
  }
  if($row[age_tipo]=="GE") {
     $ge="selected";
  }
  if($row[age_tipo]=="RT") {
     $rt="selected";
  }
  if($row[age_tipo]=="AL") {
     $al="selected";
  }
  if($row[age_tipo]=="CA") {
     $ca="selected";
  }

?><script>
function grahora(hr) {
     document.form.gra_hora_ini.value = hr;
}
function gralimpa() {
     document.form.gra_hora_ini.value = '';
}
</script>
<body OnLoad="grahora('<?=$row[gra_hora_ini]?>')">
<?
if(pg_num_rows($query)<="20") {
//   $linkline = "<a href='$PHP_SELF?id_login=$id_login&gra_codigo=$row[gra_codigo]&acao=delline&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg border=0></a>";
   $linkline2 = "<a href='$PHP_SELF?id_login=$id_login&gra_codigo=$row[gra_codigo]&acao=delline&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg border=0></a>";
}
if(pg_num_rows($query)>="1") {
reglog($id_login,"Mostrando Datas Disponiveis");
   $linkline = "<a href='$PHP_SELF?id_login=$id_login&acao=newline&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/nova_linha.jpg border=0></a>";
}

$rowVerif = pg_fetch_array(pg_query("select  b.gra_data, b.gra_hora_ini, coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini order by gra_data limit 1),0) -
        coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as qtdegeral
from view_qtde_grade as b
where b.med_codigo = '$med_codigo'
and b.uni_codigo = '$uni_codigo'
and b.gra_data = '$id_dia'
and b.gra_hora_ini = '$row[gra_hora_ini]'
order by b.gra_data , b.gra_hora_ini"));

 echo "<tr bgcolor=FFFFFF><form name=form method=post action='$PHP_SELF'>
	<input type=hidden name=acao value=gravar>
	<input type=hidden name=gra_codigo value=$row[gra_codigo]>
	<input type=hidden name=uni_codigo value=$uni_codigo>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=esp_codigo value=$esp_codigo>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=id_dia value=$id_dia>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=text name=gra_hora_ini class=boxn size=5 OnFocus=\"gralimpa();\" value='$row[gra_hora_ini]'></td>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=text name=gra_qtde class=boxn size=3 value='$row[gra_qtde]')\"></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
	 <select name=age_item class=box>
	  <option value='CB' $tp_01>CLINICA BÁSICA</option>
	  <option value='ES' $tp_02>ESPECIALIDADE</option>
	 </select>
	 </td>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
	 <select name=age_tipo class=box>
	  <option value='PC' $pc>PC</option>
	  <option value='GE' $ge>GE</option>
	  <option value='RT' $rt>RT</option>
	  <option value='AL' $al>AL</option>
	  <option value='CA' $ca>CA</option>
	 </select>
	 </td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=text class=boxt name='gra_obs' size=30 value='$row[gra_obs]'></td>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=text class=box name='gra_bloqueado' size=12 value='$row[gra_bloqueado]'></td>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$rowVerif[qtdegeral]</td>
	 <td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><a href='#' OnClick='window.open(\"newdate.php?id_login=$id_login&id_dia=$id_dia&med_codigo=$med_codigo&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&grahora=$row[gra_hora_ini]&age_item=$row[age_item]&age_tipo=$row[age_tipo]&gra_obs=$row[gra_obs]\",null,\"height=80,width=200,status=yes,toolbar=no,menubar=no,location=no,scrollbars=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar.jpg border=0></a> $linkline2 $linkline &nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gravar.jpg border=0></td>
	</tr></form>";
}
 echo "</table>";

 if($acao=="gravar") {
reglog($id_login,"Gravando Grade Medico Med.Cod.: $med_codigo - Cod.Grade: $gra_codigo - Qtde: $gra_qtde");
    if($gra_bloqueado == "" ) { $gra_bloqueado = "20010-12-12"; } else { $gra_bloqueado = $gra_bloqueado; }
    $sql = "update grade_medico set gra_tipo='$gra_tipo',gra_qtde='$gra_qtde',gra_hora_ini='$gra_hora_ini',age_item='$age_item',age_tipo='$age_tipo',gra_obs='$gra_obs',gra_bloqueado='$gra_bloqueado',usr_codigo_alt='$id_login' where gra_codigo='$gra_codigo'";
 $query = pg_query($sql);

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?acao=&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'\", 0);
              </SCRIPT>";
}

 if($acao=="newline") {
reglog($id_login,"Adicionando Novo Horario para o Medico Cod: $med_codigo");
 $data = "$Data[2]-$Data[1]-$Data[0]";
 $sql = "insert into grade_medico (gra_data,med_codigo,uni_codigo,esp_codigo,gra_tipo,gra_hora_ini,age_item,age_tipo,usr_codigo_cad) values ('$data','$med_codigo','$uni_codigo','$esp_codigo','PC','14:00','CB','$age_tipo','$id_login')";

 $query = pg_query($sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?acao=&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'\", 0);
              </SCRIPT>";

//vSQL($sql,"1");
}

 if($acao=="delline") {
reglog($id_login,"Apagando Horario do Medico Cod: $med_codigo");
 $data = "$Data[2]-$Data[1]-$Data[0]";
 $sql = "delete from grade_medico where gra_codigo='$gra_codigo'";

 $query = pg_query($sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?acao=&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login'\", 0);
              </SCRIPT>";

//vSQL($sql,"1");
}
?>

