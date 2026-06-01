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

$titulo="Posicao Financeira de Estoque";    //       NOME DO RELATÓRIO

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
		    <td width=50>Data Base:</td>
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
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	       </tr>
 	              </table>";
 	    echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	    }

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold' align=center>\n";
           echo "  <td width=40%>Produto </td>\n";
           echo "  <td width=20%>Estoque Atual</td>\n";
           echo "  <td width=20%>Preco Medio</td>\n";
           echo "  <td width=20%>Total Atual</td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->
if ($set_codigo) {
    $sql = "SELECT setor.set_codigo as codsetor, set_nome as nomesetor, produto.pro_codigo, produto.pro_nome, 
            grupo.gru_codigo, gru_nome,
            COALESCE(ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, '$set_codigo', to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 0),0),
               COALESCE(ROUND( CAST (VERIFICA_PRECO(PRODUTO.PRO_CODIGO, '$set_codigo', to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 4),0),
               COALESCE(ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, '$set_codigo', to_date('$dt_final', 'dd/mm/yyyy'))  *
                      VERIFICA_PRECO(PRODUTO.PRO_CODIGO, '$set_codigo', to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 2),0)
	FROM   PRODUTO, PRODUTO_SETOR, SETOR, GRUPO
	WHERE  PRODUTO.PRO_CODIGO = PRODUTO_SETOR.PRO_CODIGO
	AND    PRODUTO_SETOR.SET_CODIGO = SETOR.SET_CODIGO
        AND    PRODUTO.GRU_CODIGO = GRUPO.GRU_CODIGO";
}
else {
    $sql = "SELECT setor.set_codigo as codsetor, set_nome as nomesetor, produto.pro_codigo, produto.pro_nome, 
            grupo.gru_codigo, gru_nome,
               COALESCE(ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 0),0),
               COALESCE(ROUND( CAST (VERIFICA_PRECO(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 4),0),
               COALESCE(ROUND( CAST (CALCULA_ESTOQUE(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy'))  *
                      VERIFICA_PRECO(PRODUTO.PRO_CODIGO, 0, to_date('$dt_final', 'dd/mm/yyyy')) AS NUMERIC), 2),0)
	FROM   PRODUTO, PRODUTO_SETOR, SETOR, GRUPO
	WHERE  PRODUTO.PRO_CODIGO = PRODUTO_SETOR.PRO_CODIGO
	AND    PRODUTO_SETOR.SET_CODIGO = SETOR.SET_CODIGO
        AND    PRODUTO.GRU_CODIGO = GRUPO.GRU_CODIGO";
}
if ($set_codigo)   $sql .= " AND  SETOR.set_codigo= $set_codigo ";
if ($gru_codigo)   $sql .= " AND grupo.GRU_CODIGO = $gru_codigo ";

$sql .= "  ORDER BY PRO_NOME";

echo $sql;
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
      $total = 0;
      while($row=pg_fetch_row($query)) {

          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0');
              $lin=9;
          }
          echo " <tr align=center>\n";

          echo "  <td>$row[3]</td>\n";
          echo "  <td>$row[6]</td>\n";
          echo "  <td>$row[7]</td>\n";
          echo "  <td>$row[8]</td>\n";
          echo " </tr>\n";

          $total = $total + $row[8];


      }
      echo "</table>\n";

      $totaled = formata_valor($total);
      echo "<table style=\"font-size:14px;font-family:courier,vardana,arial;font-weight:bold;\"  width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
      echo " <tr align=center>\n";
      echo "  <td width=40%> TOTAL </td>\n";
      echo "  <td width=20%>&nbsp;</td>\n";
      echo "  <td width=20%>&nbsp;</td>\n";
      echo "  <td width=20%>$totaled</td>\n";
      echo " </tr>\n";


}
echo "</table>";
echo "<body>\n";
echo "<body>\n";
?>
