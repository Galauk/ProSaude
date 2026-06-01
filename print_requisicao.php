<script language=javascript>
	function imprimir() {
		window.print();
	}
</script>

<body onload='imprimir()'>
<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  echo "<body bgcolor=FFFFFF>  <link href='estilo.css' rel='stylesheet' type='text/css'>";
	$imageLogo = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_papeis.jpg>";
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em REQUISICAO DE EXAMES");
//------------------------------------------------------------------>

//
//-> Botoes

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $med_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select *from medico where med_codigo='$med_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$uni_codigo'"));
 $usuario = pg_fetch_array(pg_query("select *from usuario where usu_codigo='$usu_codigo'"));
 $atend = pg_fetch_array(pg_query("select ate_diagnostico,cd10_codigo,to_char(ate_datafinal,'DD/MM/YYYY') as ate_datafinal,ate_horafinal from atendimento where ate_codigo='$ate_codigo'"));
 $ArMes = array("","Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
 $mes = date('m');
 $mes_desc = $ArMes[$mes];

echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>    
 <td width=65>$imageLogo</td>
	     <td valign=top>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
           <tr>
	        <td><font size=4 face=arial>$medInfo[med_nome]</font></td>
	       </tr>
           <tr>
	        <td><font size=1 face=arial>$medInfo[med_endereco]</font></td>
	       </tr>
           <tr>
	        <td>CRM:<font size=2 face=arial>$medInfo[med_crm]</font></td>
	       </tr>
	</table>
 </td>
 <td>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td width=74%>&nbsp;</td>
	 <td><iframe name=codigo src='codigo.php?id_login=$id_login&age_codigo=$receita' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=150 height=80></iframe>
	</tr>
	</table>
  </td>
 </tr>
</table>";
echo "<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
            <tr>
             <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
            </tr>
       </table>
           <table height=454 width=100%% cellspcing=0 cellpadding=0 border=0 align=center>
            <tr>
             <td valign=top>";
echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td align=center><font size=4>Requisição de EXAMES</font></td>
         </tr>
        </table><br>";

     echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	 	<tr>
	    	<td><font size=2><b>Nome:</b> $usuario[usu_nome]</font></td>
	   	</tr>
	   	<tr>
	    	<td><font size=2><b>Dados Clínicos:</b> $atend[ate_diagnostico]</font></td>
	   	</tr>
	   	<tr>
	   		<td align=center><font size=2><b>Exames Requisitados:</b> </font></td>
	   	</tr>
	  </table>";
 	  $select ="select *from requisicao_exames where ate_codigo = '$ate_codigo'";
	  $sql_requisicao = pg_query($select);
  echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
  
         ";
$c1 = "#A6A6A6";
$c2 = "";		

while($rv=pg_fetch_array($sql_requisicao)) {
if ($controle == 0) {
	$cor = $c1;
	$controle++;
} else {
	$cor = $c2;
	$controle = 0;
}
 $proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = '$rv[proc_codigo]'"));
 echo "<tr bgcolor='$cor'>
 		<td>
 		<font size=2>$proc[proc_nome]</font>
 	  </td>
 	 </tr>";

 }

 echo "
        </table>";
  echo "<br><br><br><br><br><br>
  		<br><br><br><br><br><br>
    	<br><br><br><br><br><br>
  		<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr bgcolor='$cor'>
           <td align=right> ".date('d')."&nbsp; $mes_desc de ".date('Y')."</td>
          </tr>
         </table><br><br>";
 echo "</td>
        </tr>
       </table>
        <table width=80% cellspacing=0 cellpadding=0 border=0 align=center>
        <tr>
         <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_pddapeis.jpg width=500 height=3></td>
        </tr>
           <tr>
            <td align=center><b>$uniInfo[uni_desc]</b>&nbsp;&nbsp;$uniInfo[uni_localizacao]</td>
           </tr>
          </table>";
 $qq = pg_query("update requisicao_exames set req_finalizada='S' where ate_codigo='$ate_codigo'");

?>
