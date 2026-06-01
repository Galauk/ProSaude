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
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
echo "<body>
     <link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

$titulo="Atendimentos no PAM de Outras Cidades";    //       NOME DO RELATÓRIO

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "Hora INICIAL->".$hr_inicial."<br>";
//echo "Hora FINAL  ->".$hr_final."<br>";


if ($uni_codigo) {
    $sql = "SELECT unidade.uni_desc " .
           "  FROM unidade " .
           " WHERE unidade.uni_codigo = $uni_codigo";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $UniNome=$row[0];
    }
} else {  $UniNome = "TODAS";  }


//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Uni, $Cab) {

//--->        Cabeçalho do Sistema

       if ($Cab == 0) {

          echo "<table width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=1 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>Relatório - ".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>PERIODO: $dtIni $hrIni A $dtFin $hrFin</font></td>
 	    	       </tr>
 	    	       <tr>
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	       </tr>
 	              </table>";
 	    echo "<table style=\"font-size:11px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
 	    }

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold'>\n";
           echo "  <td width=20%>Cidade            </td>\n";
           echo "  <td width=10%>&nbsp;&nbsp;&nbsp;Data </td>\n";
           echo "  <td width=10%>Hora     </td>\n";
           echo "  <td width=30%>Paciente </td>\n";
           echo "  <td width=10%>Dt.Nasc. </td>\n";
           echo "  <td width=10%>Idade </td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->

$sql = "SELECT usu_nome, datanasc, ate_hora, to_char(ate_data, 'dd/mm/yyyy'), usu_sexo, 
	calcula_idade(usu_codigo) as Idade, nomecidade 
        " .
       "  FROM v_atend_pam" .
       " WHERE 
       ate_data between '$dt_inicial' and '$dt_final'  
       AND nomecidade <> 'APUCARANA'" ; 
$sql.= " Order By nomecidade, ate_data,
                  ate_hora,
                  usu_nome ";
//vSQL($sql,"1");

$lin=999;
$query=pg_query($sql);
if (pg_num_rows($query) == 0) {
    echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=70% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
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
    echo "      <td align=right>Hora INICIAL</td>\n";
    echo "      <td align=center>.....</td>\n";
    echo "      <td align=left>$hr_inicial</td><td>&nbsp;</td></tr\n";
    echo "  <tr><td align=right></td>\n";
    echo "      <td align=right>Hora FINAL</td>\n";
    echo "      <td align=center>........</td>\n";
    echo "      <td align=left>$hr_final</td><td>&nbsp;</td></tr>\n";
    echo "</table>\n";
}
else {
      $hr_Par_fim=explode(":",$hr_final);
      $hr_Par_inicio=explode(":",$hr_inicial);

      $dt_final  =str_replace("/", "-", $dt_final);
      $dt_inicial=str_replace("/", "-", $dt_inicial);

      $dt_Par_fim=explode("-",$dt_final);
      $dt_Par_inicio=explode("-",$dt_inicial);

      $dt_Par_fim=$dt_Par_fim[2].$dt_Par_fim[1].$dt_Par_fim[0];
      $hr_Par_fim=$hr_Par_fim[0].$hr_Par_fim[1];
      $dt_Par_inicio=$dt_Par_inicio[2].$dt_Par_inicio[1].$dt_Par_inicio[0];
      $hr_Par_inicio=$hr_Par_inicio[0].$hr_Par_inicio[1];
      $quant = 0;

      while($row=pg_fetch_row($query)) {

          $dt_BD=explode("-",$row[3]);
          $dt_BD=$dt_BD[0].$dt_BD[1].$dt_BD[2];
          if ($dt_Par_inicio==$dt_BD) {
              $hr_BD=explode(":",$row[3]);
              $hr_BD=$hr_BD[0].$hr_BD[1];
              if ($hr_BD < $hr_Par_inicio) continue;
          }
          if ($dt_Par_fim==$dt_BD) {
              $hr_BD=explode(":",$row[3]);
              $hr_BD=$hr_BD[0].$hr_BD[1];
              if ($hr_BD > $hr_Par_fim)    continue;
          }
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0');
              $lin=9;
          }
          echo " <tr>\n";
	  if ($cidadeant <> $row[6]) { 
	      if ($quant > 0) {
                 echo "  <td> Total de Pacientes </td>\n";
                 echo "  <td>". $quant ."</td>\n";
                 echo "  <td colspan=4> &nbsp;</td>\n";
		 echo "</tr>";
                 echo "<tr> <td colspan=6> &nbsp; </td></tr>";
		 echo "<tr>";
	      }
              echo "  <td>$row[6]</td>\n";
	      $quant = 0;
	  }    
          else {
              echo "  <td > &nbsp; </td>\n";
	  }    

         		      
          echo "  <td>$row[3]</td>\n";
          echo "  <td>$row[2]</td>\n";
          echo "  <td>$row[0]</td>\n";
          echo "  <td>$row[1]</td>\n";
          echo "  <td>$row[5]</td>\n";
          echo " </tr>\n";
	  $quant = $quant + 1;
	  $quanttotal = $quanttotal + 1;
	  $cidadeant = $row[6];

      }
                 echo "  <td> Total de Pacientes </td>\n";
                 echo "  <td>". $quant."</td>\n";
                 echo "  <td colspan=4> &nbsp;</td>\n";
		 echo "</tr>";
                 echo "<tr> <td colspan=6> &nbsp; </td></tr>";
		 echo "<tr>";
                 echo "  <td colspan=2> Total de Pacientes de Outras Cidades</td>\n";
                 echo "  <td>". $quanttotal ."</td>\n";
                 echo "  <td colspan=3> &nbsp;</td>\n";
		 echo "</tr>";
                 echo "<tr> <td colspan=6> &nbsp; </td></tr>";
		 echo "<tr>";
      echo "</table>\n";


}
echo "</table>";
echo "<body>\n";
?>
