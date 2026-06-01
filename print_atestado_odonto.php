<?php

//var_dump($_GET);
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

echo "<body bgcolor=FFFFFF topmargin=0 leftmargin=0 rightmargin=0>
      <link href='estilo.css' rel='stylesheet' type='text/css'>";

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $med_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select *from medico where med_codigo='$med_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$uni_codigo'"));
 $usuario = pg_fetch_array(pg_query("select *from usuario where usu_codigo='$usu_codigo'"));	
 //$atend = pg_fetch_array(pg_query("select cd10_codigo,to_char(ate_datafinal,'DD/MM/YYYY') as ate_datafinal,ate_horafinal from atendimento where ate_codigo='$ate_codigo'"));
$atend = db_getRow("select to_char(od_datafinal,'DD/MM/YYYY') as ate_datafinal,to_char(od_hora, 'HH24:MI') as ate_horafinal from odonto where age_codigo='$age_codigo' ORDER BY od_codigo DESC");

 $ArMes = array("","Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
 $mes = date('m');
 $mes_desc = $ArMes[ intval($mes) ];

if(($choice_atestado=="" and $acao=="")) {
   echo "<form method=post action=$PHP_SELF>
	  <input type=hidden name=acao value=add>
	  <input type=hidden name=id_login value=$id_login>
	  <input type=hidden name=ate_codigo value=$ate_codigo>
	  <input type=hidden name=age_codigo value=$age_codigo>

	 <table width=100% cellspacing=0 cellpadding=5 border=0>
	  <tr bgcolor=e1e1e1>
	   <td align=right><b><font size=5>Atestado Odontol&oacute;gico</font></b></td>
	  </tr>
	 </table><br><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
	   <td>O(a) Sr.(a): $usuario[usu_nome]</td>
	  </tr>
	 </table><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
	   <td>Esteve em consulta no dia $atend[ate_datafinal], às $atend[ate_horafinal]</td>
	  </tr>
	 </table><br><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
	  <tr>
	   <td width=25><input type=checkbox name=consulta value='S'></td>
	   <td>Consulta Odontol&oacute;gica</td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=filho value='S'></td>
	   <td>Acompanhando seu filho menor:&nbsp;<input type=text name=acomp_filho class=box size=38></td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=rentornotrab value='S'></td>
	   <td>Devendo retornar ao trabalho: <input type=text name=retornar_trab class=box size=40></td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=repousohs value='S'></td>
	   <td>Devendo permanecer em repouso: <input type=text name=hs_ini class=box size=10>&nbsp;hs. a partir das <input type=text name=hs_final class=box size=10>&nbsp;hs.</td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=repousohj value='S'></td>
	   <td>Devendo permanecer em repouso hoje.</td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=repousodia value='S'></td>
	   <td>Devendo permanecer em repouso&nbsp;<input type=text name=repdia class=box size=10>&nbsp;dias, a partir desta data.</td>
	  </tr>
	  <tr>
	   <td width=25><input type=checkbox name=tipoobs value='S'></td>
	   <td><input type=text name=tipoobs_char class=box size=69></td>
	  </tr>
	 </table><br><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
	   <td>Observação:</td>
	  </tr>
	  <tr>
	   <td><textarea name=obs class=box cols=73 rows=5></textarea></td>
	  </tr>
	 </table><br><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
	   <td align=right>Guarapuava, ".date('d')."&nbsp; de $mes_desc de ".date('Y')."</td>
	  </tr>
	 </table><br><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
	   <td align=center><input type=submit value='Gravar Atestado' class=box></td>
	  </tr>
	 </table><br><br></form>";
}

if($acao=="add")
{
	$stmt = "insert into atestado 
	       (age_codigo,consulta_medica,acompanhando_filho,retorno_trabalho,repouso_hs,repouso_hoje,repouso_dia,tipo_obs,acompanhando,retornoaotrabalho,
	        repousohs_ini,repousohs_final,repousodias,tipoobs,obs,dt_atestado)
       values ('$age_codigo','$consulta','$filho','$rentornotrab','$repousohs','$repousohj','$repousodia',
	       '$tipoobs','$acomp_filho','$retornar_trab','$hs_ini','$hs_final','$repdia','$tipoobs_char',
	       '$obs',NOW())";
	
	$sql=db_query($stmt);
	
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
		setTimeout(\"location='$PHP_SELF?id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&acao=print'\", 000);
	</SCRIPT>";
}



if($acao=="print") {
	
 echo "<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td width=65><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_papeis.jpg></td>
	 <td valign=top>
	  <table width=100% cellspacing=0 cellpadding=0 border=0>	
           <tr>
	    <td><font size=4 face=arial>$medInfo[med_nome]</font></td>
	   </tr>
           <tr>
	    <td><font size=2 face=arial>$medInfo[med_endereco]</font></td>
	   </tr>
           <tr>
	    <td>CRM:<font size=2 face=arial>$medInfo[med_crm]</font></td>
	   </tr>
	  </table>
         </td>
	</tr>
	</table>
	<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	</tr>
       </table>
	<table height=454 width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td background=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fundo_papeis.jpg valign=top>";
	include $_SESSION[root].$_SESSION[modulo]."atestado_odonto.php";
 echo "</td>
	</tr>
       </table>";
/*
	<table width=80% cellspacing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	</tr>
           <tr>
	    <td align=center><b>$uniInfo[uni_desc]</b>&nbsp;&nbsp;$uniInfo[uni_localizacao]</td>
	   </tr>
	  </table>";
*/
}
?>