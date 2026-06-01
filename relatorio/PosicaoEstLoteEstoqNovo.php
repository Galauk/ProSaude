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
$tipo_e_codigo = explode('_', $_GET[pro_codigo]);
$tipo = $tipo_e_codigo[0];
$codigo = $tipo_e_codigo[1];
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
    $sql = "SELECT p.pro_codigo, 
    			   p.pro_nome " . //       Pega Produto
           "  FROM produto p ";
    if ($tipo == 1){
    $sql .= "WHERE p.gru_codigo = $codigo"; 
    }else{
    $sql .= "WHERE p.pro_codigo = $codigo";
    }

    $query = pg_query($sql);
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
		 	 echo "  <td width='45%'><b>Nome</b></td>\n";
		 	 echo "  <td width='15%'><b>Lote</b></td>\n";
		 	 echo "  <td width='15%'><b>Validade</b></td>\n";
			 echo "  <td width='10%' align=right><b>Estoque</b></td>\n";
			 echo "  <td width= '5%' style='padding-left:10px'><b>Custo</b></td>\n";
			 echo "  <td width= '10%' align=right><b>Total</b></td>\n";
			 echo " </tr>\n";
/*			 echo " <tr>\n";
			 echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
*/         }
}


$sql  = "SELECT p.pro_nome, 
				s.*,
				(select sum(ite_custo_medio) 
				   from itens_movimento im
				   join movimento m
					 on m.mov_codigo = im.mov_codigo
				  where im.ite_lote = s.sal_lote
					and im.pro_codigo = p.pro_codigo
					and (m.set_entrada = s.set_codigo or m.set_saida=s.set_codigo)) as custo
		   FROM saldo s
		   JOIN produto p
			 ON s.pro_codigo = p.pro_codigo
		  WHERE s.set_codigo = $set_codigo
"; 
if ($pro_codigo) {
if ($tipo == 1){
    $sql .= "AND p.gru_codigo = $codigo"; 
    }else{
    $sql .= "AND p.pro_codigo = $codigo";
    }
	//$sql .= "AND p.pro_codigo = $pro_codigo"; 
}
if ($zerado == 'NAO' ) {
	$sql .= "AND s.sal_qtde > 0";
}

$sql .= "AND sal_qtde>=0 ";

if ($pros_codigo) {
	$sql .= "AND  pros_codigo = $pros_codigo ";
}

$sql .= " ORDER BY p.pro_nome";

//echo $sql;
$query = pg_query($sql);

if (pg_num_rows($query) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "&nbsp;&nbsp;&nbsp;Data ->".$dt_final."<br>";
    echo "&nbsp;&nbsp;&nbsp;Setor ->".$set_codigo."<br>";
}

//----------------  Rotina de Impressão  ------------------>

$lin = 999;

// Zebragem
$controle = 0;
$saldoTotal = 0;
$custoTotal = 0;
while($row = pg_fetch_array($query)) {
	$pro_nome = $row['pro_nome'];
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

 //---- Pega Saldo --->
	
	$Saldo = $row['sal_qtde'];
	$saldoTotal += $Saldo; 
	$TotalItem = $row['custo'] * $Saldo;
	$custoTotal += $row['custo'];
	$TotalItemGeral += $TotalItem;



	$c1 = "#EEEEEE";
//	$c1 = "#A6A6A6";
	$c2 = "";
		
	if ($controle == 0) {
		$cor = $c1;
		$controle++;
	}
	else
	{
		$cor = $c2;
		$controle = 0;
	}
	
	echo " 
	<tr bgcolor='$cor'>\n
		<td>".$pro_nome."                     </td>\n
		<td>$row[sal_lote] </td>\n
		<td>".formatarData($row['sal_validade'])." </td>\n
		<td align=right>".number_format($Saldo,0,',','.')."    </td>\n
		<td style='padding-left:10px'>".number_format($row['custo'],5,',','.')."&nbsp;&nbsp;&nbsp;&nbsp; </td>\n";
			$TotalItemfmt = number_format($TotalItem,2,',','.');
		  //$TotalItemfmt = formata_valor($TotalItem);
	echo "
		<td align=right>$TotalItemfmt    </td>\n
	</tr>\n";
}
	echo " 
	<tr bgcolor='#A6A6A6' height='25'>
			<td width= '45%'><b> Total Geral</b>      </td>\n
			<td width= '15%'>                         </td>\n
			<td width= '15%'>                         </td>\n
			<td width= '10%' align=right><b>".($tipo == 1 ? "&nbsp;" : number_format($saldoTotal))."</b></td>\n
			<td width= '5%'></td>\n";
	//      $TotalItemfmt = formata_valor($TotalItemGeral);
	$TotalItemfmt = number_format($TotalItemGeral,2,',','.');
	echo "  
			<td width= '10%' align=right><b>$TotalItemfmt</b></td>\n
	</tr>\n";

echo "</table>";

?>
