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
			 echo "  <td > Setor Destino/Fornecedor </td>\n";
			 echo "  <td > Tipo </td>\n";
			 echo "  <td > Quantidade </td>\n";
			 echo "  <td > Saldo </td>\n";
			 echo " </tr>\n";
			 echo " <tr>\n";
			 echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
         }
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

$sql = "SELECT data, mov_nr_nota, setor, nomesetorsolicit, desc_movimentacao, sinal, ite_quantidade,mov_tipo ,pro_codigo 
          FROM v_movimentacao
         WHERE ";

$sql .= "   codsetor = $set_codigo ";
if($pro_codigo != null || $pro_codigo != ""){
	$sql .= " AND pro_codigo = $pro_codigo ";
}
if($mov_tipo != "" || $mov_tipo != null){
	
	$sql .="AND mov_tipo = '$mov_tipo'";
}
$sql .= "   AND    mov_data between to_date('$dt_inicial','dd/mm/yyyy') and to_date('$dt_final', 'dd/mm/yyyy') ";
$sql .= " ORDER BY mov_data asc ";
//vSQL($sql,"1");
//print $sql;
//die($sql);
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

$lin = 99;

// Zebragem
$controle = 0;

while($row=pg_fetch_row($query)) {
      if ($lin > 20) {
          if ($lin== 99) {
              cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome,  '1');
//              $Saldo=$SaldoAnterior;
          }

         cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $ProNome, '0');

         $lin=0;
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
      echo "  <td>$row[0]</td>\n";
      echo "  <td align=center>$row[1]</td>\n";
      echo "  <td>$row[2]-$row[3]</td>\n";
      echo "  <td>$row[4]</td>\n";
      echo "  <td>".number_format($row[6], 0, ',', '.')."</td>\n";
      echo "  <td>".number_format($Saldo, 0, ',', '.')."</td>\n";
	  echo " </tr>\n";
}
echo "</table>";

?>
