<script language=javascript>

function imprimir() {
       window.print();
}
</script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>

<body>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Dados Recebidos  ---------------->

//echo "Inicial-->".$dt_inicial."<br>";
//echo "Final---->".$dt_final."<br>";
//echo "Setor---->".$set_codigo."<br>";
//echo "Produto-->".$pro_codigo."<br>";

$titulo="Dispensação de Medicamentos";    //       NOME DO RELATÓRIO




function cabeca($Tit, $dtIni, $dtFin, $SetNo, $ProNo, $tpCab) {

        //---------  Cabeçalho do Relatorio  ----------------->

         if ($tpCab == 1) {
            include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
 	        echo "<table style=\"font-size:09px;font-family:Tahoma,Arial;\" border=0 width=100% align=center cellspacing=0 cellpadding=0 topmargin=0 leftmargin=0>\n";
 	     }
        //---------  Cabeçalho dos Dados  ----------------->

         if ($tpCab == 0) {
         	 echo "<tr>";
         	 echo "<td colspan=6 align=center><strong>Itens Do Movimento</strong></td>";
             echo "</tr>";
         	 echo " <tr>\n";
		 	 echo "  <td align=left  > Codigo </td>\n";
			 echo "  <td  align=center> Descrição/Mov </td>\n";
			 echo "  <td > Lote </td>\n";
			 echo "  <td > Validade </td>\n";
		  	 echo "  <td align='right'> Qtde</td>\n";
			 echo "  <td align='right'> Valor </td>\n";
			 		
			 echo " </tr>\n";
			 echo " <tr>\n";
			 echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
         }
}

function cabecatotal($saldo,$quantidade){
	
	echo"<tr>
			<td style=\"font-size:14px;font-family:Tahoma,Arial;color:#000000; \"><strong>Total:</strong></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
			$quantidade
			</td> 
			<td align='right' style=\"font-size:14px;font-family:Tahoma,Arial;color:#FF0000; \">
				<strong>".number_format($saldo, 0, ',', '.')."</strong>
			</td>
		</tr>";  
      	
}


//-------------  Rotina Captação dos Dados  --------------->

//                ---- Saldo Anterior ---                  >



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
$sql ="select  m.mov_codigo,
	m.mov_data,
	u.usu_nome,
	s.set_nome,
	im.pro_codigo,
	p.pro_nome,
	im.ite_quantidade,
	im.ite_vlrtotal,*
	from movimento as m
	 JOIN itens_movimento as im
	   ON m.mov_codigo = im.mov_codigo
	 JOIN setor as s
	   ON s.set_codigo = m.set_saida
	 JOIN usuario as u
	   ON u.usu_codigo = m.usu_codigo
	 JOIN produto as p
	   ON p.pro_codigo = im.pro_codigo
	   where im.mov_codigo = '$mov_codigo'";		   

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

/*if (pg_num_rows($query) == 0) {
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
}*/

//----------------  Rotina de Impressão  ------------------>

//----------------  Obtenção dos totais da movimento inicial  ------------------>
/*$entrada = "select sum(x.ite_quantidade) as entradas from
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
$totalsai2 = $res_exe_sai2['saidas'];		*/	  
			  
/*$soma = "SELECT sum(x.sal_qtde) AS soma 
		   FROM(SELECT *
			      FROM saldo 
			     WHERE pro_codigo = $pro_codigo order by sal_data desc) AS x";
$exeSoma = pg_query($soma);
$resSoma = pg_fetch_array($exeSoma);
$saldoDaSoma = $resSoma['soma'];*/

$saldoDaSoma =0;

$lin = 99;
$qtde =0;
// Zebragem
$controle = 0;
//cabeca("ABC", "12/12/2010", "12/12/2010", "teste", "LOCO ABREU",  '1');
while($res = pg_fetch_array($query)) {
$qtde += $res['ite_quantidade'];
$saldoDaSoma +=$res['ite_vlrtotal'];
	 if ($lin > 20) {
          if ($lin== 99) {
          	$setor = $res['set_codigo']." - ".$res['set_nome'];
          	$ProNome = $res['pro_nome'];
              cabeca($titulo, $dt_inicial, $dt_final,$setor , $ProNome,  '1');
//              $Saldo=$SaldoAnterior;
          }
//   	 	qtdeAntiga($saldoDaSoma);
		
     	   		
		
		
         cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome, '0');
      
         $lin=0;
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
      echo "  <td align='right'>$res[pro_codigo]</td>\n";
      echo "  <td align=center>$res[pro_nome]</td>\n";
      echo "  <td>$res[ite_lote]</td>\n";
      echo "  <td align='right'>$res[ite_validade]</td>\n";
      echo "  <td align='right'>".number_format($res['ite_quantidade'], 0, ',', '.')."</td>\n";	 
      echo "  <td align='right'>$res[ite_vlrtotal]</td>\n";
  	
      echo " </tr>\n";  	 
	  
}
  cabecatotal($saldoDaSoma,$qtde);


echo "</table>";

?>
