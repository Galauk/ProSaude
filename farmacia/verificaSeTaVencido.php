<?php

	@header('Content-Type: text/html; charset=ISO-8859-1');
	require_once '../global.php';
	require_once COMUM ."/library/php/funcoes.db.php";
	$set_codigo = getSetorByLogon();
	
	$pro_codigo = $_REQUEST['pro_codigo'];
	$out = array();
	$sql = "SELECT *,
				   to_char(sal_validade,'DD/MM/YYYY') AS validade 
			  FROM saldo s
			  JOIN produto p
			    ON p.pro_codigo = s.pro_codigo
			 WHERE s.pro_codigo = $pro_codigo
			   AND sal_validade < NOW()
			   AND sal_qtde > 0
			   AND set_codigo = $set_codigo";
	
	$query = pg_query($sql);
	if(pg_num_rows($query) >= 1){
		$i = 0;
		$table = "<table border=0 style=\"\">";
			$table .= "<tr>";
				$table .= "<th width=160>Produto</th>";
				$table .= "<th width=100>Quantidade</th>";
				$table .= "<th width=100>Lote</th>";
				$table .= "<th width=100>Validade</th>";
			$table .= "</tr>";
		while($reg = pg_fetch_array($query)){
			$table .= "<tr>";
				$table .= "<td align=center>$reg[pro_nome]</td>";
				$table .= "<td align=center>$reg[sal_qtde]</td>";
				$table .= "<td align=center>$reg[sal_lote]</td>";
				$table .= "<td align=center>$reg[validade]</td>";
			$table .= "</tr>";
			//$out[$i] = array("pro_codigo"=>$reg[pro_codigo],"pro_nome"=>$reg[pro_nome],"sal_qtde"=>$reg[sal_qtde],"sal_validade"=>$reg[validade]);
			//$i++;
		}
		$table .= "</table>";
		echo $table;
	}else{
		echo "";
	}