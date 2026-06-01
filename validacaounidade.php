<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$quantidade = $_GET['quantidade'];
$data = $_GET['data'];
$proc_codigo = $_GET['proc_codigo'];
$med_codigo = $_GET['med_codigo'];
//VALIDAวรO DE CAMPOS
$vagasPorLaboratorio = "SELECT graex_qtde FROM grade_exame
							WHERE graex_data = '$data'
							 AND
							  proc_codigo = $proc_codigo
							 AND
							  med_codigo = $med_codigo";
$exeVagasPorLaboratorio = pg_query($vagasPorLaboratorio);
$resExeVagasPorLaboratorio = pg_fetch_array($exeVagasPorLaboratorio);
$totalDeResultado = $resExeVagasPorLaboratorio['graex_qtde'];

//SOMA DE QUANTIDADE DISTRIBUIDOS POR UNIDADE.
$vagasDistribuidasParaUnidade = "SELECT sum(graexuni_qtde) AS soma
									FROM grade_exame_unidade
									WHERE graexuni_data = '$data'
									 AND
									  proc_codigo = $proc_codigo
									 AND
									  med_codigo = $med_codigo";
$exeVagasDistribuidasParaUnidade = pg_query($vagasDistribuidasParaUnidade);

$resExeVagasDistribuidasParaUnidade = pg_fetch_array($exeVagasDistribuidasParaUnidade);
$totalDeResultadoUnidade = $resExeVagasDistribuidasParaUnidade['soma'];

$somaUnidade = $quantidade + $totalDeResultadoUnidade;
if($somaUnidade > $totalDeResultado)
{
	echo false;
}else{	
	echo true;
}
?>