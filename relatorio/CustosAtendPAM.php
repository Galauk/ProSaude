<!-- --------------------------------------------------------------
       Funções javascript
------------------------------------------------------------------ -->

<SCRIPT Language="Javascript">

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
//echo "Sint / Anali->".$SinAna."<br>";


switch ($tp_custo) {
   case 0: $TPCusto="TODOS"; break;
   case 1: $TPCusto="Custo Medio"; break;
   case 2: $TPCusto="Custo Referencia";
}
switch ($SinAna) {
   case 1: $SintetAnalit="Sintetico"; break;
   case 2: $SintetAnalit="Analitico";
}

$titulo="RELATORIO DE CUSTOS DE ATENDIMENTO";    //       NOME DO RELATÓRIO

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

function cabeca($Tit, $dtIni, $dtFin, $Pac, $Med, $Proc, $TPCust, $SinteAnali, $Cab) {

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
 	     	        <td colspan=3><font size=1 face=courier>SINTET / ANALIT: $SinteAnali </font></td>
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
           echo "  <td> MEDICO                     </td>\n";
           echo "  <td colspan=2> DATA/HORA INICIO </td>\n";
           echo "  <td colspan=2> DATA/HORA FIM    </td>\n";
           echo "  <td colspan=2>&nbsp;            </td>\n";
           echo " </tr>\n";
        }
       if ($Cab == 1) {
           echo " <tr style='font-weight:bold'>\n";
           echo "  <td>&nbsp;                      </td>\n";
           echo "  <td> MEDICAMENTO                </td>\n";
           echo "  <td colspan=2>&nbsp; QTDE       </td>\n";
           echo "  <td colspan=2> CUSTO UNIT.      </td>\n";
           echo "  <td> CUSTO TOTAL                </td>\n";
           echo "  <td> &nbsp;                     </td>\n";
           echo " </tr>\n";
        }
       if ($Cab == 2) {
           echo " <tr style='font-weight:bold'>\n";
           echo "  <td>&nbsp;                      </td>\n";
           echo "  <td> PROCEDIMENTO               </td>\n";
           echo "  <td> VALOR                      </td>\n";
           echo "  <td colspan=5> &nbsp;           </td>\n";
           echo " </tr>\n";
        }
}

//----------------  Rotina de Impressão  ---------------->

$sql  = "SELECT atendimento.ate_codigo, usuario.usu_nome, medico.med_nome, 
                atendimento.ate_data, atendimento.ate_hora,
                atendimento.ate_datafinal, atendimento.ate_horafinal
           FROM atendimento, procedimento_atendimento, usuario, medico "        ;
if ($proc_codigo) { $sql.= ", procedimento "                                                      ; }
$sql .= " WHERE atendimento.ate_codigo = procedimento_atendimento.ate_codigo 
            AND atendimento.usu_codigo = usuario.usu_codigo 
            AND atendimento.med_codigo = medico.med_codigo "                                ; 
if ($dt_inicial ) { $sql .= " AND atendimento.ate_data  between '$dt_inicial' and  '$dt_final'  " ; }
if ($proc_codigo) { $sql .= " AND procedimento_atendimento.proc_codigo = procedimento.proc_codigo
                              AND procedimento.proc_codigo = $proc_codigo "                       ; }
if ($med_codigo ) { $sql .= " AND atendimento.med_codigo = $med_codigo "                          ; }
if ($pac_codigo ) { $sql .= " AND atendimento.usu_codigo = $pac_codigo "                          ; }
$sql .= " ORDER BY usuario.usu_nome, atendimento.ate_codigo, medico.med_nome, atendimento.ate_data, 
                   atendimento.ate_hora, atendimento.ate_datafinal, atendimento.ate_horafinal   " ;
//vSQL($sql,"1");

$queryAtend=pg_query($sql);

if (pg_num_rows($queryAtend) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS <br><br>";  
    echo $titulo."<br>";
    echo " INICIAL________".$dt_inicial."<br>";
    echo " FINAL__________".$dt_final."<br>";
    echo " Medico_________".$med_codigo."-".$MEDNome."<br>";
    echo " Procedimento___".$proc_codigo."-".$PROCNome."<br>";
    echo " Tipo Custo______".$TPCusto."<br>";
    echo " Sint/Analit_______".$SintetAnalit."<br>";
}
$lin=999;
$TotPaciente=$TotRelatorio=0;
while($rowAtend=pg_fetch_row($queryAtend)) {

      if ($lin== 999) {
          cabeca($titulo, $dt_inicial, $dt_final, $PACNome, $MEDNome, $PROCNome, $TPCusto, $SintetAnalit, '0');
          $lin=9;
      }
      echo " <tr>\n";
      echo "  <td>".substr($rowAtend[1],0,40)                                 ."</td>\n";
      echo "  <td>".substr($rowAtend[2],0,40)                                 ."</td>\n";
      echo "  <td colspan=2>&nbsp;".substr(inv_data($rowAtend[3]),0,10)." <> ".$rowAtend[4]."</td>\n";
      echo "  <td colspan=2>&nbsp;".substr(inv_data($rowAtend[5]),0,10)." <> ".$rowAtend[6]."</td>\n";
      echo "  <td colspan=2>&nbsp;                                              </td>\n";
      echo " </tr>\n";
      $lin++;
/*custo    MEDICAMENTO / MATERIAL                                 */
      $sql="SELECT itens_movimento.pro_codigo, produto.pro_nome, itens_movimento.ite_quantidade,
                   itens_movimento.ite_vlrunit 
              FROM movimento, itens_movimento, produto
             WHERE movimento.mov_codigo = itens_movimento.mov_codigo
               AND movimento.mov_tipo = 'S'
               AND itens_movimento.pro_codigo = produto.pro_codigo
               AND movimento.ate_codigo = $rowAtend[0]";
      $queryMedMat=pg_query($sql);
      if ($SinAna==2) {
          if (pg_num_rows($queryMedMat) > 0) {
              cabeca($titulo, $dt_inicial, $dt_final, $PACNome, $MEDNome, $PROCNome, $TPCusto, $SintetAnalit, '1');
          } else { ECHO "NAO ACHEI NADA 1"; }
      }
      $TotMedMatHosp=0;
      while($rowMedMat=pg_fetch_row($queryMedMat)) {
            $VlrMov=$rowMedMat[2] * $rowMedMat[3];
            if ($SinAna==2) {
                echo " <tr>\n";
                echo "  <td>&nbsp;</td>\n";
                echo "  <td>".substr($rowMedMat[1],0,40)."</td>\n";
                echo "  <td align=right>".$rowMedMat[2]             ."</td>\n";
                echo "  <td width=10%>&nbsp;</td>\n";
                echo "  <td align=right>".$rowMedMat[3]             ."</td>\n";
                echo "  <td width=10%>&nbsp;</td>\n";
                echo "  <td align=right>".formata_valor($VlrMov)    ."</td>\n";
                echo "  <td width=5%>&nbsp;</td>\n";
                echo " </tr>\n";
            }
            $TotMedMatHosp=$TotMedMatHosp+$VlrMov;
            $lin++;
      }
      if ($TotMedMatHosp > 0  ||  pg_num_rows($queryMedMat) > 0) {
          echo " <tr>\n";
          if ($SinAna==2) {
              echo "  <td>&nbsp;</td>\n";
              echo "  <td colspan=3 align=right><hr3 style='font-weight:bold'>TOTAL</hr3> Medicamentos/Material HOSPITALAR -&nbsp;&nbsp; </td>\n";
              echo "  <td colspan=3 align=right>".formata_valor($TotMedMatHosp)."</td>\n";
              echo "  <td width=5%>&nbsp;</td>\n";
          } else {
                  echo "  <td colspan=2 align=right><hr3 style='font-weight:bold'>TOTAL</hr3> Medicamentos/Material HOSPITALAR -&nbsp;&nbsp; </td>\n";
              echo "  <td colspan=1 align=right>".formata_valor($TotMedMatHosp)."</td>\n";
              echo "  <td colspan=3>&nbsp;</td>\n";
              echo "  <td colspan=1 width=15%>&nbsp;</td>\n";
            }     
                  
          echo " </tr>\n";
      }
/*custo  PROCEDIMENTO                                             */
      $sql="SELECT procedimento.proc_nome, procedimento.proc_valor
              FROM procedimento, procedimento_atendimento
             WHERE procedimento.proc_codigo = procedimento_atendimento.proc_codigo
               AND procedimento.proc_exame <> 'S'
               AND procedimento_atendimento.ate_codigo = $rowAtend[0]";
      $queryProc=pg_query($sql);
      if ($SinAna==2) {
          if (pg_num_rows($queryProc) > 0) {
              cabeca($titulo, $dt_inicial, $dt_final, $PACNome, $MEDNome, $PROCNome, $TPCusto, $SintetAnalit, '2');
          }
      }
      $TotProcHosp=0;
      while($rowProc=pg_fetch_row($queryProc)) {
            if ($SinAna==2) {
                echo " <tr>\n";
                echo "  <td>&nbsp;</td>\n";
                echo "  <td>".substr($rowProc[0],0,40)  ."</td>\n";
                echo "  <td align=right>".formata_valor($rowProc[1])."</td>\n";
                echo "  <td colspan=5>&nbsp;</td>\n";
                echo " </tr>\n";
            }
            $TotProcHosp=$TotProcHosp+$rowProc[1];
            $lin++;
      }
      if ($TotProcHosp > 0  ||  pg_num_rows($queryProc) > 0) {
          echo " <tr>\n";
          echo "  <td colspan=2 align=right><hr3 style='font-weight:bold'>TOTAL</hr3>  Procedimento HOSPITALAR -&nbsp;&nbsp; </td>\n";
          echo "  <td align=right>".formata_valor($TotProcHosp)."</td>\n";
          echo "  <td colspan=5>&nbsp;&nbsp;</td>\n";
          echo " </tr>\n";
      }
/*custo  EXAMES / DIAGNOSTICO                                     */
      $sql="SELECT procedimento.proc_nome, procedimento.proc_valor
              FROM procedimento, procedimento_atendimento
             WHERE procedimento.proc_codigo = procedimento_atendimento.proc_codigo
               AND procedimento.proc_exame = 'S'
               AND procedimento_atendimento.ate_codigo = $rowAtend[0]";
      $queryExam=pg_query($sql);
      if ($SinAna==2) {
          if (pg_num_rows($queryExam) > 0) {
              cabeca($titulo, $dt_inicial, $dt_final, $PACNome, $MEDNome, $PROCNome, $TPCusto, $SintetAnalit, '2');
          }
      }
      $TotExamHosp=0;
      while($rowExam=pg_fetch_row($queryExam)) {
            if ($SinAna==2) {
                echo " <tr>\n";
                echo "  <td>&nbsp;</td>\n";
                echo "  <td>".substr($rowExam[0],0,40)."</td>\n";
                echo "  <td align=right>".$rowExam[1] ."</td>\n";
                echo "  <td colspan=5>&nbsp;            </td>\n";
                echo " </tr>\n";
            }
            $TotExamHosp=$TotExamHosp+$rowExam[1];
            $lin++;
      }
      if ($TotExamHosp > 0  ||  pg_num_rows($queryExam) > 0) {
          echo " <tr>\n";
          echo "  <td colspan=2 align=right><hr3 style='font-weight:bold'>TOTAL</hr3>  Exame/Diagnóstico HOSPITALAR -&nbsp;&nbsp; </td>\n";
          echo "  <td align=right>".formata_valor($TotExamHosp)."</td>\n";
          echo "  <td colspan=5>&nbsp;&nbsp;</td>\n";
          echo " </tr>\n";
      }
      $TotPaciente=$TotMedMatHosp+$TotProcHosp+$TotExamHosp;
      echo " <tr style='font-weight:bold'>\n";
      echo "  <td colspan=1 align=right>TOTAL  DO PACIENTE -&nbsp;&nbsp; </td>\n";
      echo "  <td align=left colspan=7>&nbsp;&nbsp;".formata_valor($TotPaciente)."</td>\n";
      echo " </tr>\n";
      echo " <tr><td>&nbsp;&nbsp;</td></tr>\n";
      $TotRelatorio=$TotRelatorio+$TotPaciente;
      $TotMedMatHosp=$TotProcHosp=$TotExamHosp=$TotPaciente=0;
}  
echo " <tr><td>&nbsp;&nbsp;</td></tr>\n";
echo " <tr><td>&nbsp;&nbsp;</td></tr>\n";
echo " <tr style='font-weight:bold'>\n";
echo "  <td colspan=1 align=right>TOTAL  DO RELATÓRIO -&nbsp;&nbsp; </td>\n";
echo "  <td align=left colspan=7>&nbsp;&nbsp;".formata_valor($TotRelatorio)."</td>\n";
echo " </tr>\n";

echo "</table>";
echo "</body>";

?>
 
