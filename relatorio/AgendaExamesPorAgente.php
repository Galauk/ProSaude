<!-- --------------  Funções javascript  --------------- -->

<SCRIPT Language="Javascript">
function imprimir(){
   window.print() ;
}
</script>

<body onload='imprimir()'>

<?php
//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";   
//----------------  Monta Dados Recebidos  ---------------->
//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "Agente->".$agt_codigo."<br>";

$titulo="Agendamento por Agente de Saude";    //       NOME DO RELATÓRIO

if ($agt_codigo) {
    $sql = "SELECT agente.agt_codigo, agente.agt_responsavel, agente.agt_descricao " .
           "  FROM agente " .
           " WHERE agente.agt_codigo = '$agt_codigo'";
    $query=pg_query($sql);
    while($row=pg_fetch_row($query)) {
          $Agente=$row[1]."&nbsp; - ".$row[2];
    }
} else {  $Agente = "TODOS";  }


//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtIni, $dtFin, $Agen, $tpCab) {

        //---------  Cabeçalho do Relatorio  ----------------->

        if ($tpCab == 1) {
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0 align=center>
	 	           <tr>
	     	        <td width=130><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=3 face=courier>".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>PERIODO: $dtIni A $dtFin</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=3 face=courier>AGENTE: $Agen</font></td>
 	    	       </tr>
 	    	       <tr>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
	    	       </tr>
 	              </table>";

 	    echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";

 	}
        //---------  Cabeçalho dos Dados  ----------------->

        if ($tpCab == 0) {
            echo " <tr>\n";
            echo "  <td width=250 colspan=2 style=\"font-weight:bold\">M&eacute;dico</td>\n";
            echo "  <td width=10 </td>\n";
            echo "  <td width=250 colspan=1 style=\"font-weight:bold\">Paciente</td>\n";
            echo "  <td width=250 colspan=1 style=\"font-weight:bold\">Data Atend.</td>\n";
            echo "  <td width=250 colspan=1 style=\"font-weight:bold\">Data Agendam.</td>\n";
            echo "  <td width=250 colspan=1 style=\"font-weight:bold\">N. SUS</td>\n";
            echo " </tr>\n";
        }
}

//----------------  Rotina de Impressão  ------------------>

$lin=999;

$sql = "SELECT A.agt_codigo, B.agt_responsavel, B.agt_descricao, C.med_nome, A.usu_codigo,
               D.usu_nome, A.med_codigo, D.usu_prontuario, to_char(A.age_data, 'dd/mm/yyyy'), to_char(A.dt_cadastro, 'dd/mm/yyyy')
        FROM agendamento A, agente B, medico C , usuario D
        WHERE A.agt_codigo = B.agt_codigo
        AND A.med_codigo = C.med_codigo
        AND A.usu_codigo = D.usu_codigo
       ";
if ($agt_codigo) {
    $sql .= "   AND A.agt_codigo = $agt_codigo ";
}

$sql .= "   AND to_date(to_char(a.dt_cadastro, 'dd/mm/yyyy'), 'dd/mm/yyyy') >= '$dt_inicial' and to_date(to_char(a.dt_cadastro, 'dd/mm/yyyy'), 'dd/mm/yyyy') <= '$dt_final' " .
"ORDER BY  C.med_nome, A.dt_cadastro, D.usu_nome, B.agt_responsavel ";

//vSQL($sql,"1");
$query=pg_query($sql);
if (pg_num_rows($query) == 0) {
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "Data INICIAL->".$dt_inicial."<br>";
    echo "Data FINAL  ->".$dt_final."<br>";
    echo "AGENTE      ->".$agt_codigo."<br>";
}
else {
    while($row=pg_fetch_row($query)) {
      if ($lin == 999) {
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $Agente, '1');
          }
          $lin=0;
      }
      if ($responsavel != $row[1]) {
         if ($dia) {
             echo "<tr>\n";
             echo "  <td colspan=4>&nbsp;</td>\n";
             echo "</tr>\n";
             $lin++;
         }
         echo "<tr>\n";
         echo "  <td colspan=4 style=\"font-weight:bold\"><I>*** Agente - $row[1] - $row[2]</I></td>\n";   // Quebra Agente
         echo "</tr>\n";
         $lin++;
         cabeca($titulo, $dt_inicial, $dt_final, $Agente, '0');

         $responsavel=$row[1];
      }
      echo " <tr>\n";

      if ($medico != $row[6]) {
          echo "<tr> <td> &nbsp; </td> </tr>";
          echo " <td width=20%>$row[3]</td>\n";
          $medico=$row[6];
      } else {
          echo "  <td width=20%>       </td>\n";
      }

      echo "  <td width= 5% align = right>$row[4]</td>\n";
      echo "  <td width= 1%> </td>\n";
      echo "  <td width=35%>    $row[5]</td>\n";
      echo "  <td width=15%>    $row[8]</td>\n";
      echo "  <td width=15%>    $row[9]</td>\n";
      echo "  <td width=10%>    $row[7]</td>\n";
      echo " </tr>\n";
      $tot++;
    }
    if (pg_num_rows($query) > 0) {
        echo " <tr><td>&nbsp;</<td></tr>\n";
        echo " <tr><td>&nbsp;</<td></tr>\n";
        echo " <tr>\n";
        echo "  <td colspan=4><p style=\"text-indent:3em;\">  --> Total $tot Consultas</p></td>\n";
        echo " </tr>\n";
        echo " <tr><td>&nbsp;</<td></tr>\n";
        echo " <tr>\n";
        echo "  <td colspan=4>Consultas retiradas por _______________________________ em ___/___/___</td>\n";
        echo " </tr>\n";
        echo " <tr><td>&nbsp;</<td></tr>\n";
        echo " <tr><td>&nbsp;</<td></tr>\n";
        echo " <tr>\n";
        echo "  <td colspan=4><p style=\"text-indent:2em;\">Assinatura _______________________________ Data  ___/___/___</p></td>\n";
        echo " <tr>\n";
    }
echo "</table>";
}



?>
