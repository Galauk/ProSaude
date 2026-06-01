<?php

function inv_data($dat) {
   $d=explode("-",$dat);
   $dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
   return "$dat";
 }

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//echo "<body>
//     <link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

$titulo="Atendimentos do PAM - Por Cidade ";    //       NOME DO RELATÓRIO

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "Hora INICIAL->".$hr_inicial."<br>";
//echo "Hora FINAL  ->".$hr_final."<br>";

$hr_inicial = '00:00';
$hr_final = '23:59';

$dias = array("31", "28", "31", "30", "31", "30","31", "31", "30", "31", "30", "31");

$mes = pg_fetch_array(pg_query("select extract (month from date(now()))"));
$ano = pg_fetch_array(pg_query("select extract (year from date(now()))"));

if ($mes[0] < 10) {
   $mes1 = '0'.$mes[0];
}
else {
   $mes1 = $mes[0];
}

//echo $dt_inicial;

if (!$dt_inicial) {
    $dt_inicial = '01/'.$mes1.'/'.$ano[0];
    $diafinal = $dias[$mes[0]-1];
    $dt_final = $diafinal.'/'.$mes1.'/'.$ano[0];
}


echo "
  <form method=post action=$PHP_SELF>
	<input type=hidden name=user value=$user>

	<fieldset>
	    <legend>Dados do Relatorio</legend>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
		    <td width=110>Data Inicial:</td>
		    <td>
			<input type=text name=dt_inicial class=box size=13 value=$dt_inicial>
			</td>
	     </tr>
	     <tr>
		    <td width=110>Data Final:</td>
		    <td>
			<input type=text name=dt_final class=box size=13 value=$dt_final>
			</td>
	     </tr>";
echo "      <tr> <td> &nbsp;&nbsp;</td>
              <td > <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg> </td>\n";
echo "      <td > <a href=index.php?user=$user><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";


echo"
        </table>
        </fieldset>
        </form>";

    echo " <br> <br> <br> ";
//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Uni, $Cab) {

//--->        Cabeçalho do Sistema

       if ($Cab == 0) {

          echo "<table style='font-size:14px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=3 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=3 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=3 face=courier>".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=3 face=courier>PERIODO: $dtIni $hrIni A $dtFin $hrFin</font></td>
 	    	       </tr>
 	    	       <tr>
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	       </tr>
 	              </table>";
 	    echo "<table style=\"font-size:14px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	    }

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold' align=center>\n";
           echo "  <td width=50%>Cidade </td>\n";
           echo "  <td width=25%>Quantidade de Atend. </td>\n";
           echo "  <td width=25%>Percentual </td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->
      $sqltotal = "SELECT
               COUNT(*) AS QUANTATEND
        FROM   V_ATEND_PAM
        where  ate_data >= '$dt_inicial' AND ate_data <= '$dt_final'";
      $total=pg_fetch_array(pg_query($sqltotal));

$sql = "SELECT NOMECIDADE,  
               COUNT(*) AS QUANTATEND
        FROM   V_ATEND_PAM
        WHERE  ATE_DATA >= '$dt_inicial' AND ATE_DATA <= '$dt_final'
        GROUP BY NOMECIDADE 
        ORDER BY NOMECIDADE"; 
        
$query=pg_query($sql) or die (pg_last_error());
$lin=999;


if (pg_num_rows($query) == 0) {
    echo "<table style='font-size:14px;font-family:Tahoma,Arial;' width=70% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
    echo "  <tr><td align=center colspan=5>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>\n";
    echo "  <tr><td align=right  colspan=5>&nbsp;</td></tr>\n";
    echo "  <tr><td align=right  width=25%></td>\n";
    echo "      <td align=right  width=20%>Data INICIAL</td>\n";
    echo "      <td align=center width= 5%>.....</td>\n";
    echo "      <td align=left   width=30%>$dt_inicial</td><td>&nbsp;</td></tr\n";
    echo "  <tr><td align=right></td>\n";
    echo "      <td align=right>Data FINAL</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$dt_final</td><td>&nbsp;</td></tr\n";
    echo "  <tr><td align=right></td>\n";
    echo "</table>\n";
}
else {

      while($row=pg_fetch_row($query)) {

          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0');
              $lin=9;
          }
          $percentual = $row[1] / $total[0] * 100;
          $perc = formata_valor($percentual);
          echo " <tr align=center>\n";
          echo "  <td>$row[0]</td>\n";
          echo "  <td>$row[1]</td>\n";
          echo "  <td>$perc</td>\n";
          echo " </tr>\n";

      }
      echo "</table>\n";

 	  echo "<table style=\"font-size:14px;font-family:courier,vardana,arial;font-weight:bold;\"  width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
      echo " <tr align=center>\n";
      echo "  <td width=15%> TOTAL </td>\n";
      echo "  <td width=15%>$total[0]</td>\n";
      echo " </tr>\n";

}
echo "</table>";
echo "<body>\n";
echo "<body>\n";
?>
