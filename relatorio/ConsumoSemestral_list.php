<SCRIPT Language="Javascript">
	function imprimir()
	{
		window.print() ;
	}
</script>
<?php

function inv_data($dat) {
	$d=explode("-",$dat);
	$dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
	return "$dat";
}

// Zebragem
$controle = 0;

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
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

$meses++;

if( $meses > 1 )
{
	$ano1 = substr($dt_inicial,6,4);
	$mes1 = substr($dt_inicial,3,2);
}
if( $meses > 2 )
{
	$mes2 = $mes1+1;
	$ano2 = $ano1;
	if( $mes2 > 12 )
	{
		$ano2++;
		$mes2 -= 12;
		if( $mes2 < 10 )
		{
			$mes2 = "0".$mes2;
		}
	}
}
if( $meses > 3 )
{
	$mes3 = $mes2+1;
	$ano3 = $ano2;
	if( $mes3 > 12 )
	{
		$ano3++;
		$mes3 -= 12;
		if( $mes3 < 10 )
		{
			$mes3 = "0".$mes3;
		}
	}
}
if( $meses > 4 )
{
	$mes4 = $mes3+1;
	$ano4 = $ano3;
	if( $mes4 > 12 )
	{
		$ano4++;
		$mes4 -= 12;
		if( $mes4 < 10 )
		{
			$mes4 = "0".$mes4;
		}
	}
}
if(  $meses > 5 )
{
	$mes5 = $mes4+1;
	$ano5 = $ano4;
	if( $mes5 > 12 )
	{
		$ano5++;
		$mes5 -= 12;
	}
}
$ano6 = substr($dt_final,6,4);
$mes6 = substr($dt_final,3,2);

//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Uni, $Cab, $mes1, $ano1, $mes2, $ano2, $mes3, $ano3, $mes4, $ano4, $mes5, $ano5, $mes6, $ano6, $tipo, $tipo_mov) {

//--->        Cabeçalho do Sistema

	if ($Cab == 0) 
	{
		include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

		echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=1 border=1 topmargin=0 leftmargin=0>\n";
	}

//--->        Cabeçalho dos Dados
	if ($Cab == 0)
	{
		echo " <tr style='font-weight:bold' align=center>\n";
		echo "  <td width=30%>Produto ".$tipo_mov."</td>\n";
		//echo "  <td width=12%>Tipo </td>\n";
		if( $mes1 )
		{
			echo "  <td width=8%>$mes1/$ano1</td>\n";
		}
		if( $mes2 )
		{
			echo "  <td width=8%>".( $mes2 < 10 && $mes2 > 1 ? "0" : "" )."$mes2/$ano2</td>\n";
		}
		if( $mes3 )
		{
			echo "  <td width=8%>".( $mes3 < 10 && $mes3 > 1 ? "0" : "" )."$mes3/$ano3</td>\n";
		}
		if( $mes4 )
		{
			echo "  <td width=8%>".( $mes4 < 10 && $mes4 > 1 ? "0" : "" )."$mes4/$ano4</td>\n";
		}
		if( $mes5 )
		{
			echo "  <td width=8%>".( $mes5 < 10 && $mes5 > 1 ? "0" : "" )."$mes5/$ano5</td>\n";
		}
		if( $mes6 )
		{
			echo "  <td width=8%>$mes6/$ano6</td>\n";
		}
			echo "  <td width=10%>Total Cons. </td>\n";
			echo "  <td width=10%>M&eacute;dia/M&ecirc;s </td>\n";
			echo "  <td width=10%>Saldo Atual </td>\n";
			echo " </tr>\n";
        }
}

//----------------  Rotina de Impressão  ---------------->
switch ($tipomovim):
	case 'A': $tipo_mov = "(Ajuste)";
		break;
	case 'M': $tipo_mov = "(Empr&eacute;stimo)";
		break;
	case 'I': $tipo_mov = "(Invent&aacute;rio)";
		break;
	case 'R': $tipo_mov = "(Perdas)";
		break;
	case 'P': $tipo_mov = "(Permuta)";
		break;
	case 'S': $tipo_mov = "(Sa&iacute;da de Consumo)";
		break;
	case 'T': $tipo_mov = "(Transfer&ecirc;ncia)";
		break;
	case 'O': $tipo_mov = "(Outras Sa&iacute;das)";
		break;
	case 'ST': $tipo_mov = "(Sa&iacute;da de Consumo + Transfer&ecirc;ncia)";
		break;
	default : $tipo_mov = "";
endswitch;
if( ! empty($tipomovim) )
{
	if ($tipomovim == 'ST'){
		$tipomovim = " AND tipomovim in ('S', 'T')";
	}else{
		$tipomovim = " AND tipomovim = '$tipomovim'";
	}
	$tipomovim .= " AND sinal = '-' ";
}
else
{
	$tipomovim = "";
}
$sql = "SELECT CONS.PRO_CODIGO, CONS.PRO_NOME, ";

if( $meses > 1 )
{
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES1
	WHERE MES1.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES1.MOV_DATA) = $ano1
	AND EXTRACT(MONTH FROM MES1.MOV_DATA) = $mes1
    ";
    if ($set_codigo)   $sql .= " AND MES1.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES1.GRU_CODIGO = $gru_codigo ";
$sql .=" $tipomovim
	) AS CONSMES1, ";
}
if( $meses > 2 )
{
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES2
	WHERE MES2.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES2.MOV_DATA) = $ano2
	AND EXTRACT(MONTH FROM MES2.MOV_DATA) = ".( $mes2 < 10 ? "0" : "" )."$mes2
    ";
    if ($set_codigo)   $sql .= " AND MES2.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES2.GRU_CODIGO = $gru_codigo ";
$sql .="
	$tipomovim
	) AS CONSMES2, ";
}
if( $meses > 3 )
{
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES3
	WHERE MES3.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES3.MOV_DATA) = $ano3
	AND EXTRACT(MONTH FROM MES3.MOV_DATA) = ".( $mes3 < 10 ? "0" : "" )."$mes3
    ";
    if ($set_codigo)   $sql .= " AND MES3.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES3.GRU_CODIGO = $gru_codigo ";
$sql .="
	$tipomovim
	) AS CONSMES3, ";
}
if( $meses > 4 )
{
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES4
	WHERE MES4.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES4.MOV_DATA) = $ano4
	AND EXTRACT(MONTH FROM MES4.MOV_DATA) = ".( $mes4 < 10 ? "0" : "" )."$mes4
    ";
    if ($set_codigo)   $sql .= " AND MES4.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES4.GRU_CODIGO = $gru_codigo ";
$sql .="
	$tipomovim
	) AS CONSMES4, ";
}
if( $meses > 5 )
{
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES5
	WHERE MES5.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES5.MOV_DATA) = $ano5
	AND EXTRACT(MONTH FROM MES5.MOV_DATA) = ".( $mes5 < 10 ? "0" : "" )."$mes5
    ";
    if ($set_codigo)   $sql .= " AND MES5.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES5.GRU_CODIGO = $gru_codigo ";
$sql .="
	$tipomovim
	) AS CONSMES5, ";
}
$sql .="(
	SELECT SUM(CONSUMO) FROM v_consumo_tp_mov AS MES6
	WHERE MES6.pro_codigo = CONS.pro_codigo
	AND EXTRACT(YEAR FROM MES6.MOV_DATA) = $ano6
	AND EXTRACT(MONTH FROM MES6.MOV_DATA) = $mes6
    ";
    if ($set_codigo)   $sql .= " AND MES6.CODSETOR = $set_codigo ";
    if ($gru_codigo)   $sql .= " AND MES6.GRU_CODIGO = $gru_codigo ";
$sql .="
	$tipomovim
	) AS CONSMES6 
	
	FROM V_CONSUMO AS CONS
	INNER JOIN v_movimentacao AS mov ON mov.mov_data = CONS.mov_data
	WHERE CONS.MOV_DATA >= '$dt_inicial' AND CONS.MOV_DATA <='$dt_final' 
	";

if ($set_codigo)   $sql .= " AND CONS.CODSETOR = $set_codigo ";
if ($gru_codigo)   $sql .= " AND CONS.GRU_CODIGO = $gru_codigo ";
if ($pro_codigo)   $sql .= " AND CONS.pro_codigo = '$pro_codigo' ";

$sql .= "GROUP BY CONS.PRO_CODIGO, CONS.PRO_NOME
	ORDER BY CONS.PRO_NOME";
//echo "<pre>".$sql."</pre>";
$query=db_query($sql);
$lin=999;

if (pg_num_rows($query) == 0) {
    echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=70% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
    echo "  <tr><td align=center colspan=5>N&Atilde;O TEM DADOS PARA ESTES PAR&Acirc;METROS</td></tr>\n";
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
			$c1 = "";
			$c2 = "#A6A6A6";
			
			if ($controle == 0) {
			  $cor = $c1;
			  $controle++;
			} else {
			  $cor = $c2;
			  $controle = 0;
			}
			
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $UniNome, '0', $mes1, $ano1, $mes2, $ano2, $mes3, $ano3, $mes4, $ano4, $mes5, $ano5, $mes6, $ano6, $tipo, $tipo_mov);
              $lin=9;
          }
          $seleciona = "SELECT sum(sal_qtde) as estoqueatual
						  FROM saldo s
						 WHERE s.pro_codigo = $row[0]
						   AND s.set_codigo = $set_codigo";
          $exec_seleciona = pg_query($seleciona);
          $saldoAtual = pg_fetch_array($exec_seleciona);
	  $total = $row[2] + $row[3] + $row[4] + $row[5] + $row[6] + $row[7];
	if( $total != 0 )
	{
          echo " <tr bgcolor='$cor' align=center>\n";

          $media = $total / $meses;
          $totaled = formata_valor($total);
          $mediaed = formata_valor($media);

          echo "  <td>$row[1]</td>\n";
	if( $mes1 )
	{
          echo "  <td>".number_format($row[2],0,',','.')."&nbsp;</td>\n";
        }
        if( $mes2 )
	{
          echo "  <td>".number_format($row[3],0,',','.')."&nbsp;</td>\n";
        }
        if( $mes3 )
	{
          echo "  <td>".number_format($row[4],0,',','.')."&nbsp;</td>\n";
        }
        if( $mes4 )
	{
          echo "  <td>".number_format($row[5],0,',','.')."&nbsp;</td>\n";
        }
        if( $mes5 )
	{
          echo "  <td>".number_format($row[6],0,',','.')."&nbsp;</td>\n";
        }
        if( $mes6 )
	{
          $ultimo = $meses+1;
          echo "  <td>".number_format($row[$ultimo],0,',','.')."&nbsp;</td>\n";
        }
          echo "  <td>".number_format($totaled,0,',','.')."</td>\n";
          echo "  <td>".number_format($mediaed,2,',','.')."</td>\n";
          echo "  <td>".number_format($saldoAtual[estoqueatual],0,',','.')."</td>\n";
          echo " </tr>\n";
        }

      }
      echo "</table>\n";
}
echo "</table>";
echo "<body>\n";
echo "<body>\n";
?>