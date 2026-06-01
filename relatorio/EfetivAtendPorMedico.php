<?php
/**
 * @version     11/05/07 17:22
 * @author      Leandro
 * @brief       Dados do final do relatório alterados
*/
?>
<!-- --------------  Funções javascript  --------------- -->

<script language=javascript>

function imprimir() {
       window.print();
}
</script>

<body>

<?php
//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "UNIDADE->".$uni_codigo."<br>";

$titulo="Efetividade de Atendimentos por Medico";    //       NOME DO RELATÓRIO

//------------------  Dados Recebidos  -------------------->

if ($uni_codigo) {
    $sql = "SELECT unidade.uni_desc " .
           "  FROM unidade " .
           " WHERE unidade.uni_codigo = $uni_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $UniNome=$row[0];
    }
} else {  $UniNome = "TODAS";  }

//---------------  Cabeçalho do Relatório  ---------------->

function cabeca($Tit, $dtIni, $dtFin, $UniNo, $Cab) {

        if ($Cab == 1) {
            include "cabecalho.php";

 	    echo "<table style='font-size:12px; font-family:Tahoma,Arial;' width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	}

//----------------  Cabeçalho dos Dados  ------------------>

       if ($Cab == 0) {
           echo " <tr>\n";
           echo "  <td width=40% style='font-weight:bold'>Medico</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Agendamentos</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Atendimentos</td>\n";
           echo "  <td width=12% style='font-weight:bold' align=center>Nao Atendidos</td>\n";
           echo " </tr>\n";
        }
}

//-----------------  Captura dos Dados  ------------------->

$sql = "SELECT DISTINCT agendamento.med_codigo, medico.med_nome      " .
       "  FROM  agendamento                                                " .
       " INNER JOIN medico                                                 " .
       "    ON agendamento.med_codigo = medico.med_codigo            " .
       " WHERE agendamento.age_data between '$dt_inicial' AND '$dt_final'  ";
if ($uni_codigo) {
       $sql .= "   AND agendamento.uni_codigo = $uni_codigo                "; }
$sql .="ORDER BY medico.med_nome";


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

  $TotAtendimentos = 0;
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
                 " WHERE  agendamento.med_codigo = $row[0]                          ";
     if ($uni_codigo) {
         $sqlCount.= " AND agendamento.uni_codigo = $uni_codigo                     "; }
     $sqlCount .="   AND  agendamento.age_data BETWEEN '$dt_inicial' AND '$dt_final'";
     $sqlCount .="   AND  agendamento.age_atendido <> 'T'";
     $queryCount=pg_query($sqlCount);
     while($linha=pg_fetch_row($queryCount)) {

	   $agendado = $linha[1] + $linha[2] + $linha[3] + $linha[4];
	   $naoatendido = $linha[2] + $linha[3] + $linha[4];
//           $Perc[1]=((100*$linha[1])/$linha[0]);
           $Perc[1]=((100*$linha[1])/$agendado);
           $Perc[2]=((100*$naoatendido)/$agendado);

           echo " <tr>\n";
           echo "  <td>   $row[1]                      </td>\n";
           echo "  <td align=center>  $agendado                     </td>\n";
           echo "  <td align=center>  $linha[1] - ".round($Perc[1],1)."%</td>\n";
           echo "  <td align=center>  $naoatendido - ".round($Perc[2],1)."%</td>\n";
           echo " </tr>\n";
           $lin++;
           $TotMedicos++;
           $TotPacientes+=$linha[0];
           $TotAgendamentos = $TotAgendamentos + $agendado;
           $TotAtendimentos = $TotAtendimentos + $linha[1];
           $TotNaoAtendidos = $TotNaoAtendidos + $naoatendido;
     }
  }
  echo " <tr><td>&nbsp</td></tr>\n";
/*
  echo " <tr style='font-weight:bold'>\n";
  echo "  <td align=right>TOTAL  DE MEDICOS -&nbsp;&nbsp;&nbsp;&nbsp;$TotMedicos&nbsp;&nbsp; </td>\n";
  echo "  <td align=center> $TotPacientes </td>\n";
  echo "  <td>  PACIENTES </td>\n";
//  echo "  <td colspan=2> &nbsp </td>\n";
  echo " </tr>\n";
*/
           $Perc[1]=(100*$TotAtendimentos/$TotAgendamentos);
           $Perc[2]=(100*$TotNaoAtendidos/$TotAgendamentos);
  echo " <tr style='font-weight:bold'>\n";
  echo "  <td align=right>TOTAL DE AGENDAMENTOS - &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>\n";
  echo "  <td align=center>&nbsp;$TotAgendamentos          </td>\n";
  echo "  <td align=center>&nbsp;$TotAtendimentos - " . round($Perc[1],1)."%</td>\n";
  echo "  <td align=center>&nbsp;$TotNaoAtendidos - " . round($Perc[2],1)."%</td>\n";
  echo " </tr>\n";
}

echo "</table>";
echo "</body>\n";
echo "</html>\n";
