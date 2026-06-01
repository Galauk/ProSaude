<?php
    set_time_limit(100000000000);
    // ARRUME A CONEXÃO DO BANCO 
	$dbconn3 = pg_connect("host=177.55.33.18 port=5432 dbname=dbsocial user=postgres password=sIMkj59zr");
    ini_set('display_errors', 'On');
    
    $sql = "SELECT * FROM SALDO WHERE set_codigo = '1'";
    $query = pg_query($sql);
    $num_reg = 0;
    while ($row = pg_fetch_array($query)) {
		$diferenca = 0;
		$novoSaldo = 0;
		$saldoMov = 0;
		$saldo = 0;
		
		$sqlSaldoMov = "SELECT 
							(SELECT 
								CAST(COALESCE(SUM(ite_quantidade),0) AS integer)
							FROM 
								produto AS p2
							INNER JOIN 
								itens_movimento AS ite ON ite.pro_codigo = p2.pro_codigo
							INNER JOIN 
								movimento AS mov ON mov.mov_codigo = ite.mov_codigo
							WHERE
								(p2.pro_codigo = ".$row["pro_codigo"].") AND 
								(mov.mov_tipo = 'E' or mov.mov_tipo = 'T') AND 
								(mov.set_entrada = '".$row["set_codigo"]."') AND
								(ite.ite_lote = '".$row["sal_lote"]."') AND
								(ite.ite_validade = '".$row["sal_validade"]."')) -
							(SELECT 
								CAST(COALESCE(SUM(ite_quantidade),0) AS integer)
							 FROM 
								produto AS p2
							 JOIN 
								itens_movimento AS ite ON ite.pro_codigo = p2.pro_codigo
							 JOIN 
								movimento AS mov ON mov.mov_codigo = ite.mov_codigo
							 WHERE
								(p2.pro_codigo = ".$row["pro_codigo"].") AND 
								(mov.mov_tipo = 'S' or mov.mov_tipo = 'T') AND 
								(mov.set_saida = '".$row["set_codigo"]."') AND
								(ite.ite_lote = '".$row["sal_lote"]."') AND
								(ite.ite_validade = '".$row["sal_validade"]."')) as saldo_movimentacoes
							FROM
								produto
							WHERE 
								pro_codigo = '".$row["pro_codigo"]."'";
		$querySaldoMov = pg_query($sqlSaldoMov);
		$rowSaldoMov = pg_fetch_array($querySaldoMov);
		$saldoMov = $rowSaldoMov["saldo_movimentacoes"];
		
		$sqlSaldo = "SELECT 
						COALESCE(SUM(sal_qtde),0) AS saldo 
					FROM 
						saldo 
					WHERE 
						pro_codigo = ".$row["pro_codigo"]." AND 
						sal_lote = '".$row["sal_lote"]."' AND 
						sal_validade = '".$row["sal_validade"]."' AND 
						set_codigo = '".$row["set_codigo"]."'";
        $querySaldo = pg_query($sqlSaldo);
        $rowSaldo = pg_fetch_array($querySaldo);
		$saldo = $rowSaldo["saldo"];
		
		$diferenca = $saldoMov - $saldo;
		
		
		if ($diferenca != 0) {
			$novoSaldo = $saldoMov;
			// CONFERE SE DEU CERTO PRIMEIRO DE EXECUTAR, AI DESCOMENTA AS LINHAS
			//$sqlAtuSaldo = "UPDATE saldo SET sal_qtde = '$novoSaldo' WHERE sal_codigo = '".$row["sal_codigo"]."'"; 
			//$queryAtuSaldo = pg_query($sqlAtuSaldo);
			//if ($queryAtuSaldo) {
				echo "SalCodigo: ".$row["sal_codigo"]." Produto: ".$row["pro_codigo"]." - Lote: ".$row["sal_lote"]." - Validade: ".$row["sal_validade"]." - Setor: ".$row["set_codigo"]."<br />";
				echo "Total de itens: ".$rowSaldoMov["saldo_movimentacoes"]." Saldo: ".$rowSaldo["saldo"]."<br />";
				echo "NovoSaldo".$novoSaldo."<br /><br />";
			//}
		}
		//echo "aaaa";
		
    }
    //echo "O número de itens inserido foi de: ".$num_reg."<br />";
?>
