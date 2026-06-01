<!-- --------------  Funções javascript  --------------- -->

<script language=javascript>

function imprimir() {
       window.print();
}
</script>

<body onload='imprimir()'>

<?php

//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "UNIDADE->".$uni_codigo."<br>";

$titulo="Faltosos por Periodo";    //       NOME DO RELATÓRIO

//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Monta Dados Recebidos  ---------------->

if ($uni_codigo) {
    $sql = "SELECT unidade.uni_desc " .
           "  FROM unidade " .
           " WHERE unidade.uni_codigo = $uni_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $UniNome=$row[0];
    }
} else {  $UniNome = "TODAS";  }


//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtIni, $dtFin, $Uni, $Cab) {

        if ($Cab == 1) {
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0 align=center>
	 	   <tr>
	     	    <td width=130><font size=1 face=courier>GESTAO PUBLICA DE SAUDE</font></td>
         	    <td width= 10><font size=1 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	   </tr>
 	    	   <tr>
 	     	    <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>
 	    	   </tr>
 	    	   <tr>
 	     	    <td colspan=2><font size=1 face=courier>PERIODO: $dtIni A $dtFin</font></td>
 	    	   </tr>
 	    	   <tr>
 	     	    <td colspan=2><font size=1 face=courier>UNIDADE: $Uni</font></td>
 	    	   </tr>
 	    	   <tr>
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	   </tr>
 	          </table>";
 	    echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
 	}

//----------------  Cabeçalho dos Dados  ------------------>

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold'>\n";
           echo "  <td width=48%>PACIENTE</td>\n";
           echo "  <td width=48%>MEDICO / LABORATORIO</td>\n";
           echo "  <td>TIPO</td>\n";
           echo " </tr>\n";
        }
}

//------------------  Captura Dados  -------------------->

$lin=999;

$sql = "SELECT agendamento.age_codigo,  TO_CHAR(agendamento.age_data,'DD/MM/YY'),
               agendamento.usu_codigo,  usuario.usu_nome,  agendamento.med_codigo,
               medico.med_nome,  agendamento.age_tipo" .
       "  FROM agendamento, usuario, unidade, medico  " .
       " WHERE agendamento.age_atendido = 'N'        " .
       "   AND agendamento.usu_codigo = usuario.usu_codigo " .
       "   AND agendamento.uni_codigo = unidade.uni_codigo " .
       "   AND agendamento.med_codigo = medico.med_codigo " .
       "   AND medico.med_codigo <> 250 " ;
if ($uni_codigo) {
    $sql.= " AND unidade.uni_codigo = $uni_codigo " ; 
}
$sql.= "   AND agendamento.age_data between '$dt_inicial' AND '$dt_final' " .
       "ORDER BY agendamento.age_data,  usuario.usu_nome ";
//vSQL($sql,"1");

$query=pg_query($sql);

//----------------  Rotina de Impressão  ---------------->

if (pg_num_rows($query) == 0) {
    echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=70% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
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
    echo "      <td align=right>UNIDADE</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$uni_codigo</td><td>&nbsp;</td></tr>\n";
    echo "</table>\n";
}
else {
      while($row=pg_fetch_row($query)) {
            if ($lin== 999) {
                cabeca($titulo, $dt_inicial, $dt_final, $UniNome, '1');
            }
            if ($dia != $row[1]) {
                if ($dia) {
                    echo "<tr>\n";
                    echo "  <td colspan=6>&nbsp;</td>\n";
                    echo "</tr>\n";
                    $lin++;
                }
            echo "<tr>\n";
            echo "  <td colspan=6 style='font-weight:bold'<I>*** Data - $row[1]</I></td>\n";   // Quebra Data
            echo "</tr>\n";
            $lin++;
            cabeca($titulo, $dt_inicial, $dt_final, $UniNome, '0');
            $lin++;

            $dia=$row[1];
            }
            echo " <tr>\n";
            echo "  <td width=10%>$row[3]</td>\n";
            echo "  <td width=10%>$row[5]</td>\n";
            echo "  <td width=10%>&nbsp;&nbsp;$row[6]</td>\n";
            echo " </tr>\n";
           $lin++;
           $TotFaltosos++;
      }
}
   echo " <tr style='font-weight:bold'>\n";
   echo "  <td align=right>TOTAL  DE FALTANTES -&nbsp;&nbsp;&nbsp;&nbsp;$TotFaltosos</td>\n";
   echo "  <td colspan=2> &nbsp </td>\n";
   echo " </tr>\n";

echo "</table>";
echo "</body>\n";
echo "</html>\n";
