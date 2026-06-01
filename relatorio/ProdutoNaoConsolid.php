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
//echo "Grupo---->".$gru_codigo."<br>";

$titulo="Produtos com Consolidacoes pendentes";    //       NOME DO RELATÓRIO

if ($set_codigo) {
    $sql = "SELECT setor.set_codigo, setor.set_nome " .  //       Pega Setor
           "  FROM setor " .
           " WHERE setor.set_codigo = $set_codigo";
    $query=db_query($sql);
    while($rowSetor=pg_fetch_array($query)) {
          $SetNome=$rowSetor[1];
    }
} else {  $SetNome = "TODOS";  }

if ($pro_codigo) {
    $sql = "SELECT produto.pro_codigo, produto.pro_nome " .  //       Pega Produto
           "  FROM produto " .
           " WHERE produto.pro_codigo = $pro_codigo";
    $query=db_query($sql);
    while($rowProduto=pg_fetch_array($query)) {
          $proProduto=$rowProduto[1];
    }
} else {  $proProduto = "TODOS";  }
function cabeca($Tit, $dtIni, $dtFin, $SetNo, $GruNo, $tpCab, $proProduto) {

        //---------  Cabeçalho do Relatorio  ----------------->

        if ($tpCab == '0')
        {
            /* echo "<table  width='100%' cellspacing=0 cellpadding=0 border=0 align=center>
	 	            <tr>
	     	         <td width=250><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	         <td width= 10><font size=1 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>Periodo: $dtIni a $dtFin</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>SETOR:   $SetNo</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>Produto:   $proProduto</font></td>
 	    	        </tr>
                    ";
 	    	 echo " <tr>
		             <td>&nbsp;</td>
		             <td>&nbsp;</td>
	    	        </tr>
 	               </table>";
            */
            include 'cabecalho.php';  
 	         echo "<table style='font-size:11px;font-family:Tahoma,Arial;' border=1 width='100%' align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>\n";
        }
        //---------  Cabeçalho dos Dados  ----------------->

         if ($tpCab == '1') {
             echo " <tr>\n";
		 	 echo "  <td width='40%' colspan=1 align=left ><b> Produto                 </b></td>\n";
			 echo "  <td width='10%'           align=left><b> Nota </b></td>\n";
			 echo "  <td width= '10%'align=left><b> Data Mov. </b></td>\n";
			 echo "  <td width= '30%'align=left><b> Tipo Mov. </b></td>\n";
			 echo "  <td width= '15%'align=left><b>   Quantidade         </b></td>\n";
			 echo "  <td width= '15%' align=left><b> Estoque</b></td>\n";
			 echo " </tr>\n";
			 echo " <tr>\n";
			 //echo "  <td align=center cellspacing=0 cellpadding=1 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
         }
}

//-------------  Rotina Captação dos Dados  --------------->


//         ---- Captação Produto/Movimentos --->

/*$sql = "SELECT mov_naoconsolid.pro_nome, mov_nr_nota, data as mov_data, ite_quantidade ,
                          round(cast (calcula_estoque (produto.pro_codigo, $set_codigo, mov_data) as numeric), 4), desc_movimentacao
		FROM mov_naoconsolid, produto
        WHERE mov_naoconsolid.pro_codigo = produto.pro_codigo ";
          ($pro_codigo ? $sql .= " AND   produto.pro_codigo = $pro_codigo " : "");
$sql .= " AND codsetor = $set_codigo
          AND mov_data >= '$dt_inicial' and mov_data <= '$dt_final'
		  ORDER BY  mov_nr_nota, produto.pro_nome, mov_data ";*/
$sql = "SELECT mov_naoconsolid_rel.pro_nome, mov_nr_nota, data as mov_data, ite_quantidade ,
                          round(cast (calcula_estoque (produto.pro_codigo, $set_codigo, mov_data) as numeric), 4), desc_movimentacao
		FROM mov_naoconsolid_rel, produto
        WHERE mov_naoconsolid_rel.pro_codigo = produto.pro_codigo ";
          ($pro_codigo ? $sql .= " AND   produto.pro_codigo = $pro_codigo " : "");
$sql .= " AND codsetor = $set_codigo
          AND mov_data >= '$dt_inicial' and mov_data <= '$dt_final'
		  ORDER BY  mov_nr_nota, produto.pro_nome, mov_data ";
//vSQL($sql,"1");

//echo "<pre>$sql</pre>";

$query=db_query($sql);

if (pg_num_rows($query) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "&nbsp;&nbsp;&nbsp;Data ->".$dt_final."<br>";
    echo "&nbsp;&nbsp;&nbsp;Setor     ->".$set_codigo."<br>";
}


//echo "<pre>$sql</pre>";

//----------------  Rotina de Impressão  ------------------>

$lin = 999;
$total = 0;

// Zebragem
$controle = 0;

while($row=pg_fetch_row($query)) {
      if ($lin > 20) {
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome, '0', $proProduto);
            }
         cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome , '1', $proProduto);
         $lin=0;
      }

 //---- Pega Saldo --->

		$c1 = "";
		$c2 = "#A6A6A6";
		
		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		} else {
		  $cor = $c2;
		  $controle = 0;
		}
		
      echo " <tr bgcolor='$cor'>\n";
      echo "  <td align=left>$row[0] </td>\n";
      echo "  <td align=left>$row[1]    </td>\n";
      echo "  <td align=left>$row[2]               </td>\n";
      echo "  <td align=left>$row[5]               </td>\n";
      echo "  <td align=left>$row[3]               </td>\n";
      echo "  <td align=left>$row[4]               </td>\n";
	  echo " </tr>\n";
      $total = $total + $row[3];

}
      //echo " <tr><td colspan='6'><hr></td></tr>\n";
      echo " <tr>\n";
      echo "  <td align=left colspan='4'>Total Geral </td>\n";
      echo "  <td align=left colspan='2'>$total          </td>\n";
	  echo " </tr>\n";
echo "</table>";

?>
