<?php
/*
 * @version Renato 9/7/2007 - 9:30
*/
?>
<script language=javascript>

function imprimir() {
	window.print();
}
</script>
<style>
	.fundo1{
		background-color:#EEEEEE;
	}
	.fundo2{
		background-color:#FFFFFF;
	}
</style>
<body>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Dados Recebidos  ---------------->

$dt_i = $dt_inicial;
$dt_f = $dt_final;

//echo "Inicial-->".$dt_inicial."<br>";
//echo "Final---->".$dt_final."<br>";
//echo "Setor---->".$set_codigo."<br>";
//echo "Grupo---->".$gru_codigo."<br>";

$titulo = "Posicao do Estoque Por Centro Estocador e Periodo";    //       NOME DO RELATÓRIO

if ($set_codigo) {
	$sql = "SELECT setor.set_codigo, 
				   setor.set_nome " .  //       Pega Setor
           "  FROM setor " .
           " WHERE setor.set_codigo = $set_codigo";
	$query = pg_query($sql);
	while($rowSetor = pg_fetch_array($query)) {
		$SetNome=$rowSetor[1];
	}
} else {
	$SetNome = "TODOS";
}

if ($gru_codigo) {
	$sql = "SELECT g.gru_codigo, 
				   g.gru_nome " .  //       Pega Grupo
           "  FROM grupo g" .
           " WHERE g.gru_codigo = $gru_codigo";
    $query = pg_query($sql);
	while($rowGrupo = pg_fetch_array($query)) {
		$GruNome = $rowGrupo[1];
	}
} else {  
	$GruNome = "TODOS";  
}


function cabeca($Tit, $dtIni, $dtFin, $SetNo, $GruNo, $tpCab, $zerado) {

	//---------  Cabeçalho do Relatorio  ----------------->

	if ($tpCab == '0') {
		include "cabecalho.php";
		if ($pro_codigo) {
			echo "
			<tr>
				<td colspan=2><font size=1 face=courier>PRODUTO:   $ProNo</font></td>
			</tr>";
		}
		echo " 
		</table>";
	
		echo "
		<table style='font-size:11px;font-family:Tahoma,Arial;' border=0 width=100% align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>\n";
	}
//---------  Cabeçalho dos Dados  ----------------->

	if ($tpCab == '1') {
		echo " 
		<tr>\n
			<td width='55%' align=left ><b>Produto</b></td>\n
			<td width='10%' align=right><b>Estoque</b></td>\n
			<td width= '8%' align=right><b>Custo</b></td>\n
			<td width= '8%' align=right><b>Total</b></td>\n
		</tr>\n";
	}
}

//-------------  Rotina Captação dos Dados  --------------->


//         ---- Captação Produto/Movimentos --->

$sql = " SELECT x.pro_nome,
				x.estoque,
				x.custo
		   FROM (SELECT p.pro_nome,
						SUM(s.sal_qtde) AS estoque,
						ROUND(CAST(verifica_preco(p.pro_codigo, $set_codigo, '$dt_final') as numeric), 4) as custo 
				   FROM produto p
				   JOIN saldo s
					 ON s.pro_codigo = p.pro_codigo";
if ($gru_codigo) {
	$sql .= "	  WHERE gru_codigo = $gru_codigo ";
}			  
$sql .=			" GROUP By p.pro_codigo, p.pro_nome) as X ";
if ($zerado == 'NAO' ) {
	$sql .= "WHERE x.estoque > 0";
}
$sql .=		  "
		  ORDER BY x.pro_nome";
#vSQL($sql,"1");

//echo "<pre>$sql</pre>";

$query = pg_query($sql);

if (pg_num_rows($query) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "&nbsp;&nbsp;&nbsp;Data ->".$dt_final."<br>";
    echo "&nbsp;&nbsp;&nbsp;Setor     ->".$set_codigo."<br>";
}

//----------------  Rotina de Impressão  ------------------>

$lin = 999;

// Zebragem
$controle = 0;

while($row = pg_fetch_array($query)) {
	if ($lin > 20) {
		if ($lin== 999) {
			cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome, '0', $zerado);
		}
		cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome , '1', $zerado);
		
		echo " <tr>\n";
		echo "  <td></td>\n";
		echo " </tr>\n";
		$lin = 0;
	}

 //---- Pega Saldo --->

	$Saldo = $row['estoque'];
	$TotalItem = $row['custo'] * $Saldo;
	$TotalItemGeral = $TotalItemGeral + $TotalItem;

	$c1 = "#EEEEEE";
	$c2 = "";
	
	if ($controle == 0) {
		$classe = "fundo1";
		$controle++;
	} else {
		$classe = "fundo2";
		$controle = 0;
	}

	echo " 
		<tr class='$classe'>\n
			<td align=left>$row[pro_nome] </td>\n
			<td align=right>$Saldo    </td>\n
			<td align=right>".number_format($row['custo'],5,',','.')."</td>\n";
			$TotalItemfmt = number_format($TotalItem,2,',','.');
	//      $TotalItemfmt = formata_valor($TotalItem);
	echo "  <td align=right>$TotalItemfmt    </td>\n
		</tr>\n";
}
      echo " 
	  	<tr bgcolor='#A6A6A6' height='25'>\n
			<td align=left><b> Total Geral</b></td>\n
			<td align=right> </td>\n
			<td>               </td>\n";
	//      $TotalItemfmt = formata_valor($TotalItemGeral);
			$TotalItemfmt = number_format($TotalItemGeral,2,',','.');
	echo "  <td align=right><b>$TotalItemfmt  </b>  </td>\n
		</tr>\n
	</table>";

?>
