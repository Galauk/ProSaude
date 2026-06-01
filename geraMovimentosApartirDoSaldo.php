<?php
include "global.php";
$acao = 1;
if($acao == 1){
	$sqlPrimeiroScript = " select * 
							   from inventario as i 
							   join inventario_produto as ip 
							     on i.inv_codigo = ip.inv_codigo 
							   join inventario_produto_lote_quantidade as ivpl 
							     on ip.invp_codigo = ivpl.invp_codigo 
							  where i.inv_codigo = 72";
	$queryPrimeiro = pg_query($sqlPrimeiroScript);
	while($registro = pg_fetch_array($queryPrimeiro)){
		$updateSaldo = "UPDATE saldo 
						   SET sal_qtde = $registro[invplq_quantidade_saldo] 
						 WHERE sal_lote = '$registro[invplq_lote]' 
						   AND sal_validade = '$registro[invplq_validade]';";
		echo $updateSaldo."<br/>";
	}
	echo "rodou";
}

if($acao == 2){
	$sqlPrimeiroScript = "select pro_nome,
								 * 
						   from inventario as i 
						   join inventario_produto as ip 
						     on i.inv_codigo = ip.inv_codigo 
						   join inventario_produto_lote_quantidade as ivpl 
						     on ip.invp_codigo = ivpl.invp_codigo 
						   join produto as p
						     on p.pro_codigo = ip.pro_codigo
						  where i.inv_codigo = 72
						     and invplq_quantidade_saldo <> invplq_quantidade";
	$queryPrimeiro = pg_query($sqlPrimeiroScript);
	$cont = 0;
	$cont2 = 0;
	while($registro = pg_fetch_array($queryPrimeiro)){
		$conta = ($registro[invplq_quantidade] - $registro[invplq_quantidade_saldo]);
		
		if($conta > 0){
			if($cont == 0){
				$select = "SELECT nextval('seq_mov_codigo') as proximaentrada";
				$querySeq = pg_query($select);
				$sequencia = pg_fetch_array($querySeq);
				$mov_codigo = $sequencia['proximaentrada'];
				$insertMovEntrada ="INSERT 
									  INTO movimento(mov_codigo,
											 mov_data,
											 mov_tipo,
											 for_codigo,
											 set_entrada,
											 mov_nr_nota,
											 mov_data_inclusao,
											 mov_entrada)
									  VALUES($mov_codigo,
									  		 '2012-05-08',
									  		 'E',
									  		 5003,
									  		 1,
									  		 $mov_codigo,
									  		 '2012-05-08',
									  		 'I');";
				$queryEntrada = pg_query($insertMovEntrada) or die (pg_last_error());
				//echo $insertMovEntrada."<br/>";
				$cont++;
			}
			//echo $registro[pro_nome]."&nbsp;&nbsp;&nbsp;".$conta."<br/>";
			$insertItensMovimento = "insert
									  into itens_movimento(
									  			   mov_codigo,
											       pro_codigo,
											       ite_lote,
											       ite_validade,
											       ite_quantidade,
											       ite_consolidado)	       
											values ($mov_codigo,
													$registro[pro_codigo],
													'$registro[invplq_lote]',
													'$registro[invplq_validade]',
													$conta,
													'S')";
			//echo "<font color=green>INSERT NA ITENS_MOVIMENTO_ENTRADA </font>".$registro[pro_nome]."&nbsp;&nbsp;&nbsp;".$conta."<BR/>";
			$queryItensMovimento = pg_query($insertItensMovimento) or die(pg_last_error());
			//echo $insertItensMovimento."entrada<br/> ";
		}else{
			if($cont2 == 0){
				$select2 = "SELECT nextval('seq_mov_codigo') as proximaentrada";
				$querySeq2 = pg_query($select2);
				$sequencia2 = pg_fetch_array($querySeq2);
				$mov_codigo2 = $sequencia2['proximaentrada'];
				$insertMovSaida = " INSERT 
									  INTO movimento(mov_codigo,
											 mov_data,
											 mov_tipo,
											 for_codigo,
											 set_saida,
											 mov_nr_nota,
											 mov_data_inclusao,
											 mov_entrada)
									  VALUES($mov_codigo2,
									  		 '2012-05-08',
									  		 'S',
									  		 5003,
									  		 1,
									  		 $mov_codigo2,
									  		 '2012-05-08',
									  		 'I');";
				$queryEntrada = pg_query($insertMovSaida)or die (pg_last_error());
				//echo $insertMovSaida."$mov_codigo2 <br/> ";
				$cont2++;
			}
			
			$arredonda = ($conta * -1);
			
			$insertItensMovimentoSaida = "insert
									  into itens_movimento(
									  			   mov_codigo,
											       pro_codigo,
											       ite_lote,
											       ite_validade,
											       ite_quantidade,
											       ite_consolidado)	       
											values ($mov_codigo2,
													$registro[pro_codigo],
													'$registro[invplq_lote]',
													'$registro[invplq_validade]',
													$arredonda,
													'S')";
			$queryItensMovimentoSaida = pg_query($insertItensMovimentoSaida) or die(pg_last_error());									
			//echo "<font color=red>INSERT NA ITENS_MOVIMENTO_SAIDA </font>".$registro[pro_nome]."&nbsp;&nbsp;&nbsp;".$conta."<BR/>";
			//echo $insertItensMovimentoSaida."saida<br/>  ";
			
		}
		
	}
	echo "rodou2";
}
?>