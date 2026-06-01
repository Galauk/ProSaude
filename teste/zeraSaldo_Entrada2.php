<?php
include "../global.php";
$sqlSetor = "select distinct set_codigo from saldo and set_codigo <> 118";
$querySetor = pg_query($sqlSetor);
//pg_query("BEGIN");
while($reg_setor = pg_fetch_array($querySetor)){

	$sql =  " select distinct pro_codigo 
				from saldo 
			   where sal_qtde < 0
			   and set_codigo = $reg_setor[set_codigo]";
	$query = pg_query($sql) or die(pg_last_error()."a");
	$i=0;
	$select = "SELECT nextval('seq_mov_codigo') as proximaentrada";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	$mov_codigo = $linha['proximaentrada'];
	
	
	$insert = "insert into movimento(mov_codigo,mov_data,mov_tipo,mov_entrada,mov_observacao,set_entrada) VALUES ($mov_codigo,now(),'E','E','Colocando entradas para ajustar balanço',$reg_setor[set_codigo]);";
	//echo $insert."<br/>";
	$queryInsert = pg_query($insert);
	$j = 0;
	while($reg = pg_fetch_array($query)){
		$sqlSaldoProduto = "select * from saldo where pro_codigo = $reg[pro_codigo] and sal_qtde < 0 and set_codigo = $reg_setor[set_codigo]";
		$queryProduto = pg_query($sqlSaldoProduto);
		while($regSaldoProduto = pg_fetch_array($queryProduto)){
		    $quantidade = $regSaldoProduto[sal_qtde] * -1;
			$sqlItens = "insert into itens_movimento(mov_codigo,
													 pro_codigo,
													 ite_quantidade,
													 ite_lote,
													 ite_validade)
											  VALUES ($mov_codigo,
													  $reg[pro_codigo],
													  $quantidade,
													  '$regSaldoProduto[sal_lote]',
													  '$regSaldoProduto[sal_validade]');";
			$queryItens = pg_query($sqlItens);
			//echo $sqlItens."<br/>";
		}
	}
}

//echo "deu certo";