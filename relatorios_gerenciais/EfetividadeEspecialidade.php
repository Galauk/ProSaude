<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "UNIDADE->".$uni_codigo."<br>";
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

$titulo="Efetividade de Atendimentos por Especialidade";    //       NOME DO RELATÓRIO

//------------------  Dados Recebidos  -------------------->

echo "
  <form method=post action=$PHP_SELF>
	<input type=hidden name=user value=$user>

	<fieldset>
	    <legend>Dados do Relatorio</legend>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
    <tr>
               <td width=70>Unidade:</td>
               <td>
                   <select name=uni_codigo class=boxr>";
                   $sql = pg_query('select * from unidade order by uni_desc');
                   echo "<option value=''> -- TODAS AS UNIDADES -- </option>";
                   while($uni=pg_fetch_array($sql)) {
				   		if($_REQUEST['uni_codigo'] == $uni['uni_codigo']){
                        	echo "<option value='$uni[uni_codigo]' selected> $uni[uni_desc]</option>";
						}else{
                        	echo "<option value='$uni[uni_codigo]'> $uni[uni_desc]</option>";

						}
                   }
             echo "</select>
              </td>
        </tr>
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

    if ($unidade == '')
       $UniNo = 'TODAS AS UNIDADES';

//---------------  Cabeçalho do Relatório  ---------------->

function cabeca($Tit, $dtIni, $dtFin, $UniNo, $Cab) {

        if ($Cab == 1) {
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0 align=center>
	 	   <tr>
	     	    <td width=130><font size=3 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	    <td width=10><font size=3 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	   </tr>
 	    	   <tr>
 	     	    <td colspan=2><font size=3 face=courier>".strtoupper($Tit)."</font></td>
 	    	   </tr>
 	    	   <tr>
 	     	    <td colspan=2><font size=3 face=courier>PERIODO: $dtIni A $dtFin</font></td>
 	    	   </tr>
 	    	   <tr>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
	    	   </tr>
 	          </table>";
 	    echo "<table style='font-size:14px; font-family:Tahoma,Arial;' width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	}

//----------------  Cabeçalho dos Dados  ------------------>

       if ($Cab == 0) {
           echo " <tr>\n";
           echo "  <td width=40% style='font-weight:bold'>Medico</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Agenda</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Atend</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Falta</td>\n";
          // echo "  <td width=12% style='font-weight:bold' align=center>Faltas</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Falta Prof.</td>\n";
           echo " </tr>\n";
        }
}

//-----------------  Captura dos Dados  ------------------->

$sql = "SELECT DISTINCT agendamento.esp_codigo, especialidade.esp_nome      " .
       "  FROM  agendamento                                                " .
       " INNER JOIN especialidade                                                 " .
       "    ON agendamento.esp_codigo = especialidade.esp_codigo            " .
       " WHERE agendamento.age_data between '$dt_inicial' AND '$dt_final'  ";
if ($uni_codigo) {
       $sql .= "   AND agendamento.uni_codigo = $uni_codigo                "; }
$sql .="ORDER BY especialidade.esp_nome";


//------------------> falta AGENTE na tabela agendamento   ( de 01-07-2005 até 15-07-2005 >2675 agendamentos )

//vSQL($sql,"1");

$query=pg_query($sql);
if (pg_num_rows($query) == 0) {
    echo "NÃO FORAM ENCONTRADOS DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "Data INICIAL..( ".$dt_inicial." )<br>";
    echo "Data FINAL....( ".$dt_final  ." )<br>";
    echo "UNIDADE.......( ".$uni_codigo." )<br>";
} else {

//----------------  Rotina de Impressão  ---------------->

  $Perc=array();
  $lin=999;
  $TotAtendidos = 0;
  $TotAgendados = 0;
  $TotNAtendidos = 0;
  $TotFaltas = 0;
  $TotTransferencias = 0;

  while($row=pg_fetch_row($query)) {
     if ($lin== 999) {
         cabeca($titulo, $dt_inicial, $dt_final, $UniNome, '1');
         cabeca($titulo, $dt_inicial, $dt_final, $UniNome, '0');
         $lin=6;
         $unidad=$row[8];
     }

	  //-------> total Agendamento, total agendado  $linha[0]
	  //-------> total Agendamento, compareceu      $linha[1]
	  //-------> total Agendamento, não compareceu  $linha[2]
	  //-------> total Agendamento, faltou          $linha[3]
	  //-------> total Agendamento, transferido     $linha[4]

     $sqlCount = "SELECT  count(age_codigo) as AgendAgend, " .
                 "        sum(case when age_atendido='S' then 1 else 0 end) as AgenSim,   " .
                 "        sum(case when age_atendido='N' then 1 else 0 end) as AgenNao,   " .
                 "        sum(case when age_atendido='F' then 1 else 0 end) as AgenFalt,  " .
                 "        sum(case when age_atendido='T' then 1 else 0 end) as AgenTransf " .
                 "  FROM  agendamento                                               " .
                 " WHERE  agendamento.esp_codigo = $row[0]                          ";
     if ($uni_codigo) {
         $sqlCount.= " AND agendamento.uni_codigo = $uni_codigo                     "; }
     $sqlCount .="   AND  agendamento.age_atendido <> 'T'";
     $sqlCount .="   AND  agendamento.age_data BETWEEN '$dt_inicial' AND '$dt_final'";
     $queryCount=pg_query($sqlCount);
     while($linha=pg_fetch_row($queryCount)) {

           $Perc[1]=((100*$linha[1])/$linha[0]);
           $Perc[2]=((100*$linha[2])/$linha[0]);
           $Perc[3]=((100*$linha[3])/$linha[0]);
           $Perc[4]=((100*$linha[4])/$linha[0]);

           echo " <tr>\n";
           echo "  <td>   $row[1]                      </td>\n";
           echo "  <td align=center>  $linha[0]                     </td>\n";
           echo "  <td align=center>  $linha[1] - ".round($Perc[1],1)."%</td>\n";
           echo "  <td align=center>  ".($linha[2]+$linha[3])." - ".round($Perc[2]+$Perc[3],1)."%</td>\n";
           //echo "  <td align=center>  $linha[3] - ".round($Perc[3],1)."%</td>\n";
           echo "  <td align=center>  $linha[4] - ".round($Perc[4],1)."%</td>\n";
           echo " </tr>\n";
           $lin++;
           $TotAgendados = $TotAgendados + $linha[0];
           $TotAtendidos = $TotAtendidos + $linha[1];
           $TotNAtendidos = $TotNAtendidos + $linha[2];
           $TotFaltas = $TotFaltas + $linha[3];
           $TotTransferencias = $TotTransferencias + $linha[4];
     }
  }
  echo " <tr style='font-weight:bold'>\n";
//  echo "  <td align=right>TOTAL  DE ATENDIDOS -&nbsp;&nbsp;&nbsp;&nbsp;$TotAtendidos</td>\n";
           $Perc[1]=((100*$TotAtendidos)/$TotAgendados);
           $Perc[2]=((100*$TotNAtendidos)/$TotAgendados);
           $Perc[3]=((100*$TotFaltas)/$TotAgendados);
           $Perc[4]=((100*$TotTransferencias)/$TotAgendados);
           echo " <tr>\n";
           echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           //echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           echo "  <td>  &nbsp; &nbsp;                 </td>\n";
           echo "</tr>";
           echo " <tr>\n";
           echo "  <td  style='font-weight:bold'>  Total Geral                 </td>\n";
           echo "  <td align=center  style='font-weight:bold'>  $TotAgendados                     </td>\n";
           echo "  <td align=center  style='font-weight:bold'>  $TotAtendidos- ".round($Perc[1],1)."%</td>\n";
           echo "  <td align=center  style='font-weight:bold'>  ".($TotNAtendidos+$TotFaltas)." - ".round($Perc[2]+$Perc[3],1)."%</td>\n";
           //echo "  <td align=center  style='font-weight:bold'>  $TotFaltas - ".round($Perc[3],1)."%</td>\n";
           echo "  <td align=center  style='font-weight:bold'>  $TotTransferencias - ".round($Perc[4],1)."%</td>\n";
           echo " </tr>\n";
  echo "  <td colspan=2> &nbsp </td>\n";
  echo " </tr>\n";
}

echo "</table>";
echo "</body>\n";
echo "</html>\n";
