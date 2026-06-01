<!-- --------------------------------------------------------------
       Funções javascript
------------------------------------------------------------------ -->

<SCRIPT Language="Javascript">

function imprimir() { window.print(); }

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

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL--->".$dt_final."<br>";
//echo "Paciente----->".$pac_codigo."<br>";
//echo "Medico------->".$med_codigo."<br>";
//echo "Procedimento->".$proc_codigo."<br>";
//echo "Tipo Custo--->".$tp_custo."<br>";


switch ($tp_custo) {
   case 0: $TPCusto="TODOS";       break;
   case 1: $TPCusto="Custo Medio"; break;
   case 2: $TPCusto="Custo Referencia";
}

$titulo="RELATORIO DE TEMPO DE ATENDIMENTO DO PAM";    //       NOME DO RELATÓRIO

if ($pac_codigo) {
    $sql = "SELECT usuario.usu_nome  FROM  USUARIO  " .
           " WHERE usuario.usu_codigo = $pac_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $PACNome=$row[0];
    }
} else {  $PACNome = "TODOS";  }
if ($med_codigo) {
    $sql = "SELECT medico.med_nome  FROM  MEDICO  " .
           " WHERE medico.med_codigo = $med_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $MEDNome=$row[0];
    }
} else {  $MEDNome = "TODOS";  }
if ($proc_codigo) {
    $sql = "SELECT procedimento.proc_nome  FROM  PROCEDIMENTO   " .
           " WHERE procedimento.proc_codigo = $proc_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $PROCNome=$row[0];
    }
} else {  $PROCNome = "TODOS";  }


//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $Pac, $Med, $Proc, $TPCust, $Cab) {

//--->        Cabeçalho do Sistema

        if ($Cab == 0) {
            echo "<table width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	           <tr>
	     	        <td width=80><font size=1 face=courier>GESTAO PUBLICA DE SAUDE</font></td>
         	        <td width=10 align=right><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>
           	        <td width=10></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier> $Tit </font></tdu
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier>PERIODO: $dtIni A $dtFin </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier>USUARIO: $Pac </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier>MEDICO: $Med </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier>PROCEDIMENTO: $Proc </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=3><font size=1 face=courier>TIPO DE CUSTO: $TPCust </font></td>
 	    	       </tr>
 	    	       <tr>
		            <td colspan=4>&nbsp;</td>
	    	       </tr>
 	              </table>";

 	    echo "<table style=\"font-size:11px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
 	}

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold'>\n";
           echo "  <td> PACIENTE                   </td>\n";
           echo "  <td> DATA/HORA INICIO </td>\n";
           echo "  <td> DATA/HORA FIM    </td>\n";
           echo "  <td align=center> TEMPO DO ATEND.  </td>\n";
           echo " </tr>\n";
        }
}

//----------------  Rotina de Impressão  ---------------->

$sql="SELECT distinct usuario.usu_nome, medico.med_nome, atendimento.ate_data, atendimento.ate_hora,
                      atendimento.ate_datafinal, atendimento.ate_horafinal, atendimento.ate_codigo
        FROM atendimento, procedimento_atendimento, usuario, medico "           ;
if ($proc_codigo) { $sql.= ", procedimento "                                                      ; }
$sql .= " WHERE atendimento.ate_codigo = procedimento_atendimento.ate_codigo 
            AND atendimento.usu_codigo = usuario.usu_codigo 
            AND atendimento.med_codigo = medico.med_codigo "                                ; 
if ($dt_inicial ) { $sql .= " AND atendimento.ate_data  between '$dt_inicial' and  '$dt_final'  " ; }
if ($proc_codigo) { $sql .= " AND procedimento_atendimento.proc_codigo = procedimento.proc_codigo
                              AND procedimento.proc_codigo = $proc_codigo "                       ; }
if ($med_codigo ) { $sql .= " AND atendimento.med_codigo = $med_codigo "                          ; }
if ($pac_codigo ) { $sql .= " AND atendimento.usu_codigo = $pac_codigo "                          ; }
$sql .= " ORDER BY usuario.usu_nome, medico.med_nome, atendimento.ate_data, atendimento.ate_hora,
                   atendimento.ate_datafinal, atendimento.ate_horafinal   "                 ;
//vSQL($sql,"1");

$queryAtend=pg_query($sql);

if (pg_num_rows($queryAtend) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS <br><br>";  
    echo $titulo."<br>";
    echo " INICIAL________" .$dt_inicial."<br>";
    echo " FINAL__________" .$dt_final."<br>";
    echo " Medico_________" .$MEDNome."<br>";
    echo " Procedimento___" .$PROCNome."<br>";
    echo " Tipo Custo______".$TPCusto."<br>";
}
$lin=999;
$TotPaciente=$TotRelatorio=0;

while($rowAtend=pg_fetch_row($queryAtend)) {

      if ($lin== 999) {
          cabeca($titulo, $dt_inicial, $dt_final, $PACNome, $MEDNome, $PROCNome, $TPCusto, '0');
          $lin=9;
      }
      $d=explode("-",$rowAtend[2]);
      $h=explode(":",$rowAtend[3]);
      $dt_in=mktime($h[0],$h[1],00,$d[1],$d[2],$d[0]);

      $d=explode("-",$rowAtend[4]);
      $h=explode(":",$rowAtend[5]);
      $dt_fin=mktime($h[0],$h[1],00,$d[1],$d[2],$d[0]);

      echo " <tr>\n";
      echo "  <td>"             .substr($rowAtend[0],0,40)                                       ."</td>\n";
      echo "  <td>&nbsp;"       .substr(inv_data($rowAtend[2]),0,10)." <> ".$rowAtend[3]         ."</td>\n";
      echo "  <td>"             .substr(inv_data($rowAtend[4]),0,10)." <> ".$rowAtend[5]         ."</td>\n";
      echo "  <td align=center>".intval((($dt_fin-$dt_in)/60)/60).":".str_pad((($dt_fin-$dt_in)/60)%60,2,"0",str_pad_left)."&nbsp;&nbsp;hh:mm"."</td>\n";
      echo " </tr>\n";
      $lin++;
}  
echo "</table>";
echo "</body>";

?>
