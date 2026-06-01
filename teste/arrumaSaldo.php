<?php
	include "../global.php";
	
	$sql =  "SELECT DISTINCT pro.pro_nome, 
				(SELECT SUM(ite_quantidade) 
				   FROM produto p2 
				   JOIN itens_movimento i2 
				     ON i2.pro_codigo = p2.pro_codigo 
				   JOIN movimento m2 
				     ON m2.mov_codigo = i2.mov_codigo 
				  WHERE 1=1 
				    
				   
				    AND (mov_tipo = 'E' or mov_tipo = 'T') 
				    AND set_entrada = 1 
				    AND pro.pro_codigo = p2.pro_codigo) as entrada, 
			        (SELECT SUM(ite_quantidade) 
			           FROM movimento m2 
			           JOIN itens_movimento i2 
			             ON m2.mov_codigo =i2.mov_codigo 
			           JOIN usuario u2 
			             ON u2.usu_codigo = m2.usu_codigo 
			           JOIN produto p2 
			             ON p2.pro_codigo = i2.pro_codigo 
			          WHERE 1=1 
			            AND u2.usu_codigo is not null 
			           
			            AND (mov_tipo = 'S') 
			            AND set_saida = 1 
			            AND pro.pro_codigo = p2.pro_codigo) as saida, 
			        (SELECT sum(sal_qtde) 
			           FROM saldo 
			          WHERE pro_codigo = pro.pro_codigo 
			            AND set_codigo = 1 
			            AND sal_validade > 'NOW()') as saldo, 
			        (SELECT sum(sal_qtde) 
			           FROM saldo 
			          WHERE pro_codigo = pro.pro_codigo 
			            AND set_codigo = 1 
			            ) as perda
			            ,ite.pro_codigo
			   FROM produto AS pro 
			   LEFT JOIN itens_movimento AS ite 
			     ON ite.pro_codigo=pro.pro_codigo 
			   LEFT JOIN movimento AS mov 
			     ON mov.mov_codigo=ite.mov_codigo 
			   LEFT JOIN fornecedor AS f 
			     ON f.for_codigo=mov.for_codigo 
			   LEFT JOIN setor AS set 
			     ON set.set_codigo=mov.set_entrada 
			  WHERE (mov.set_entrada= 1 OR mov.set_saida = 1)
			  
			  GROUP BY for_nome, pro_nome, for_cnpj, set.set_codigo, pro.pro_codigo, mov_data ,ite.pro_codigo
			  ORDER BY pro_nome ASC";
	$query = pg_query($sql) or die(pg_last_error()."a");
	$i=0;
	$select = "SELECT nextval('seq_mov_codigo') as proximaentrada";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	$mov_codigo = $linha['proximaentrada'];
	$insert = "insert into movimento(mov_codigo,mov_data,mov_tipo,for_codigo,mov_observacao,set_entrada) VALUES ($mov_codigo,now(),'E',5003,'Colocando Entradas para ajustar balanço',1);";
	$queryInsert = pg_query($insert) or die(pg_last_error());
	$j = 0;
	while($reg = pg_fetch_array($query)){
		$j++;
		$entrada = $reg[entrada];
		$saida = $reg[saida];
		$saldo = $reg[saldo];
		//if($saida > $entrada){
		$i++;
		$resultSoma = ($saida + $saldo);
		$subtracaoDaEntrada = $resultSoma - $entrada; 
		//echo $subtracaoDaEntrada."<br/>";
		//echo $saida."-".$saldo."=".$resultSubt."<br/><br/>";
		$insertItens = "insert into itens_movimento (mov_codigo,pro_codigo,ite_lote,ite_validade,ite_status,ite_quantidade) values ($mov_codigo,$reg[pro_codigo],'ARRUMA_ESTOQUE','NOW()','A',$subtracaoDaEntrada)";
		$queryItens = pg_query($insertItens) or die('x'.pg_last_error());
		//}
	}
	echo $i;
	echo "<br/>".$j;