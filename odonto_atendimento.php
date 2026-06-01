<?php
/** Atendimento odontologico **/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();

// teste se há algum atendimento para este agendamento e data !
$age_data2 = db_get("SELECT age_data FROM agendamento WHERE age_codigo = $age_codigo");
$sql = "SELECT MAX(od_codigo) FROM odonto WHERE age_codigo = $age_codigo AND od_data = '$age_data2'";
$age_teste = db_get($sql);

if( ! $age_teste )
{
	db_query('Begin');

	$pk = db_get("SELECT NEXTVAL('odonto_od_codigo_seq');");
	
	$sql2 = "INSERT INTO odonto (od_codigo, od_data, age_codigo) VALUES ($pk, NOW(), $age_codigo)";
	
	db_query($sql2);

	//db_query('Rollback');
	db_query('Commit');
}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em ODONTO");
//------------------------------------------------------------------>
if( empty($url) || $url=="odonto_recepcionado.php" ) 
{ 
	$url="odonto_recepcionado.php"; 
	$size_W="100%";
	$size_H="500";
}    
else if($url=="odonto_fazer_atendimento.php") 
{ 
	$size_W="100%";
	$size_H="500";
}    
else if($url=="odonto_dados_paciente.php") 
{
	$size_W="100%";
	$size_H="500";
}
  
  echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>";

  echo "<br><table width=48% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td width=101><a href='?url=odonto_recepcionado.php&id_login=$id_login&age_codigo=$age_codigo'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/agenda_dia_off.jpg width=101 border=0></a></td>
	  <td width=120><a href='?url=odonto_dados_paciente.php&age_codigo=$age_codigo&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/dados_paciente_off.jpg width=120 border=0></a></td>
	  <td><a href='?url=odonto_fazer_atendimento.php&age_codigo=$age_codigo&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/atendimento_off.jpg width=89 border=0></a></td>
	 </tr>
	</table>
	<table height=$size_H width=$size_W cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-left:1px solid;border-right:1px solid; border-color:A4A4A4'>
	 <tr bgcolor=E6E6E6>
	  <td valign=top><iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=$size_W height=$size_H>\n</iframe></td>
	 </tr>
	</table>";

if($url=="odonto_dados_paciente.php") 
{
	$size_W="100%";
	$size_H="500";
	if( empty($url_hist) ) { $url_hist="odonto_historico_atendimento.php"; }
	echo "<td>";
	echo "<br><table width=48% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/historico_atendimento.jpg border=0></td>
	 </tr>
	</table>
	<table height=$size_H width=$size_W cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-left:1px solid;border-right:1px solid; border-color:A4A4A4'>
	 <tr bgcolor=E6E6E6>
	  <td valign=top><iframe name=frameprincipal src='$url_hist?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=$size_W height=$size_H>\n</iframe></td>
	 </tr>
	</table>";
  	echo "</td>";
 }

echo "</td>
     </tr>
  </table>
</body>
</html>
  ";


?>
