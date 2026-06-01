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

$titulo="Estoque de Produto por Habitante";    //       NOME DO RELATÓRIO

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

if (!$dt_final) {
    $datafinal = pg_fetch_array(pg_query("select to_char(date(now()), 'dd/mm/yyyy')"));
    $dt_final = $datafinal[0];
}

echo "
  <form method=post action=$PHP_SELF>
	<input type=hidden name=user value=$user>
	<fieldset>
	    <legend>Dados do Relatorio</legend>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
    <tr>
       <td width=70>Grupo de Produto:</td>
       <td>
                <select name=gru_codigo class=boxr>";
                   $sql = pg_query("select * from grupo order by gru_nome");
                   echo "<option value=''> -- TODOS OS GRUPOS -- </option>";
                   while($uni=pg_fetch_array($sql)) {
      	   		 if($_REQUEST['gru_codigo'] == $uni['gru_codigo']){
                        	echo "<option value='$uni[gru_codigo]' selected> $uni[gru_nome]</option>";
						}else{
                        	echo "<option value='$uni[gru_codigo]'> $uni[gru_nome]</option>";
			}
                   }
             echo "</select>
              </td>
        </tr>

	     <tr>
		    <td width=30>Data Base:</td>
		    <td>
			<input type=text name=dt_final class=box size=13 value=$dt_final>
			</td>
	     </tr>";
echo "      <tr> <td> &nbsp; </td>
                <td > <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg> </td>\n";
echo "      <td > <a href=index.php?user=$user><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";


echo"
        </table>
        </fieldset>
        </form>";


    echo " <br> <br> ";
//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Uni, $Cab) {

//--->        Cabeçalho do Sistema

       if ($Cab == 0) {

          echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> ".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>Data: $dtFin </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>Cidade: Apucarana    -   Habitantes: 117.260 (Estimativa Populacional para 2006 - IPARDES)</font></td>
 	    	       </tr>
 	    	       <tr>
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	       </tr>
 	              </table>";
 	    echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	    }

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold' align=center>\n";
           echo "  <td width=32%>Produto </td>\n";
           echo "  <td width=8%>Estoque Atual</td>\n";
           echo "  <td width=8%>Estoque/Habitante</td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->

$sql = "SELECT GRUPO.GRU_CODIGO, GRU_NOME, PRODUTO.PRO_CODIGO, PRODUTO.PRO_NOME,
               ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 0),
               ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) / 117260 AS NUMERIC), 6)
        FROM   PRODUTO, GRUPO
        WHERE  PRODUTO.GRU_CODIGO = GRUPO.GRU_CODIGO
        AND    CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) > 0
";

if ($gru_codigo)   $sql .= " AND grupo.GRU_CODIGO = $gru_codigo ";

$sql .= "  ORDER BY PRO_NOME";

$query=pg_query($sql) or die (pg_last_error());
$lin=999;

if (pg_num_rows($query) == 0) {
    echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=70% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
    echo "  <tr><td align=center colspan=5>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>\n";
    echo "  <tr><td align=right  colspan=5>&nbsp;</td></tr>\n";
    echo "  <tr><td align=right  width=25%></td>\n";
    echo "      <td align=right  width=20%>Data </td>\n";
    echo "      <td align=left>$dt_final</td><td>&nbsp;</td></tr>\n";

    echo "  <tr><td align=right></td>\n";
    echo "</table>\n";
}
else {
      while($row=pg_fetch_row($query)) {

          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0', $mes1, $ano1, $mes2, $ano2, $mes3, $ano3, $mes4, $ano4, $mes5, $ano5, $mes6, $ano6);
              $lin=9;
          }
          echo " <tr align=center>\n";


          echo "  <td>$row[3]</td>\n";
          echo "  <td>$row[4]</td>\n";
          echo "  <td>$row[5]</td>\n";
          echo " </tr>\n";

      }
      echo "</table>\n";
}

echo "</table>";
echo "<body>\n";
echo "<body>\n";
?>
