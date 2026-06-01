<?php
/**
 * @version     12/07/2007
 * @author      Leandro
 * @brief       Relatorio solicitado pela OS 287
*/
?>
<!-- --------------  Funções javascript  --------------- -->
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
}
tr{
	font-size:12px;
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

//----------------  Monta Dados Recebidos  ---------------->
$pro_codigo	= (int)$_GET["pro_codigo"];
$gru_codigo	= (int)$_GET["gru_codigo"];
$for_codigo	= (int)$_GET["for_codigo"];
$dtIni		= $_GET["dt_ini"];
$dtFin		= $_GET["dt_fim"];

if( $pro_codigo!= -1 )
{
	$sql_produto = "SELECT pro_nome FROM produto WHERE pro_codigo = ".$pro_codigo;
	$Produto = db_get($sql_produto);
	//$and_produto = " AND vm.pro_codigo = ".$pro_codigo;
	$total_produto = 0;
}
else
{
	$Produto = "TODOS";
	$and_produto = '';	
}

if( $gru_codigo!= -1 )
{
	$sql_grupo = "SELECT gru_nome FROM grupo WHERE gru_codigo = ".$gru_codigo;
	$Grupo = db_get($sql_grupo);
	//$and_grupo = " AND p.gru_codigo = ".$gru_codigo;
}
else
{
	$Grupo = "TODOS";
	$and_grupo = '';
}

if( $for_codigo!= -1 )
{
	$sql_fornecedor = "SELECT for_nome FROM fornecedor WHERE for_codigo = ".$for_codigo;
	$Fornecedor = db_get($sql_fornecedor);
	//$and_fornecedor = " AND m.for_codigo = ".$for_codigo; 	
}
else
{
	$Fornecedor = "TODOS";
	$and_fornecedor = '';
}

$titulo="MOVIMENTAÇÃO DE ENTRADA";    //       NOME DO RELATÓRIO
//$dt_final = date("d/m/Y");

//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtIni, $dtFin, $tpCab, $Grupo)
{
	//---------  Cabeçalho do Relatorio  ----------------->
	if ($tpCab == 1)
	{
		include "cabecalho.php";

	}
	//---------  Cabeçalho dos Dados  ----------------->
	if ($tpCab == 0)
	{
		echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";		
		echo " <tr>\n";
		echo "  <td width=200 colspan=1 style=\"font-weight:bold\">Fornecedor</td>\n";
		echo "  <td width=300 colspan=1 style=\"font-weight:bold\">Produto</td>\n";
		echo "  <td width=80 colspan=1 style=\"font-weight:bold\">Data</td>\n";
		echo "  <td width=60 colspan=1 style=\"font-weight:bold\">Quantidade</td>\n";
		echo " </tr>\n";
	}
}

//----------------  Rotina de Impressão  ------------------>

cabeca($titulo, $dtIni, $dtFin, '1', $Grupo);

	/*
	$sql = "SELECT f.for_nome, vm.pro_nome, TO_CHAR(m.mov_data, 'dd/mm/yyyy'), vm.ite_quantidade
	FROM movimento AS m
	LEFT JOIN v_movimentacao AS vm ON vm.mov_codigo = m.mov_codigo
	LEFT JOIN produto AS p ON p.pro_codigo = vm.pro_codigo
	LEFT JOIN fornecedor AS f ON f.for_codigo = m.for_codigo
	WHERE m.mov_tipo = 'E' AND vm.sinal = '+'
	AND m.mov_data BETWEEN '$dtIni' AND '$dtFin'"
	.$and_fornecedor
	.$and_grupo
	.$and_produto."
	ORDER BY 1, 2, 4";
	*/
	$sql = "SELECT f.for_nome, vm.pro_nome, TO_CHAR(m.mov_data, 'dd/mm/yyyy'), vm.ite_quantidade
	FROM movimento AS m
	LEFT JOIN v_movimentacao AS vm ON vm.mov_codigo = m.mov_codigo
	LEFT JOIN produto AS p ON p.pro_codigo = vm.pro_codigo
	LEFT JOIN fornecedor AS f ON f.for_codigo = m.for_codigo
	WHERE m.mov_tipo = 'E' AND vm.sinal = '+'
	AND m.mov_data BETWEEN '$dt_ini' AND '$dt_fim'
	".( $for_codigo != -1 ? "AND m.for_codigo = $for_codigo" : "" )."
	".( $gru_codigo != -1 ? "AND p.gru_codigo = $gru_codigo" : "" )."
	".( $pro_codigo != -1 ? "AND vm.pro_codigo = $pro_codigo" : "" )."
	".( !empty($tipomovim) ? "AND m.mov_entrada = '$tipomovim'" : "" )."
	".( !empty($mov_nr_nota) ? "AND m.mov_nr_nota = $mov_nr_nota" : "" )."
	".( !empty($ce_codigo) ? "AND m.set_entrada = $ce_codigo" : "" )."
	ORDER BY $agrupar, 4";
	
//print "<pre>".$sql."</pre>";

/*	
	".( !empty($for_codigo) ? " AND m.for_codigo = $for_codigo " : "" )."
	".( !empty($por_codigo) ? " AND vm.pro_codigo = $por_codigo " : "" )."
	".( !empty($gru_codigo) ? " AND p.gru_codigo = $gru_codigo " : "" )."
*/	
die($sql);
$query=db_query($sql);

$qtd = pg_num_rows($query);
if ( $qtd == 0 )
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "FORNECEDOR	->".$Fornecedor."<br>";
    echo "PRODUTO	->".$Produto."<br>";
    echo "GRUPO		->".$Grupo."<br>";
    echo "PERÍODO	->".$dtIni." até ".$dtFin."<br>";
}
else
{
	cabeca($titulo, $dtIni, $dtFin, '0');
	$last_fornecedor = '';
	$total = 0;
	while($row=pg_fetch_row($query))
	{
	        $numero = number_format($row[3],0,',','.');  
		echo " <tr>\n";
		echo "  <td>".( $last_fornecedor != $row[0] ? "$row[0]" : "" )."</td>\n";
		echo "  <td>$row[1]</td>\n";
		echo "  <td>$row[2]</td>\n";
		echo "  <td> $numero </td>\n";
		echo " </tr>\n";
		$last_fornecedor = $row[0];
	}
	echo "</table>";
}
?>
