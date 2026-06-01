<script language=javascript>

function imprimir() {
       window.print();
}
</script>

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

$titulo="Posicao do Estoque Por Produto / Lote e Validade";    //       NOME DO RELATÓRIO

if ($set_codigo) {
    $sql = "SELECT setor.set_codigo, setor.set_nome " .  //       Pega Setor
           "  FROM setor " .
           " WHERE setor.set_codigo = $set_codigo";
    $query=pg_query($sql);
    while($rowSetor=pg_fetch_array($query)) {
          $SetNome=$rowSetor[1];
    }
} else {  $SetNome = "TODOS";  }

if ($pro_codigo) {
    $sql = "SELECT produto.pro_codigo, produto.pro_nome " . //       Pega Produto
           "  FROM produto " .
           " WHERE produto.pro_codigo = $pro_codigo";
    $query=pg_query($sql);
    while($rowProduto=pg_fetch_array($query)) {
          $ProNome=$rowProduto[1];
    }
} else {  $ProNome = "TODOS";  }

function cabeca($Tit, $dtIni, $dtFin, $SetNo, $ProNo, $tpCab, $zerado) {

        //---------  Cabeçalho do Relatorio  ----------------->

         if ($tpCab == '0') {
             include "cabecalho.php";

 	         echo "<table style='font-size:11px;font-family:Tahoma,Arial;' border=0 width='100%' align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>\n";
         }
        //---------  Cabeçalho dos Dados  ----------------->

         if ($tpCab == '1') {
             echo " <tr>\n";
		 	 echo "  <td width= '5%' colspan=1 align=right> Nome                 </td>\n";
		 	 echo "  <td width='25%' colspan=1 align=left > Lote                 </td>\n";
		 	 echo "  <td width='25%' colspan=1 align=left > Validade             </td>\n";
			 echo "  <td width='10%'           align=right> Estoque &nbsp;&nbsp;&nbsp;</td>\n";
			 echo "  <td width= '6%'                      >                         </td>\n";
			 echo "  <td width= '8%'> Custo                                         </td>\n";
			 echo "  <td width= '8%' align=right> Total &nbsp;&nbsp;&nbsp;          </td>\n";
			 echo " </tr>\n";
			 echo " <tr>\n";
			 echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
         }
}

//-------------  Rotina Captação dos Dados  --------------->


//         ---- Captação Produto/Movimentos --->
/*
$sql  = "SELECT produto.pro_codigo, pro_nome, ite_lote,
		to_char(to_date(ite_validade, 'dd/mm/yyyy'), 'dd/mm/yyyy') as ite_validade,
		round(cast (calcula_estoque_lote(produto.pro_codigo, $set_codigo, '$dt_final', ite_lote) as numeric), 0), 
        round(cast (verifica_preco (produto.pro_codigo, $set_codigo, '$dt_final') as numeric), 4),
		to_date(ite_validade, 'dd/mm/yyyy') as ite_validade2
		FROM produto, itens_movimento
		WHERE produto.pro_codigo = itens_movimento.pro_codigo"; 

if ($zerado == 'NAO' ) {
    $sql .= " AND calcula_estoque_lote(produto.pro_codigo, $set_codigo, '$dt_final', ite_lote) > 0 ";
    if ($pro_codigo) {
       $sql .= " AND produto.pro_codigo = $pro_codigo "; 
    }
} else {
    if ($pro_codigo) {
       $sql .= " AND produto.pro_codigo = $pro_codigo "; 
    }
}    
$sql .= " GROUP BY produto.pro_codigo, produto.pro_nome, ite_lote, ite_validade ";
$sql .= " ORDER BY produto.pro_nome, ite_validade2 ";
*/
$sql  = "SELECT produto.pro_codigo, 
				pro_nome, 
				ite_lote,
				ite_validade as ite_validade,
				calcula_estoque_lote(produto.pro_codigo, $set_codigo, '$dt_final', ite_lote), 
				verifica_preco(produto.pro_codigo, $set_codigo, '$dt_final')
		   FROM produto, 
		   		itens_movimento
		  WHERE produto.pro_codigo = itens_movimento.pro_codigo"; 

if ($zerado == 'NAO' ) {
    $sql .= " AND calcula_estoque_lote(produto.pro_codigo, $set_codigo, '$dt_final', ite_lote) > 0 ";
    if ($pro_codigo) {
       $sql .= " AND produto.pro_codigo = $pro_codigo "; 
    }
} else {
    if ($pro_codigo) {
       $sql .= " AND produto.pro_codigo = $pro_codigo "; 
    }
}    
$sql .= " GROUP BY produto.pro_codigo, produto.pro_nome, ite_lote, ite_validade ";
$sql .= " ORDER BY produto.pro_nome, ite_validade ";
//vSQL($sql,"1");

//echo "<pre>$sql</pre>";

$query=pg_query($sql);

if (pg_num_rows($query) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "&nbsp;&nbsp;&nbsp;Data ->".$dt_final."<br>";
    echo "&nbsp;&nbsp;&nbsp;Setor     ->".$set_codigo."<br>";
}

//----------------  Rotina de Impressão  ------------------>

$lin = 999;

// Zebragem
$controle = 0;
while($row=pg_fetch_row($query)) {
      if ($lin > 20) {
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome, '0', $zerado);
            }
         cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome , '1', $zerado);

         echo " <tr>\n";
		 echo "  <td></td>\n";
		 echo " </tr>\n";
         $lin=0;
      }
	$pro_nome = $row['pro_nome'];
 //---- Pega Saldo --->

	  $Saldo=$row[4];
	  $TotalItem=$row[5] * $Saldo;
      $TotalItemGeral = $TotalItemGeral + $TotalItem;

		$c1 = "";
		$c2 = "#A6A6A6";
		
		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		}
                else
                {
		  $cor = $c2;
		  $controle = 0;
		}

      echo " <tr bgcolor='$cor'>\n";
      echo "  <td width= '5%' colspan=1 align=right> $pro_nome                     </td>\n";
      echo "  <td align=left>$row[2] </td>\n";
      echo "  <td align=left>".substr($row[3],8,2)."/".substr($row[3],5,2)."/".substr($row[3],0,4)." </td>\n";
      echo "  <td align=right>".number_format($Saldo,0,',','.')."    </td>\n";
      echo "  <td>                      </td>\n";
      echo "  <td align=left>".number_format($row[5],5,',','.')."&nbsp;&nbsp;&nbsp;&nbsp; </td>\n";
      $TotalItemfmt = number_format($TotalItem,2,',','.');
//      $TotalItemfmt = formata_valor($TotalItem);
      echo "  <td align=right>$TotalItemfmt    </td>\n";
	  echo " </tr>\n";

}
      echo " <tr><h2>\n";
      echo "  <td width= '5%' colspan=1 align=right>                         </td>\n";
      echo "  <td colspan=2 align=left><h4> Total Geral</h4></td>\n";
      echo "  <td align=right> </td>\n";
      echo "  <td>                      </td>\n";
      echo "  <td>               </td>\n";
//      $TotalItemfmt = formata_valor($TotalItemGeral);
      $TotalItemfmt = number_format($TotalItemGeral,2,',','.');
      echo "  <td align=right><h4>$TotalItemfmt  </h4>  </td>\n";
	  echo " </h2></tr>\n";

echo "</table>";

?>
