<?php
/**
 * @brief       trata os cabeçalhos dos relatórios
*/
?>
<!-- --------------  Funções javascript  --------------- -->
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
}
</style>

<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>

<body>

<?php
//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$Tit="Lista de Produtos";    //       NOME DO RELATÓRIO
$dtIni = $dt_inicial;
$dtFin = $dt_final;

$select = "select pro_nome from produto where pro_codigo = $_GET[pro_codigo]";
$exec_select = pg_query($select);
$linha = pg_fetch_array($exec_select);
$Produto = $linha[0];

//------------------  Funções php  ------------------------>


include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";


echo "<link href='../estilo.css' rel='stylesheet' type='text/css'>";
$dt_ini = $_GET[dt_inicial];
$dt = explode("/",$dt_ini);
/*
echo $_GET[dt_inicial];
echo "<pre>";
	print_r($dt);
echo "</pre><br>";
echo "<br>->";
*/
if($dt[0] > 1)
{
	$num = cal_days_in_month(CAL_GREGORIAN, $dt[1], $dt[2]);
	if($dt[0] <= 10)
	{
		$dt[0] = $dt[0] - 1;
		$dt[0] = "0".$dt[0];
	} else {
		$dt[0] = $dt[0] - 1;
	}
	$mes = $dt[1];
	$ano = $dt[2];
} else {
	if($dt[1] == 1)
	{
		$dt[1] = 13;
		$dt[2] = $dt[2]-1;
	}
	$num = cal_days_in_month(CAL_GREGORIAN, ($dt[1]-1), $dt[2]);
	if($dt[1] <= 10)
	{
		$mes = $dt[1]-1;
		$mes = "0".$mes;
	} else {
		$mes = $dt[1]-1;
	}
	$dt[0] = $num;
	$ano = $dt[2];
}
$data_inicial = $dt[0]."/".$mes."/".$ano;

$dt_ini = $data_inicial;


	$select_saldo_anterior = "select calcula_estoque($_GET[pro_codigo], $_GET[centro_estocador], '$dt_ini')";
	$saldo = pg_query($select_saldo_anterior);
	$saldo_anterior = pg_fetch_array($saldo);
	/*echo $select_saldo_anterior;
	echo pg_last_error($db);*/
	$select_preco = "select verifica_preco($_GET[pro_codigo], $_GET[centro_estocador], '$dt_ini')";
	$pre = pg_query($select_preco);
	$preco = pg_fetch_array($pre);
	/*echo "<br>";
	echo $select_preco;
	echo "<br>".pg_last_error($db);*/
echo "Produto: <b>".$Produto."</b>";
	echo "<table width=90% border=1 cellspacing=1 cellpadding=1>";
		echo "<tr bgcolor=F9f9f9>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left' width=100px>";
				echo "Saldo Anterior:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$saldo_anterior[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Pre&ccedil;o M&eacute;dio:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$preco[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Vlr Financeiro:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150>";
				echo "&nbsp;".$saldo_anterior[0]*$preco[0];
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<table width=90% border=1 cellspacing=1 cellpadding=1>";

		$saldoatual = $saldo_anterior[0];
		$vlratual = $saldo_anterior[0]*$preco[0];
/*
		$select_total_entrada = "(select pro_codigo, pro_nome, desc_movimentacao, to_char(mov_data, 'dd/mm/yyyy') as mov_data, ite_quantidade as qtde, codsetor,
                                        case when ite_vlrunit is not null then ite_quantidade * ite_vlrunit else coalesce(verifica_preco(271, 99405, mov_data), 0) * ite_quantidade end as vlr,
                                        case when ite_vlrunit is not null then ite_vlrunit else coalesce(verifica_preco(271, 99405, mov_data), 0) end as vlrunitario, sinal, mov_data AS dt_ordem
                                 from v_movimentacao
                                 where sinal = '+'
                                 and mov_data >= '$dt_ini' and mov_data <= '$_GET[dt_final]'
 			                     and pro_codigo = $_GET[pro_codigo]
								 and codsetor = $_GET[centro_estocador]

                                 UNION

                                 SELECT pro_codigo, pro_nome, desc_movimentacao, to_char(mov_data, 'dd/mm/yyyy') AS mov_data, ite_quantidade AS qtde, codsetor,
                                        coalesce(verifica_preco(pro_codigo, codsetor, mov_data), 0) * ite_quantidade AS vlr,
                                        coalesce(verifica_preco(pro_codigo, codsetor, mov_data), 0) AS vlrunitario, sinal, mov_data AS dt_ordem
                                 FROM v_movimentacao
                                 WHERE sinal = '-'
                                 AND mov_data >= '$dt_ini' AND mov_data <= '$_GET[dt_final]'
                     			 AND pro_codigo = $_GET[pro_codigo]
								 AND codsetor = $_GET[centro_estocador] )
                                 ORDER BY 10, 3";
*/

		$select_total_entrada = "(
		SELECT vm.pro_codigo, vm.pro_nome, vm.desc_movimentacao, 
		to_char(vm.mov_data, 'dd/mm/yyyy') AS mov_data, vm.ite_quantidade AS qtde, vm.codsetor, 
		CASE when vm.ite_vlrunit IS NOT NULL THEN ite_quantidade * ite_vlrunit 
		ELSE coalesce(verifica_preco($_GET[pro_codigo], $_GET[centro_estocador], vm.mov_data), 0) * ite_quantidade end as vlr, 
		CASE when vm.ite_vlrunit IS NOT NULL THEN ite_vlrunit ELSE coalesce(verifica_preco($_GET[pro_codigo], $_GET[centro_estocador], vm.mov_data), 0) end AS vlrunitario, 
		vm.sinal, vm.mov_data AS dt_ordem, m.set_entrada, m.set_saida
		FROM v_movimentacao AS vm
		LEFT JOIN movimento AS m ON m.mov_codigo = vm.mov_codigo
		WHERE vm.sinal = '+' AND vm.mov_data >= '$dt_ini' AND vm.mov_data <= '$_GET[dt_final]' 
		AND vm.pro_codigo = $_GET[pro_codigo] AND vm.codsetor = $_GET[centro_estocador] 
		UNION 
		SELECT vm.pro_codigo, vm.pro_nome, vm.desc_movimentacao, 
		to_char(vm.mov_data, 'dd/mm/yyyy') AS mov_data, vm.ite_quantidade AS qtde, vm.codsetor, 
		coalesce(verifica_preco(vm.pro_codigo, vm.codsetor, vm.mov_data), 0) * ite_quantidade AS vlr, 
		coalesce(verifica_preco(vm.pro_codigo, vm.codsetor, vm.mov_data), 0) AS vlrunitario, vm.sinal, 
		vm.mov_data AS dt_ordem, m.set_entrada, m.set_saida
		FROM v_movimentacao AS vm
		LEFT JOIN movimento AS m ON m.mov_codigo = vm.mov_codigo
		WHERE vm.sinal = '-' AND vm.mov_data >= '$dt_ini' AND vm.mov_data <= '$_GET[dt_final]' 
		AND vm.pro_codigo = $_GET[pro_codigo] AND vm.codsetor = $_GET[centro_estocador] 
		) 
		ORDER BY 10, 3";
		$exec_select = pg_query($select_total_entrada);
/*		echo $select_total_entrada;
		echo pg_last_error($db);*/
		/*echo "<pre>";
			print_r($linha);
		echo "</pre><br>";*/
		echo "<tr>";
			echo "<td valign=top style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=345px>";
				echo "<table>";
					echo "<tr bgcolor=F9f9f9>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Tipo ";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Data ";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Quantidade ";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Preco Unitario";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Valor Total";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Saldo Atual";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Valor Atual";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Setor Entrada";
						echo "</th>";
						echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							echo "Setor Saida";
						echo "</th>";

					echo "</tr>";
					while($linha = pg_fetch_array($exec_select))
					{
					    $vlrfmt = formata_valor($linha[vlr]);
						echo "<tr>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha[desc_movimentacao];
   						    echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha[mov_data];
							echo "</td>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha[qtde];
							echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $linha[vlrunitario];
   						    echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $vlrfmt;
						echo "</td>";
							if ($linha[sinal] == '+') {
    							$saldoatual = $saldoatual + $linha[qtde];
    						}
    						else {
    						    $saldoatual = $saldoatual - $linha[qtde];
    						}
   						    echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $saldoatual;
							echo "</td>";
                            if ($linha[sinal] == '+') {
    							$vlratual = $vlratual + $linha[vlr];
    						}
    						else {
    						    $vlratual = $vlratual - $linha[vlr];
    						}
					        $vlratualfmt = formata_valor($vlratual);
   						    echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $vlratualfmt;
							echo "</td>";
							echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							if( ! empty($linha[set_entrada]) )
							{
								$sql_set = pg_query("SELECT set_nome FROM setor WHERE set_codigo = $linha[set_entrada]");
								$row_set = pg_fetch_array($sql_set);
								echo $row_set[0];
							}
							else
							{
								echo "-";
							}
							//echo ($linha[set_entrada] ? $linha[set_entrada] : '-');
							echo "</td>";
							echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
							if( ! empty($linha[set_saida]) )
							{
								$sql_set = pg_query("SELECT set_nome FROM setor WHERE set_codigo = $linha[set_saida]");
								$row_set = pg_fetch_array($sql_set);
								echo $row_set[0];
							}
							else
							{
								echo "-";
							}
							//echo ($linha[set_saida] ? $linha[set_saida] : '-');
							echo "</td>";
						echo "</tr>";
					}
				echo "</table>";
			echo "</td>";

	echo "</table>";
	echo "<table width=90% border=1 cellspacing=2 cellpadding=1>";

		$select_saldo_atual = "select calcula_estoque($_GET[pro_codigo], $_GET[centro_estocador], '$_GET[dt_final]')";
		$saldo = pg_query($select_saldo_atual);
		$saldo_atual = pg_fetch_array($saldo);
		/*echo $select_saldo_anterior;
		echo pg_last_error($db);*/
		$select_preco = "select verifica_preco($_GET[pro_codigo], $_GET[centro_estocador], '$_GET[dt_final]')";
		$pre = pg_query($select_preco);
		$preco = pg_fetch_array($pre);
		/*echo "<br>";
		echo $select_preco;
		echo "<br>".pg_last_error($db);*/

		echo "<tr bgcolor=F9f9f9>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left' width=100px>";
				echo "Saldo Atual:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$saldo_atual[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Pre&ccedil;o M&eacute;dio:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$preco[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Vlr Financeiro:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150>";
				echo "&nbsp;".$saldo_atual[0]*$preco[0];
			echo "</td>";
		echo "</tr>";

	echo "</table>";

?>