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
		            ite.pro_codigo
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
	$j = 0;
	while($reg = pg_fetch_array($query)){
		$entrada = $reg[entrada];
		$saida = $reg[saida];
		$saldo = $reg[saldo];
		
		$resultado = ($entrada - $saida);
		if($resultado != $saldo){
			$i++;
			if($resultado > $saldo){
				$j++;
			}
			//echo $reg[pro_codigo]."<br/>";
		}
		
	}
	echo "<br/>".$i."<br/>".$j;
