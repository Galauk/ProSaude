<html xmlns="http://www.w3.org/1999/xhtml">
 <head>

 <?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

 	$unidades = array('P' => 'PAM',
				  'O' => 'ÓBITO',
				  'E' => 'ESCOLA DA GESTANTE',
				  'C' => 'CENTRO INFANTIL',
  				  'N' => 'NATTA',
				  'EM' => 'EM TRÂNSITO'				 
				  );
 ?>
 <script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}

 function msg(id_login,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data) {
     parent.location.href="atendimento_medico2.php?id_login="+id_login+"&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&url=dados_paciente.php";
 }
</script>
 <meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
 <link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
 </head>
 <body bgcolor="#E8F4F">
 
 <?
$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo $common->menuTab(array('Agenda do dia','Dados do paciente','Atendimento','Hist.Atendimento'));
  

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	cabecario();
//------------------------------------------------------------------>
  echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>";
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>
$size_W="100%";
$size_H="500";

echo $common->bodyTab('1');	
	$url = "recepcionado_medico.php";	
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();

echo $common->bodyTab('2');
	
    $url = "dados_paciente.php";
    include "$url?id_login=$id_login&age_codigo=$age_codigo";
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();
		
echo $common->bodyTab('3');
		$url = "fazer_atendimento.php";
		echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();

echo $common->bodyTab('4');		
	$url = "historico_atendimento.php";
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();

  echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>";
 if($url=="dados_paciente.php") {
     $size_W="100%";
     $size_H="500";
if(empty($url_hist)) { $url_hist="historico_atendimento.php"; }
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
      </table>";

?>
 </body>
</html>