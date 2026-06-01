<!-- --------------  Funções javascript  --------------- -->

<SCRIPT Language="Javascript">

function imprimir() {
       window.print();
}

</script>

<body onload='imprimir()'>
<?php

//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//------------------  Dados Recebidos  -------------------->

//echo "dt_inicial->".$dt_inicial."<br>";
//echo "dt_final  ->".$dt_final."<br>";
//echo "med_codigo->".$med_codigo."<br>";
//echo "esp_codigo->".$esp_codigo."<br>";
//echo "uni_codigo->".$uni_codigo."<br>";
//echo "TpAgendame->".$TpAgendame."<br>";
//echo "MostAgente->".$MostAgente."<br>";


$titulo="Relatorio de Pacientes";    //       NOME DO RELATÓRIO

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

        if ($Cab == 0) {
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0>\n";
	 	    echo " <tr>\n";
	     	echo "  <td width=200><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>\n";
         	echo "  <td width=10 align=right><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
	    	echo " </tr>\n";
 	    	echo " <tr>\n";
 	     	echo "  <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>\n";
 	    	echo " </tr>\n";
 	    	echo " <tr>\n";
 	     	echo "  <td colspan=2><font size=1 face=courier>DATA:  $dtIni </font></td>\n";
 	    	echo " </tr>\n";
 	    	echo " <tr>\n";
 	     	echo "  <td colspan=2><font size=1 face=courier>UNIDADE: $UniNo</font></td>\n";
 	        echo "</tr>\n";
		echo "</table>\n";
 	        echo "<br>\n";
# 	    echo "<table style=\"font-size:10px;font-family:Tahoma,Arial;\" width=100% align=left cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
 	}

//----------------  Cabeçalho dos Dados  ------------------>
       if ($Cab == 1) {
           echo "<table style=\"font-size:10px;font-family:Tahoma,Arial;\" width=100% align=left cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
           echo " <tr>\n";
           echo "  <td width= 60>Codigo</td>\n";
           echo "  <td width=250>Paciente/Mae</td>\n";
           echo "  <td width= 90>Mae</td>\n";
           echo "  <td width= 90>Dt.Nasc.</td>\n";
           echo "  <td width= 90>CISVIR</td>\n";
           echo " </tr></table>\n";
        }
}

//-----------------  Captura dos Dados  ------------------->

$sql = "SELECT medico.med_nome, especialidade.esp_nome, TO_CHAR(agendamento.age_data, 'DD/MM/YY'),
               usuario.usu_prontuario, usuario.usu_nome, TO_CHAR(usuario.usu_datanasc, 'DD/MM/YY'),
               usuario.usu_cisvir, usuario.usu_mae,
               agente.agt_responsavel, agente.agt_descricao, agendamento.age_hora  " .
       "  FROM agendamento, unidade, medico, usuario, especialidade, agente " .
       " WHERE agendamento.uni_codigo = unidade.uni_codigo " .
       "   AND agendamento.med_codigo = medico.med_codigo " .
       "   AND agendamento.usu_codigo = usuario.usu_codigo " .
       "   AND agendamento.esp_codigo = especialidade.esp_codigo " .
       "   AND agendamento.agt_codigo = agente.agt_codigo " .
       "   AND agendamento.age_atendido = 'N' ".
       "   AND agendamento.age_data BETWEEN '$dt_inicial' AND '$dt_final' ";
if ($med_codigo) {
    $sql.= "   AND agendamento.med_codigo = $med_codigo "; }
if ($esp_codigo) {
    $sql.= "   AND agendamento.esp_codigo = $esp_codigo "; }
if ($uni_codigo) {
    $sql.= "   AND agendamento.uni_codigo = $uni_codigo "; }
if ($TpAgendame) {
    $sql.= "   AND agendamento.age_item = '$TpAgendame' "; }
$sql.= "ORDER BY agendamento.age_data";
$sql.= ", medico.med_nome, agendamento.age_hora ";
if ($MostAgente==0) {
    $sql.= " , agente.agt_responsavel  "; }

//echo $sql;

$query=pg_query($sql);

if (pg_num_rows($query) == 0) {
    echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=50% align=left cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
    echo "  <tr><td align=center colspan=3>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>\n";
    echo "  <tr><td align=right  colspan=3>&nbsp;</td></tr>\n";
    echo "  <tr><td align=right  width=5%>Data INICIAL</td>\n";
    echo "      <td align=center width=1%>.....</td>\n";
    echo "      <td align=left   width=30%>$dt_inicial</td></tr\n";
    echo "  <tr><td align=right>Data FINAL</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$dt_final</td></tr\n";
    echo "  <tr><td align=right>Médico</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$med_codigo</td></tr\n";
    echo "  <tr><td align=right>Especialização</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$esp_codigo</td></tr\n";
    echo "  <tr><td align=right>Unidade</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$uni_codigo</td></tr\n";
    echo "  <tr><td align=right>Tipo Agenda</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$TpAgendame</td></tr>\n";
    echo "</table>\n";
}

//----------------  Rotina de Impressão  ---------------->

$lin=999;
$dataAgend=0;
         cabeca($titulo, $dt_inicial, $dt_final, $UniNome, '0');
      echo "<table style=\"font-size:10px;font-family:Tahoma,Arial;\" width=100% align=left cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
while($row=pg_fetch_row($query)) {
      if (($medico != $row[0]) || ($especialidade != $row[1]) || ($hora != $row[10])) {
         if (medico) {
             echo "<tr>\n";
             echo "  <td colspan=6>&nbsp;</td>\n";
             echo "</tr>\n";
             $lin++;
         }
         echo "<tr>\n";
         echo "  <td colspan=6 style=\"font-weight:bold\"><font size=2><I>*** Médico - $row[0]&nbsp;&nbsp; -- &nbsp;&nbsp;$row[1] - $row[10]</I></font></td>\n";   // Quebra Médico
         echo "</tr>\n";
         $medico=$row[0];
         $especialidade=$row[1];
         $hora=$row[10];
      }
      echo " <tr>\n";
      echo "  <td width=12%>$row[3]</td>\n";
      echo "  <td width=30%>".substr($row[4],0,35)."</td>\n";
      echo "  <td width=30%>".substr($row[7],0,35)."</td>\n";
      echo "  <td width=12%>$row[5]</td>\n";
      echo "  <td align=center width=5%>$row[6]</td>\n";
      echo " </tr>\n";
      $lin++;
}
echo "</table>";

?>
