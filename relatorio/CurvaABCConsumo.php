<!-- ---------------------------------------------------------------
       Funções javascript
------------------------------------------------------------------ -->

<SCRIPT Language="Javascript">

function imprimir() {
       window.print();
}
</script>

<body onload='imprimir()'>

<?php

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>

function func_PrMed($procodigo, $codsetor, $dta) {

     $PrMed=pg_fetch_array(pg_query("select prm_custo
                                        from precomedio
                                       where pro_codigo=$procodigo
                                         and set_codigo=$codsetor
                                         and prm_data<='$dta'
                                    order by prm_data desc limit 1"));
      return $PrMed[prm_custo];
}

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "CentroEstoq->".$CE_codigo."<br>";
//echo "Setor  ->".$set_codigo."<br>";
//echo "Grupo  ->".$gru_codigo."<br>";
//echo "ConsVal->".$ConsVal."<br>";
//echo "CurvaA->".$CurvA."<br>";
//echo "CurvaB->".$CurvB."<br>";
//echo "CurvaC->".$CurvC."<br>";


$titulo="CURVA ABC DE CONSUMO";    //       NOME DO RELATÓRIO
if ($ConsVal ==0) { $OP=" Classificado Por Consumo"; } else { $OP=" Classificado Por Valor"; }

if ($CE_codigo) {
    $sql = "SELECT setor.set_nome 
              FROM setor
             WHERE setor.set_estoque = 'S'
               AND setor.set_codigo = $CE_codigo";
    $row=pg_fetch_row(pg_query($sql)); 
    $CENome=$row[0];
} else {  $CENome = "TODOS";  }
if ($set_codigo) {
    $sql = "SELECT setor.set_nome 
              FROM setor                      
             WHERE setor.set_codigo = $set_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $SetorNome=$row[0];
} else {  $SetorNome = "TODOS";  }
if ($gru_codigo) {
    $sql = "SELECT grupo.gru_nome
              FROM grupo
             WHERE grupo.gru_codigo = $gru_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $GrupoNome=$row[0];
} else {  $GrupoNome = "TODOS";  }

//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $CE, $Setor, $Grupo, $Opcao, $PA, $PB, $PC, $Cab) {

//--->        Cabeçalho do Sistema

        if ($Cab == 0) {
            echo "<table width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	           <tr>
	     	        <td width=100><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=1 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier> $Tit - &nbsp;&nbsp;&nbsp;$Opcao&nbsp;&nbsp;&nbsp;$PA%&nbsp;$PB%&nbsp;$PC%   </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>PERIODO: $dtIni A $dtFin</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>Centro Estocador: $CE </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>Setor: $Setor </font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=1 face=courier>Grupo: $Grupo </font></td>
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
           echo " <tr  style=\"font-weight:bold\">\n";
           echo "  <td width= 5%>Faixa       </td>\n";
           echo "  <td width=40%>Produto     </td>\n";
           echo "  <td width= 5% align=right>Qtde                  </td>\n";
           echo "  <td width= 5%>&nbsp;                </td>\n";
           echo "  <td width= 5% align=right>Pr.Medio              </td>\n";
           echo "  <td width= 5%>&nbsp;                </td>\n";
           echo "  <td width= 7% align=right>Valor                 </td>\n";
           echo "  <td width= 5%>&nbsp;                </td>\n";
           echo "  <td width= 6% align=right>% Cons                </td>\n";
           echo "  <td width= 5%>&nbsp;                </td>\n";
           echo "  <td width= 7% align=right>% Acum                </td>\n";
           echo "  <td width= 5%>&nbsp;                </td>\n";
           echo " </tr>\n";
        }
}

//----------------  Rotina de Impressão  ---------------->

if ($set_codigo) {
    $LclPrMedio=$set_codigo;
} else {
    $row=pg_fetch_row(pg_query("select * from conf_estoque"));
    $LclPrMedio=$row[1];
}

/*      Aqui pega o 100 % Quantidade Consumo ou Valor Consumido     */
////         arrumar ou volta para 0 aqui no teste   

if ($ConsVal == 0) {
    $sql="select sum(ite_quantidade)
            FROM v_movimentacao, produto
           WHERE v_movimentacao.pro_codigo = produto.pro_codigo
             AND v_movimentacao.sinal='-' ";
    $sql.="  AND v_movimentacao.mov_data between '$dt_inicial' and '$dt_final'  " ;
    if ($CE_codigo ) { $sql.=" AND v_movimentacao.codsetor = $CE_codigo  " ;        } 
    if ($set_codigo) { $sql.=" AND v_movimentacao.codsetorsolicit = $set_codigo " ; } 
    if ($gru_codigo) { $sql.=" AND produto.gru_codigo = $gru_codigo " ;             }
    $qtdRow=pg_fetch_row(pg_query($sql));
    $sumQtd=$qtdRow[0];
    $TotValItens=$qtdRow[0];
} else {
    $sql="SELECT sum(coalesce(Ult_PrMed(v_movimentacao.pro_codigo,codsetor,mov_data),0) * ite_quantidade)
            FROM v_movimentacao, produto
           WHERE v_movimentacao.pro_codigo = produto.pro_codigo
             AND v_movimentacao.sinal='-'";
    $sql.=" AND v_movimentacao.mov_data between '$dt_inicial' and '$dt_final'  " ;
    if ($CE_codigo ) { $sql.=" AND v_movimentacao.codsetor = $CE_codigo  " ;        } 
    if ($set_codigo) { $sql.=" AND v_movimentacao.codsetorsolicit = $set_codigo " ; } 
    if ($gru_codigo) { $sql.=" AND produto.gru_codigo = $gru_codigo " ;             }
    $valRow=pg_fetch_row(pg_query($sql)); 
    $TotValItens=$valRow[0];
}
$row=pg_fetch_row(pg_query("delete from CurvaABC")); // or die (pg_last_error());

$sql="SELECT v_movimentacao.pro_codigo as CodProd , 
             sum(ite_quantidade) as Qtd , 
             coalesce(Ult_PrMed(v_movimentacao.pro_codigo,codsetor,mov_data),0) as PrMed , 
             sum(coalesce(Ult_PrMed(v_movimentacao.pro_codigo,codsetor,mov_data),0) * ite_quantidade)
                 as ValTot ,
             0 as ValAcum , 
             ((sum(ite_quantidade)*100)/$TotValItens) as PercVal ,
             0 as PercAcum , 
             produto.pro_nome  
        FROM v_movimentacao, produto 
       WHERE v_movimentacao.pro_codigo = produto.pro_codigo 
         AND v_movimentacao.sinal = '-' ";
$sql.="  AND v_movimentacao.mov_data between '$dt_inicial' and '$dt_final'  " ;
if ($CE_codigo ) { $sql.=" AND v_movimentacao.codsetor = $CE_codigo  " ;        } 
if ($set_codigo) { $sql.=" AND v_movimentacao.codsetorsolicit = $set_codigo " ; } 
if ($gru_codigo) { $sql.=" AND produto.gru_codigo = $gru_codigo " ;               }
$sql.=" GROUP BY v_movimentacao.pro_codigo, v_movimentacao.codsetor ,
                 v_movimentacao.mov_data  , v_movimentacao.ite_quantidade , 
                 produto.pro_nome ";
//vSQL($sql,1);  exit();

$queryCarga=pg_query($sql);

if (pg_num_rows($queryCarga) == 0) {
    echo "<table width=100% cellspacing=0 cellpadding=3 border=0 align=center>";
    echo " <div><div style='padding-top:122px;'>";
    echo "  <tr><td colspan=2 align=center>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>";
    echo "  <tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2>&nbsp;</td></tr>";
    echo "  <tr><td align=right width=50%>".str_pad('INICIAL', 15  , '___')."</td><td width=50%>$dt_inicial </td></tr>";
    echo "  <tr><td align=right width=50%>".str_pad('FINAL', 15  , '___')."</td><td width=50%>$dt_final     </td></tr>";
    echo "  <tr><td align=right width=50%>".str_pad('CentroEstoq', 16  , '___')."</td><td width=50%>$CENome </td></tr>";
    echo "  <tr><td align=right width=50%>".str_pad('Setor', 16  , '___')."</td><td width=50%>$SetorNome    </td></tr>";
    echo "  <tr><td align=right width=50%>".str_pad('Grupo', 15  , '___')."</td><td width=50%>$GrupoNome    </td></tr>";
    echo " </div>";
    echo "</table>";
}
$CodProd = $SumQTD = $ValAcum = $PercAcum = 0;
                                                   //0-CodProd  1-Qtd  2-PrMed  3-ValTot  4-ValAcum  5-PercVal  6-PercAcum
// Zebragem
$controle = 0;

while ($rowCarga=pg_fetch_row($queryCarga)) {
       if ($CodProd!=$rowCarga[0] && $CodProd>0) {
           $ValTot   =$SumQTD*$PrecoMed;
           $ValAcum +=$SumQTD*$PrecoMed;
           $PercVal  =($ValTot*100)/$TotValItens; 
           $PercAcum+=$PercVal;
           $sql=pg_query("INSERT INTO CurvaABC  (
                             abc_procod, abc_qtd, abc_prmed, abc_valtot, abc_valacum, abc_percval, abc_percacum, abc_pronome 
                             ) values ( " . 
                              $CodProd . " , " . $SumQTD   . " , " . $PrecoMed . " , " . $ValTot . " , " . $ValAcum . " , " .
                              $PercVal . " , " . $PercAcum . " , " . "'$Nome'" . ")");
           $ValAcum = $PercVal = $SumQTD = 0;
       }
       $SumQTD  +=$rowCarga[1];
       $PrecoMed =$rowCarga[2];
       $Nome     =$rowCarga[7];
       $CodProd  =$rowCarga[0];
}
     /*       Grava Último     */ 

$ValTot   =$SumQTD*$PrecoMed;
$ValAcum +=$SumQTD*$PrecoMed;
$PercVal  =($ValTot*100)/$TotValItens;
$PercAcum+=$PercVal;

$sql = pg_query("INSERT INTO CurvaABC 
                           ( abc_procod, abc_qtd, abc_prmed, abc_valtot, abc_valacum, abc_percval, abc_percacum, abc_pronome 
                           ) values ( " . 
                             $CodProd . " , " . $SumQTD   . " , " . $PrecoMed . " , " . $ValTot . " , " . $ValAcum . " , " .
                             $PercVal . " , " . $PercAcum . " , " . "'$Nome'" . ")");

$sql ="select * from CurvaABC order by ";
if ($ConsVal == 0)  { $sql.=" abc_qtd desc"; }   else   { $sql.=" abc_valtot desc"; }
 
$queryCurvaABC=pg_query($sql); 
                                                    //0-CodProd  1-Qtd  2-PrMed  3-ValTot  4-ValAcum  5-PercVal  6-PercAcum
$QtdItens=pg_num_rows($queryCurvaABC);
$A=round(($CurvA*$QtdItens)/100);
$B=round(($CurvB*$QtdItens)/100);
$C=round(($CurvC*$QtdItens)/100);

$lin = 999;  $AT= $BT = $CT = 0;

while($row=pg_fetch_row($queryCurvaABC)) {
      if ($lin== 999) {
          cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $OP, $CurvA, $CurvB, $CurvC, '0');
          $lin=9;
      }
      if ($A>0) { $A--; $Faixa="A"; $AT++; } else 
      if ($B>0) { $B--; $Faixa="B"; $BT++; } else 
      if ($C>0) {       $Faixa="C"; $CT++; }
       
      if ($Faixa=="A" && $AT>1) { $Faixa=""; } else 
      if ($Faixa=="B" && $BT>1) { $Faixa=""; } else
      if ($Faixa=="C" && $CT>1) { $Faixa=""; }
      
      $sep_valor=explode(".",$row[1]);
      if (strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]0"; } else
      if ($sep_valor[1]>0)            { $zero_2=substr($sep_valor[1],0,2); }
      if ($sep_valor[1]>0) {
         $zero_2=substr($sep_valor[1],0,2); 
         $QTD=$sep_valor[0].$zero_2; 
      } else { 
         $QTD=$sep_valor[0]; 
      }
      $aux=explode(".",$row[3]);
      $vait=$aux[0].",".substr($aux[1],0,2);
	  
		$c1 = "";
		$c2 = "#F2F2F2";
		
		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		} else {
		  $cor = $c2;
		  $controle = 0;
		}
		
      echo " <tr bgcolor='$cor'>\n";
      echo "  <td style=\"font-weight:bold\" align=center>$Faixa  </td>\n";
      echo "  <td>".substr($row[7],0,40).                        "</td>\n";
      echo "  <td align=right>".($QTD>0 ? "$QTD" : "0").         "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>".($row[2]>0 ? "$row[2]" : " ").   "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>".($vait>0 ? "$vait" : " ").       "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>".($row[4]>0 ? "$row[4]" : " ").   "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>".($row[5]>0 ? "$row[5]" : " ").   "</td>\n";
      echo "  <td>&nbsp;                  </td>\n";
      echo " </tr>\n";
      $lin++;
}

echo "</table>";
echo "</body>";
?>
