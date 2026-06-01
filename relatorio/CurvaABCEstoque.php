<!-- ---------------------------------------------------------------
       Funções javascript
------------------------------------------------------------------ -->

<SCRIPT Language="Javascript">

function imprimir() {
       window.print();
}
</script>

<!-- <body onload='imprimir()'> -->

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
if ($ConsVal ==1) { $OP=" Classificado Por Consumo"; } else { $OP=" Classificado Por Valor"; }

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
	     	        <td width=100><font size=1 face=courier>WebSocialSaude</font></td>
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

// $sql.=" AND v_movimentacao.mov_data between '$dt_inicial' and '$dt_final'  " ;
// if ($gru_codigo) { $sql.=" AND produto.gru_codigo = $gru_codigo " ;             }

 while ($rowTotal=pg_fetch_row($queryTotal)) {
     $val = $val + $rowTotal[0];
  }

 $TotValItens=$val;


$row=pg_fetch_row(pg_query("delete from CurvaABC")); // or die (pg_last_error());


$sql = "select pro_codigo as CodProd, (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as ENTRADAS
	       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
	       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
	       AND   MOV.SET_ENTRADA = '$CE_codigo'
	       AND   (MOV_TIPO = 'E' OR MOV_TIPO = 'T')
	       AND   IT.PRO_CODIGO = p.pro_codigo
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') - (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as SAIDAS
       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
       AND   (MOV_TIPO = 'S')
       AND   IT.PRO_CODIGO =  p.pro_codigo   
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final'),
      (select max(ite_vlrunit) from itens_movimento where pro_codigo = p.pro_codigo),
	pro_nome,
     COALESCE((SELECT COALESCE(SUM(ITE_QUANTIDADE),0)
	       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
	       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
	       AND   MOV.SET_ENTRADA = '$CE_codigo'
	       AND   (MOV_TIPO = 'E' OR MOV_TIPO = 'T')
	       AND   IT.PRO_CODIGO = p.pro_codigo
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') - (SELECT COALESCE(SUM(ITE_QUANTIDADE),0)
       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
       AND   (MOV_TIPO = 'S')
       AND   IT.PRO_CODIGO =  p.pro_codigo   
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final'))*COALESCE((select max(ite_vlrunit) from itens_movimento where pro_codigo = p.pro_codigo))
	         from produto as p";

if ($gru_codigo) 
   { 
      $sql.=" WHERE p.gru_codigo = $gru_codigo " ; 
      $sql.=" AND    p.gru_codigo = 99482 AND
	        (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as ENTRADAS
	       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
	       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
	       AND   MOV.SET_ENTRADA = '$CE_codigo'
	       AND   (MOV_TIPO = 'E' OR MOV_TIPO = 'T')
	       AND   IT.PRO_CODIGO = p.pro_codigo
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') - (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as SAIDAS
       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
       AND   (MOV_TIPO = 'S')
       AND   IT.PRO_CODIGO =  p.pro_codigo   
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') > 0 "; 
   }
else    {
      $sql.=" WHERE    p.gru_codigo = 99482 AND
	        (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as ENTRADAS
	       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
	       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
	       AND   MOV.SET_ENTRADA = '$CE_codigo'
	       AND   (MOV_TIPO = 'E' OR MOV_TIPO = 'T')
	       AND   IT.PRO_CODIGO = p.pro_codigo
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') - (SELECT COALESCE(SUM(ITE_QUANTIDADE),0) as SAIDAS
       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
       AND   (MOV_TIPO = 'S')
       AND   IT.PRO_CODIGO =  p.pro_codigo   
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') > 0 ";
   }   
$sql.=" order by COALESCE((SELECT COALESCE(SUM(ITE_QUANTIDADE),0)
	       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
	       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
	       AND   MOV.SET_ENTRADA = '$CE_codigo'
	       AND   (MOV_TIPO = 'E' OR MOV_TIPO = 'T')
	       AND   IT.PRO_CODIGO = p.pro_codigo
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final') - (SELECT COALESCE(SUM(ITE_QUANTIDADE),0)
       FROM MOVIMENTO AS MOV, ITENS_MOVIMENTO AS IT
       WHERE MOV.MOV_CODIGO = IT.MOV_CODIGO
       AND   (MOV_TIPO = 'S')
       AND   IT.PRO_CODIGO =  p.pro_codigo   
	       AND   MOV_DATA >= '01/01/2000'
	       AND   MOV_DATA <= '$dt_final'))*COALESCE((select max(ite_vlrunit) from itens_movimento where pro_codigo = p.pro_codigo)) desc ";
//$sql .= "limit 10";
$sqlCarga = $sql;

$query = pg_query($sql);
//echo $sql;
$total = 0;
while ($row=pg_fetch_row($query)) {
     $total = $total + ($row[1] * $row[2]);
}     
$TotValItens = $total;

//echo $sqlCarga;
$queryCarga=pg_query($sqlCarga);

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
$A=round(($total * $CurvA)/100);
$B=round(($total * ($CurvA + $CurvB))/100);
$C=round(($total * ($CurvA + $CurvB + $CurvC))/100);

$acum = 0;

$lin = 999;  $AT= $BT = $CT = 0;

// Zebragem
$controle = 0;

while($row=pg_fetch_row($queryCarga)) {
      if ($lin== 999) {
          cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $OP, $CurvA, $CurvB, $CurvC, '0');
          $lin=9;
      }
      $valoracum = $valoracum + $row[4];

      if ($valoracum < $A) {$Faixa="A"; $AT++; } else
      if ($valoracum < $B) {$Faixa="B"; $BT++; } else
      if ($valoracum > $B)   {$Faixa="C"; $CT++; }

      if ($Faixa=="A" && $AT>1) { $Faixa=""; } else
      if ($Faixa=="B" && $BT>1) { $Faixa=""; } else
      if ($Faixa=="C" && $CT>1) { $Faixa=""; }

      $qtde = formata_valor0($row[1]);
		
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
      echo "  <td>".substr($row[3],0,40).                        "</td>\n";
      echo "  <td align=right>". $qtde .         "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      $vlr = formata_valor($row[4]);
      echo "  <td align=right>".($row[2]>0 ? "$row[2]" : " ").   "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>". $vlr.   "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      $perc = $row[4] / $total * 100;
      $percfmt = formata_valor4($perc);
      echo "  <td align=right>".$percfmt."</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      $acum = $valoracum / $total * 100;
      $acumfmt = formata_valor4($acum);
      echo "  <td align=right>".$acumfmt."</td>\n";
      echo "  <td>&nbsp;                  </td>\n";
      echo " </tr>\n";
      $lin++;
}
      $vlr = formata_valor($total);
      echo " <tr>\n";
      echo "  <td style=\"font-weight:bold\" align=center>&nbsp;  </td>\n";
      echo "  <td>     Total Geral                                </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td align=right>". $vlr.   "</td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
//      echo "  <td align=right>".($vait>0 ? "$vait" : " ").       "</td>\n";
//      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
      echo "  <td>&nbsp;                                          </td>\n";
//      echo "  <td align=right>".($row[6]>0 ? "$row[6]" : " ").   "</td>\n";
//      echo "  <td>&nbsp;                  </td>\n";
      echo "  <td>&nbsp;                  </td>\n";
      echo " </tr>\n";

echo "</table>";
echo "</body>";
?>
