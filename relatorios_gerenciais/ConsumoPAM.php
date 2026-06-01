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

$titulo="Consumo Mensal de Produtos";    //       NOME DO RELATÓRIO

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
               <td width=70>Local de Armazenagem:</td>
               <td>
                   <select name=set_codigo class=boxr>";
                   $sql = pg_query("select * from setor where set_estoque = 'S' order by set_nome");
                   while($uni=pg_fetch_array($sql)) {
				   		if($_REQUEST['set_codigo'] == $uni['set_codigo']){
                        	echo "<option value='$uni[set_codigo]' selected> $uni[set_nome]</option>";
						}else{
                        	echo "<option value='$uni[set_codigo]'> $uni[set_nome]</option>";
						
						}
                   }
                   echo "<option value=''> -- TODOS OS LOCAIS DE ARMAZENAMENTO -- </option>";
             echo "</select>
              </td>
        </tr>
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
		    <td width=110>Data Base:</td>
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


//Rotina de calculo da data inicial do relatorio
//Deve coincidir com 6 meses anteriores a data final 
$mes0 = pg_fetch_array(pg_query("select extract(year from to_date('$dt_final', 'dd/mm/yyyy')), extract(month from to_date('$dt_final', 'dd/mm/yyyy'))"));
$mes_inicial  = $mes0[1];
$ano_inicial  = $mes0[0];
$ano6 = $ano_inicial;
$mes6 = $mes_inicial;
$i = 1;
for ($i=1;$i<=5;$i++) {
    $mes_inicial = $mes_inicial - 1;
    if ($mes_inicial == 0) {
        $mes_inicial = 12;
        $ano_inicial = $ano_inicial - 1;
    }
    if ($i == 1) {
       $ano5 = $ano_inicial;
       $mes5 = $mes_inicial;
    }
    if ($i == 2) {
       $ano4 = $ano_inicial;
       $mes4 = $mes_inicial;
    }
    if ($i == 3) {
       $ano3 = $ano_inicial;
       $mes3 = $mes_inicial;
    }
    if ($i == 4) {
       $ano2 = $ano_inicial;
       $mes2 = $mes_inicial;
    }
    if ($i == 5) {
       $ano1 = $ano_inicial;
       $mes1 = $mes_inicial;
    }
}    
$dt_inicial = '01/'.$mes1.'/'.$ano1;

    echo " <br> <br> ";
//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Uni, $Cab, $mes1, $ano1, $mes2, $ano2, $mes3, $ano3, $mes4, $ano4, $mes5, $ano5, $mes6, $ano6) {

//--->        Cabeçalho do Sistema

       if ($Cab == 0) {

          echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>Relatório - ".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>PERIODO: $dtIni a $dtFin </font></td>
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
           echo "  <td width=8%>$mes1/$ano1</td>\n";
           echo "  <td width=8%>$mes2/$ano2</td>\n";
           echo "  <td width=8%>$mes3/$ano3</td>\n";
           echo "  <td width=8%>$mes4/$ano4</td>\n";
           echo "  <td width=8%>$mes5/$ano5</td>\n";
           echo "  <td width=8%>$mes6/$ano6</td>\n";
           echo "  <td width=10%>Total Cons. </td>\n";
           echo "  <td width=10%>M&eacute;dia/M&ecirc;s </td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->

$sql = "SELECT PRO_CODIGO, PRO_NOME, 
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES1 WHERE MES1.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano1 AND EXTRACT(MONTH FROM MOV_DATA) = $mes1) AS CONSMES1,
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES2 WHERE MES2.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano2 AND EXTRACT(MONTH FROM MOV_DATA) = $mes2) AS CONSMES2,
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES3 WHERE MES3.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano3 AND EXTRACT(MONTH FROM MOV_DATA) = $mes3) AS CONSMES3,
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES4 WHERE MES4.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano4 AND EXTRACT(MONTH FROM MOV_DATA) = $mes4) AS CONSMES4,
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES5 WHERE MES5.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano5 AND EXTRACT(MONTH FROM MOV_DATA) = $mes5) AS CONSMES5,
              (SELECT COALESCE(ROUND (CAST (SUM(CONSUMO) AS NUMERIC),0),0) FROM V_CONSUMO AS MES6 WHERE MES6.PRO_CODIGO = CONS.PRO_CODIGO 
               AND EXTRACT(YEAR FROM MOV_DATA) = $ano6 AND EXTRACT(MONTH FROM MOV_DATA) = $mes6) AS CONSMES6
         FROM V_CONSUMO AS CONS
         WHERE MOV_DATA >= '$dt_inicial' AND MOV_DATA <='$dt_final'";

if ($set_codigo)   $sql .= " AND CODSETOR = $set_codigo ";
if ($gru_codigo)   $sql .= " AND GRU_CODIGO = $gru_codigo ";

$sql .= "  GROUP BY PRO_CODIGO, PRO_NOME
           ORDER BY PRO_NOME";

$query=pg_query($sql) or die (pg_last_error());
$lin=999;

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
    echo "</table>\n";
}
else {
      while($row=pg_fetch_row($query)) {

          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0', $mes1, $ano1, $mes2, $ano2, $mes3, $ano3, $mes4, $ano4, $mes5, $ano5, $mes6, $ano6);
              $lin=9;
          }
          echo " <tr align=center>\n";


          $total = $row[2] + $row[3] + $row[4] + $row[5] + $row[6] + $row[7];
          $media = $total / 6;
          $totaled = formata_valor($total);
          $mediaed = formata_valor($media);

          
          echo "  <td>$row[1]</td>\n";
          echo "  <td>$row[2]</td>\n";
          echo "  <td>$row[3]</td>\n";
          echo "  <td>$row[4]</td>\n";
          echo "  <td>$row[5]</td>\n";
          echo "  <td>$row[6]</td>\n";
          echo "  <td>$row[7]</td>\n";
          echo "  <td>$totaled</td>\n";
          echo "  <td>$mediaed</td>\n";
          echo " </tr>\n";

      }
      echo "</table>\n";
}
echo "</table>";
echo "<body>\n";
echo "<body>\n";
?>
