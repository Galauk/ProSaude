<script language=javascript>

function imprimir() {
       window.print();
}
</script>
<script
	language="JavaScript" type="text/javascript" src="funcoes.js"></script>

<body>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Dados Recebidos  ---------------->

$dt_i=$dt_inicial;
$dt_f=$dt_final;

//echo "Inicial-->".$dt_inicial."<br>";
//echo "Final---->".$dt_final."<br>";
//echo "Setor---->".$set_codigo."<br>";
//echo "Produto-->".$pro_codigo."<br>";

$titulo="Movimento de Produtos em Centro Estocador, Por Periodo";    //       NOME DO RELATÓRIO

if ($set_codigo) {
	$sql = "SELECT setor.set_codigo, setor.set_nome " .  //       Pega Setor
           "  FROM setor " .
           " WHERE setor.set_codigo = $set_codigo";
	$query=pg_query($sql);
	while($row=pg_fetch_array($query)) {
		$SetNome=$row[0]." - ".$row[1];
	}
} else {  $SetNome = "TODOS";  }

if ($pro_codigo) {
	$sql = "SELECT produto.pro_nome, produto.pro_codigo " .  //       Pega Produto
           "  FROM produto " .
           " WHERE produto.pro_codigo = $pro_codigo";
	$query=pg_query($sql);
	while($row=pg_fetch_array($query)) {
		$ProNome=$row[0]." - ".$row[1];
	}
}

function cabeca($Tit, $dtIni, $dtFin, $SetNo, $ProNo, $tpCab) {

	//---------  Cabeçalho do Relatorio  ----------------->

	if ($tpCab == 1) {
		include "cabecalho.php";
		echo "<table style=\"font-size:09px;font-family:Tahoma,Arial;\" border=0 width=100% align=center cellspacing=0 cellpadding=0 topmargin=0 leftmargin=0>\n";
	}
	//---------  Cabeçalho dos Dados  ----------------->

	if ($tpCab == 0) {
		echo " <tr>\n";
		echo "  <td width='6%' align=left  > Data </td>\n";
		echo "  <td width='12%' align=center> N.Nota/Mov </td>\n";
		//echo "  <td > Setor Destino/Fornecedor </td>\n";
		echo "  <td align='right'> Tipo </td>\n";
		echo "  <td align='right'> Lote</td>\n";
		echo "  <td align='right'> Quantidade </td>\n";
		echo "  <td align='right'> Saldo </td>\n";
		echo " </tr>\n";
		echo " <tr>\n";
		echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
		echo " </tr>\n";
	}
}

function cabecatotal($saldo){
	echo"<tr>
			<td colspan=5 ></td>
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \">
			<strong>Saldo físico final</strong>
			</td> 
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
				<strong>".number_format($saldo, 0, ',', '.')."</strong>
			</td>
		</tr>";  

}
function qtdeAntiga($qtde,$espaco)
{	echo"<tr>
			<td colspan=5 ></td>
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \">
			<strong>Saldo Físico Anterior</strong>
			</td> 
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
				<strong>".number_format($qtde, 0, ',', '.')."</strong>
			</td>".$espaco."
		</tr>";  
}

//-------------  Rotina Captação dos Dados  --------------->

//                ---- Saldo Anterior ---                  >

if ($set_codigo) {
	$LclPrMedio=$set_codigo;
} else {
	$row=pg_fetch_row(pg_query("select * from conf_estoque"));
	$LclPrMedio=$row[1];
}


//              ---- Captação Movimentos ---               >
/*$sql ="SELECT to_char(mov_data, 'dd/mm/yyyy') as data,*
 FROM itens_movimento im
 JOIN movimento m
 ON m.mov_codigo = im.mov_codigo
 WHERE im.pro_codigo = $pro_codigo
 AND set_saida = $set_codigo
 AND mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy')

 UNION ALL

 SELECT to_char(mov_data, 'dd/mm/yyyy') as data,*
 FROM itens_movimento im
 JOIN movimento m
 ON m.mov_codigo = im.mov_codigo
 WHERE im.pro_codigo = $pro_codigo
 AND set_entrada = $set_codigo
 AND mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy') ORDER BY mov_data asc";*/
$sql ="SELECT * FROM
		  (SELECT to_char(mov_data, 'dd/mm/yyyy') as data, m.mov_codigo as movimento,
		  CASE WHEN m.mov_tipo = 'S' THEN 'SAIDA' 
		  		 ELSE 'TRANSFERENCIA' END as mov_tipo2, *  
		  FROM itens_movimento im 
		  JOIN movimento m 
			ON m.mov_codigo = im.mov_codigo 
		  WHERE im.pro_codigo = $pro_codigo 
		  AND set_saida = $set_codigo
		  and mov_tipo in ('S', 'T')
		  AND mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy') 
		
		  UNION ALL 
		
		  SELECT to_char(mov_data, 'dd/mm/yyyy') as data, m.mov_codigo as movimento, 
	  		     CASE WHEN m.mov_tipo = 'E' THEN 'ENTRADA' 
		  		 ELSE 'TRANSFERENCIA' END as mov_tipo2, * 
		  FROM itens_movimento im 
		  JOIN movimento m 
			ON m.mov_codigo = im.mov_codigo 
			WHERE im.pro_codigo = $pro_codigo
			AND set_entrada = $set_codigo
			and mov_tipo in ('E', 'T')
			
			AND mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy') ) as x		
			ORDER BY x.mov_data, x.movimento asc";			   


/*$sql = "SELECT data, mov_nr_nota, setor, nomesetorsolicit, desc_movimentacao, sinal, ite_quantidade, pro_codigo
 FROM v_movimentacao
 WHERE ";
 $sql .= " pro_codigo = $pro_codigo ";
 $sql .= " AND  codsetor = $set_codigo ";
 $sql .= "   AND    mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy') ";
 $sql .= " ORDER BY mov_data asc ";*/

//vSQL($sql,"1");
//print $sql;
$query=pg_query($sql);

if (pg_num_rows($query) == 0) {
	echo " <tr>\n";
	echo "  <td width='10'>&nbsp;</td>\n";
	echo "  <td>NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br></td>\n";
	echo " </tr>\n";

	echo "<table  width=100% cellspacing=0 cellpadding=0 border=0 align=center>\n";
	echo " <tr>\n";
	echo "  <td width='80'>&nbsp;</td>\n";
	echo "  <td>Data INICIAL.( ".$dt_inicial." )<br></td>\n";
	echo " </tr>\n";
	echo " <tr>\n";
	echo "  <td width='80'>&nbsp;</td>\n";
	echo "  <td>Data FINAL....( ".$dt_final." )<br></td>\n";
	echo " </tr>\n";
	echo " <tr>\n";
	echo "  <td width='80'>&nbsp;</td>\n";
	echo "  <td>PRODUTO.....( ".$pro_codigo." )<br></td>\n";
	echo " </tr>\n";
	echo " <tr>\n";
	echo "  <td width='80'>&nbsp;</td>\n";
	echo "  <td>SETOR...........( ".$set_codigo." )<br></td>\n";
	echo " </tr>\n";
}

//----------------  Rotina de Impressão  ------------------>

//----------------  Obtenção dos totais da movimento inicial  ------------------>
$entrada = "select sum(x.ite_quantidade) as entradas from
				(SELECT to_char(mov_data, 'dd/mm/yyyy') as data,
					m.mov_tipo,im.ite_validade,
					* 
				  FROM itens_movimento im 
				  JOIN movimento m 
					ON m.mov_codigo = im.mov_codigo 
				  
				  WHERE im.pro_codigo = $pro_codigo 
				  AND set_entrada = $set_codigo
				  AND mov_tipo in ('E','T') 
				  AND mov_data < '$dt_inicial') as x";



$exe_ent = pg_query($entrada);
$res_exe_ent = pg_fetch_array($exe_ent);
$totalEntrada = $res_exe_ent['entradas'];



$saida ="select sum(x.ite_quantidade) as saidas from
				(SELECT to_char(mov_data, 'dd/mm/yyyy') as data,
					m.mov_tipo,im.ite_validade,
					* 
				  FROM itens_movimento im 
				  JOIN movimento m 
					ON m.mov_codigo = im.mov_codigo				  
				  WHERE im.pro_codigo = $pro_codigo 
				  AND set_saida = $set_codigo
				  AND mov_tipo in ('S','T')
				  AND mov_data < '$dt_inicial') as x";
$exe_sai = pg_query($saida);
$res_exe_sai = pg_fetch_array($exe_sai);
$totalsai = $res_exe_sai['saidas'];




//----------------  Obtenção dos totais da movimento final  ------------------>
$entrada2 = "select sum(x.ite_quantidade) as entradas from
				(SELECT to_char(mov_data, 'dd/mm/yyyy') as data,
					m.mov_tipo,im.ite_validade,
					* 
				  FROM itens_movimento im 
				  JOIN movimento m 
					ON m.mov_codigo = im.mov_codigo 
				  
				  WHERE im.pro_codigo = $pro_codigo 
				  AND set_entrada = $set_codigo
				  AND mov_tipo in ('E','T') 
				  AND mov_data <= '$dt_final') as x";
$exe_ent2 = pg_query($entrada2);
$res_exe_ent2 = pg_fetch_array($exe_ent2);
$totalEntrada2 = $res_exe_ent2['entradas'];

$saida2 ="select sum(x.ite_quantidade) as saidas from
				(SELECT to_char(mov_data, 'dd/mm/yyyy') as data,
					m.mov_tipo,im.ite_validade,
					* 
				  FROM itens_movimento im 
				  JOIN movimento m 
					ON m.mov_codigo = im.mov_codigo 
				  
				  WHERE im.pro_codigo = $pro_codigo 
				  AND set_saida = $set_codigo
				  AND mov_tipo in ('S','T')
				  AND mov_data <= '$dt_final') as x";
$exe_sai2 = pg_query($saida2);
$res_exe_sai2 = pg_fetch_array($exe_sai2);
$totalsai2 = $res_exe_sai2['saidas'];

/*$soma = "SELECT sum(x.sal_qtde) AS soma
 FROM(SELECT *
 FROM saldo
 WHERE pro_codigo = $pro_codigo order by sal_data desc) AS x";
 $exeSoma = pg_query($soma);
 $resSoma = pg_fetch_array($exeSoma);
 $saldoDaSoma = $resSoma['soma'];*/
$saldoDaSoma2 = $totalEntrada2 - $totalsai2;
$saldoDaSoma = $totalEntrada - $totalsai;
$valortotal = $saldoDaSoma;
$lin = 99;

// Zebragem
$controle = 0;

while($res = pg_fetch_array($query)) {
	if ($lin > 20) {
		if ($lin== 99) {
			cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome,  '1');
			//              $Saldo=$SaldoAnterior;
		}
		//   	 	qtdeAntiga($saldoDaSoma);

		echo "<tr>
			<td colspan=5 ></td>
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \">
			<strong>Saldo físico inicial</strong>
			</td> 
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
				<strong>".number_format($saldoDaSoma, 0, ',', '.')."</strong>
			</td>
		</tr>";
		echo"<tr>
			<td colspan=5></td>
			<td colspan=2 align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \">
			<strong>___________________________________</strong>
			</td> 
			
		</tr>";    		

		echo"<tr>
			<td colspan=5 ></td>
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \">
			<strong>Saldo físico final</strong>
			</td> 
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
				<strong>".number_format($saldoDaSoma2, 0, ',', '.')."</strong>
			</td>
		</tr>";  
		cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome, '0');

		$lin=0;
	}
	if($res['mov_tipo'] == 'E'){
		$saldoDaSoma = $saldoDaSoma + $res['ite_quantidade'];
	}
	if($res['mov_tipo'] == 'S'){
		$saldoDaSoma = $saldoDaSoma - $res['ite_quantidade'];
	}
	if($res['mov_tipo'] == 'T' && $res['set_entrada'] == $set_codigo){
		$saldoDaSoma = $saldoDaSoma + $res['ite_quantidade'];
	}
	if($res['mov_tipo'] == 'T' && $res['set_saida'] == $set_codigo){
		$saldoDaSoma = $saldoDaSoma - $res['ite_quantidade'];
	}


	switch ($row[5]) {
		case "-":    $Saldo=$Saldo-$row[6];  break;
		case "+":	  $Saldo=$Saldo+$row[6];  break;
	}

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
	echo "  <td align='right'>$res[data]</td>\n";
	echo "  <td align=center>$res[mov_nr_nota]</td>\n";
	//echo "  <td>$res[set_nome]</td>\n";
	echo "  <td align='right'>$res[mov_tipo2]</td>\n";
	echo "  <td align='right'>$res[ite_lote]</td>\n";
	echo "  <td align='right'>".number_format($res['ite_quantidade'], 0, ',', '.')."</td>\n";
	echo "  <td align='right'>".number_format($saldoDaSoma, 0, ',', '.')."</td>\n";

	echo " </tr>\n";

}
cabecatotal($saldoDaSoma);


echo "</table>";

?>