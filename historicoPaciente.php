<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$usu_codigo = $_GET[codigo];
	$usu_nome = $_GET[nome];
	
	$sqlDispensacoes = "select m.mov_codigo, 
						       TO_CHAR(m.mov_data,'DD/MM/YYYY') AS data, 
						       u.usu_nome, 
						       s.set_nome, 
						       im.pro_codigo, 
						       p.pro_nome, 
						       im.ite_quantidade, 
						       im.ite_vlrtotal,
						       u.usu_codigo,
						       * 
						  FROM movimento as m 
						  JOIN itens_movimento as im 
						    ON m.mov_codigo = im.mov_codigo 
						  JOIN setor as s 
						    ON s.set_codigo = m.set_saida 
						  JOIN usuario as u 
						    ON u.usu_codigo = m.usu_codigo 
						  JOIN produto as p 
						    ON p.pro_codigo = im.pro_codigo 
						 WHERE u.usu_codigo = $usu_codigo
						   AND mov_data > CURRENT_DATE - 30";
	$queryDispencacoes = pg_query($sqlDispensacoes);
	$numRows = pg_num_rows($queryDispencacoes);
	
	if($numRows > 0){
		$msg = "O paciente $usu_nome retirou os medicamentos: ";
		while($reg = pg_fetch_array($queryDispencacoes)){
			$msg .= "\n".$reg[pro_nome]."-".$reg[data];
		}
	} else {
		$msg = "F";
	}
	
	if(!pg_num_rows($queryDispencacoes)){
		die("0"); // n�o houve dispensa��es nos ultimos 30 dias
	}
	
	$msg = "O paciente $usu_nome retirou os medicamentos: ";
	
	while($reg = pg_fetch_array($queryDispencacoes)){
		$msg .= $reg[pro_nome].",    ";
		
	}
	echo $msg;
	