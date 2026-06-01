<!-- ---------------------------------------------------------------
       Funções javascript
--------------------------------------------------------------- --->

<script language=javascript>

function imprimir() {
       window.print();
}

</script>

<body onload='imprimir()'>

<?php

function inv_data($dat) {
   $d=explode("-",$dat);
   $dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
   return "$dat";
 }
//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>

//	session_start();
//	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<body>  <link href='estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

//$Ate=pg_fetch_array(pg_query("SELECT cd10_codigo,to_char(ate_datafinal,'DD/MM/YYYY') as ate_datafinal,ate_horafinal,usu_codigo, uni_codigo, to_char(ate_data,'dd/mm/yyyy') as ate_data, med_codigo FROM atendimento WHERE atendimento.ate_codigo = $ate_codigo"));
$Ate = db_getRow("select usu_codigo, to_char(od_datafinal,'DD/MM/YYYY') as ate_datafinal,to_char(od_hora, 'HH24:MI') as ate_horafinal 
	from odonto AS o 
	left join agendamento as a on o.age_codigo = a.age_codigo
	where a.age_codigo='$age_codigo' ORDER BY od_codigo DESC");
$Usu=pg_fetch_array(pg_query("SELECT usu_nome FROM usuario WHERE usuario.usu_codigo = $Ate[usu_codigo]"));
$Uni=pg_fetch_array(pg_query("SELECT uni_desc FROM unidade WHERE unidade.uni_codigo = $Ate[uni_codigo]"));
$Med=pg_fetch_array(pg_query("SELECT med_nome FROM medico  WHERE medico.med_codigo  = $Ate[med_codigo]"));
$Hoje = date("n");

switch ($Hoje) 
     {
       case  1:   $mes = "janeiro";    break;
       case  2:   $mes = "fevereiro";  break;
       case  3:   $mes = "março";      break;
       case  4:   $mes = "abril";      break;
       case  5:   $mes = "maio";       break;
       case  6:   $mes = "junho";      break;
       case  7:   $mes = "julho";      break;
       case  8:   $mes = "agosto";     break;
       case  9:   $mes = "setembro";   break;
       case 10:   $mes = "outubro";    break;
       case 11:   $mes = "novembro";   break;
       case 12:   $mes = "dezembro";   break;
     }
$Hoje = date("d")." de ".$mes. " de ".date("Y");
$DiaAtesta=$Ate[ate_data];

echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
	  <td align=center><font face=times size=4><u>ATESTADO</u></font></td>
	 </tr>
        </table><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td>O(a) Sr.(a): $Usu[usu_nome]</td>
          </tr>
         </table><br>";
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td>Esteve em consulta no dia $Ate[ate_datafinal], às $Ate[ate_horafinal]</td>
          </tr>
         </table><br><br>";


   //$rr = pg_fetch_array(pg_query("select *from atestado where ate_codigo='$ate_codigo' order by atest_codigo limit 1"));
	$stmt = "select *from atestado where age_codigo='$age_codigo' order by atest_codigo desc limit 1";
	$rr = db_getRow($stmt);

if(trim($rr[consulta_medica])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Consulta Médica</td>
          </tr>
	 </table>";
}

if(trim($rr[acompanhando_filho])==S) {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Acompanhando seu filho menor:&nbsp;$rr[acompanhando]</td>
          </tr>
	 </table>";
}

if(trim($rr[retorno_trabalho])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Devendo retornar ao trabalho: $rr[retornoaotrabalho]</td>
          </tr>
	 </table>";
}

if(trim($rr[repouso_hs])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Devendo permanecer em repouso: $rr[repousohs_ini]&nbsp;hs. a partir das $rr[repousohs_final]&nbsp;hs.</td>
          </tr>
	 </table>";
}

if(trim($rr[repouso_hoje])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Devendo permanecer em repouso hoje.</td>
          </tr>
	 </table>";
}

if(trim($rr[repouso_dia])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>Devendo permanecer em repouso&nbsp;$rr[repousodias]&nbsp;dias, a partir desta data.</td>
          </tr>
	 </table>";
}

if(trim($rr[tipo_obs])=="S") {
   echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
          <tr>
           <td>$rr[tipoobs]</td>
          </tr>
         </table>";
}
echo "<br><br>";

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td>Observação:</td>
          </tr>
          <tr>
           <td>".nl2br($rr[obs])."</td>
          </tr>
         </table><br><br>";
/*
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td width=30><b>CID:</b></td>
           <td><font size=2><b>$cid[cd10_codigo_cid]</b></font>&nbsp;-&nbsp;$cid[cd10_descricao]</td>
          </tr>
         </table><br><br>";
*/
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td align=right>Guarapuava, ".date('d')."&nbsp; $mes_desc de ".date('Y')."</td>
          </tr>
         </table>";

   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <tr>
           <td width=30%>
	    <fieldset>
	     <table width=30% cellspacing=3 cellpadding=0 border=0>
	     <!--<tr>
	      <td>CID: <font size=2><b>$cid[cd10_codigo_cid]</b></font></td>
	     </tr>-->
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td align=center>___________________________</td>
	     </tr>
	     <tr>
	      <td align=center>Ass. paciente ou reponsável</td>
	     </tr>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td><font style='font-size: 5pt;font-family: verdana,sans-serif;font-weight:900'>O código de ética médica Resolução CRM n: 1246/88, art. 117 veda a informação do diagnóstico nos atestados (C.I.D.).
		  O mesmo só poderá ser aposto com consentimento assinado pelo paciente.</td>
	     <tr>
	     </table></fieldset>
	   </td>
           <td>
	     <table width=100% cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td align=center>______________________________________________</td>
	     </tr>
	     <tr>
	      <td align=center>Carimbo e Assinatura do Dentista</td>
	     </tr>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td>&nbsp;</td>
	     </tr>
	     <tr>
	      <td align=center><font size=3><b>ATEN&Ccedil;&Acirc;O</b></font></td>
	     </tr>
	     <tr>
	      <td><font style='font-size: 5pt;font-family: verdana,sans-serif;font-weight:900'>Este documento não poderá ser rasurado. Deve ser entregue na sua empresa dentro de 24 horas.<br>
		  Não podendo ser concedido neste atestado afastamento superior a 15(quinze) dias, nem retroativo.</font>
	      </td>
	     </tr>
		
	     </table>
	   </td>
          </tr>
         </table><br><br>";



?>
