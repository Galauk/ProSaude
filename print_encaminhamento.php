<script language=javascript>
	function imprimir() {
		window.print();
	}
</script>

<body onload='imprimir()'>
<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
echo "<body bgcolor=FFFFFF>
      <link href='estilo.css' rel='stylesheet' type='text/css'>";

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $Ate = pg_fetch_array(pg_query("select *from atendimento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $med_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select *from medico where med_codigo='$med_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$uni_codigo'"));
 $usu=pg_fetch_array(pg_query("select usu_nome,to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc from usuario where usu_codigo='$usu_codigo'"));

 $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo=$esp_codigo"));



 echo "$Ate[esp_codigo]<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
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
	 <td valign=top align=center background=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fundo_papeis.jpg>
	  <font face=times size=5><u>Encaminhamento</u></font><br><br><br><br>
	  Encaminhamento do Paciente: <b>$usu[usu_nome]</b>, nascido em: <b>$usu[usu_datanasc]</b>
	  para a especialidade De: <b>$esp[esp_nome]</b>
	
 	<br><br><br><br><br>
 	<br><br><br><br><br>
	__________________________________________<br>
		$medInfo[med_nome]<br>CRM: $medInfo[med_crm]
	</td>
	</tr>
       </table>
	<table width=80% cellspacing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	</tr>
           <tr>
	    <td align=center><b>$uniInfo[uni_desc]</b>&nbsp;&nbsp;$uniInfo[uni_localizacao]</td>
	   </tr>
	  </table>";

?>
