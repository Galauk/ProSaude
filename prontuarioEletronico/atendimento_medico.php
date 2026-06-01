 <link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
<?
	session_start();
 	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
 	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
//------------------------------------------------------------------>

$form = new classForm();
$common = new commonClass();
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>
  if(empty($url) OR $url=="recepcionado_medico.php") { 
     $url="recepcionado_medico.php"; 
     $size_W="100%";
     $size_H="500";
  }    
  if($url=="fazer_atendimento.php") { 
     $size_W="100%";
     $size_H="500";
  }    
  if($url=="dados_paciente.php") {
     $size_W="100%";
     $size_H="500";
  }
 echo $common->incJquery();
//echo $common->menuTab(array('Agenda do dia','Dados do paciente'));
  echo "<table width=100% cellspacing=0 cellpadding=0 border=4>
	 <tr>
	  <td>
	";
  
/*echo $common->bodyTab('1');	
	$url = "recepcionado_medico.php";	
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();

echo $common->bodyTab('2');	
    $url = "dados_paciente.php";   
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();
		
echo $common->bodyTab('3');
		$url = "fazer_atendimento.php";
		echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();

echo $common->bodyTab('4');		
	$url = "historico_atendimento.php";
	echo"<iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=$size_W height=$size_H>\n</iframe>";
echo $common->closeTab();*/

  echo "<br><table cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td width=101> <a href=atendimento_medico.php?url=recepcionado_medico.php&id_login=$id_login&age_codigo=$age_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendamentoNovo.jpg width=91 border=0></a></td>
	  <td width=120><a href=atendimento_medico.php?url=dados_paciente.php&age_codigo=$age_codigo&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/dados_pacienteNovof.jpg width=114 border=0></a></td>
	  
	 </tr>
	</table>
	<table height=$size_H width=$size_W cellspacing=0 cellpadding=0 border=0 >
	 <tr bgcolor=#FFFFFF>
	  <td valign=top><iframe name=frameprincipal src='$url?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=$size_W height=$size_H>\n</iframe></td>
	 </tr>
	
	 </table>";

 if($url=="dados_paciente.php") {
     $size_W="100%";
     $size_H="500";
if(empty($url_hist)) { $url_hist="fazer_atendimento.php"; }
  echo "<td>";
  echo "<br><table width=30% cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>&nbsp</td>
	</table>
	<table height=$size_H width=$size_W cellspacing=0 cellpadding=0 border=0>
	 <tr bgcolor=E6E6E6>
	  <td valign=top>";
	  /*$form = new classForm();
	  $common = new commonClass();
      echo $common->incJquery();
	  echo $common->menuTab(array('Pré consulta','Anamnese','Receita','Atestado','Requizitar exames'));

	  echo $common->bodyTab('1');
	  	include "pre_consulta.php";	
	  echo $common->closeTab();
	  echo $common->bodyTab('2');
	  	include "anamnese_medico.php";	
	  echo $common->closeTab();
	  echo $common->bodyTab('3');
	  	include "itens_receita.php";	
	  echo $common->closeTab();
	  echo $common->bodyTab('4');
	  	include "print_atestado.php";	
	  echo $common->closeTab();
	  	  echo $common->bodyTab('4');
	  	include "print_atestado.php";	
	  echo $common->closeTab();
	  	  echo $common->bodyTab('5');
	  	include "requisicao_exames.php";	
	  echo $common->closeTab();*/
	  
	  echo" 
  		<table width=100%>
           <tr>
           	 <td width=72>
            	<a href='#' OnClick='window.open(\"pre_consulta.php?id_login=$id_login&age_codigo=$age_codigo\",null,\"height=450,width=510,status=yes,toolbar=no,menubar=no,location=no\");'>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/pre_consultaNovo.jpg' alt='Pre Consulta' border='0' />
				</a>
			 </td>
			 <td width=72>
          		 <a href='#' OnClick='window.open(\"anamnese_medico.php?id_login=$id_login&age_codigo=$age_codigo\",null,\"height=450,width=510,status=yes,toolbar=no,menubar=no,location=no\");'>
				 <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/anamneseNovojpg.jpg' alt='Anamnese Medico' border='0' />
				 </a>
			 </td>
			<td width=72>
				<a href=itens_receita.php?id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate[ate_codigo]>
				<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/receitaNovo.jpg border=0></a></td>
            <td width=72><a href='#' OnClick='window.open(\"print_atestado.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/atestadoNovo.jpg border=0></a></td>
            <td><a href='#' OnClick='window.open(\"requisicao_exames.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=400,width=780,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/requisitar_examesNovo.jpg border=0></a></td>";

echo "     </tr>
          </table>";
	  	echo"<iframe name=frameprincipal src='$url_hist?id_login=$id_login&age_codigo=$age_codigo' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=$size_W height=$size_H>\n</iframe>
	  	
	  </td>
	 </tr>
	</table>";



  echo "</td>";
 }
  echo "</td>
       </tr>
      </table>";


?>
