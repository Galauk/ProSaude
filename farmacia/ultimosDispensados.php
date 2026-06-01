<?php
	include "../global.php";
	setError(1);
	include_once $_SESSION[root].$_SESSION[comum].'/library/php/funcoes.db.php'; // getConfig;
	$dias = getConfig('FARMACIA_TEMPO_HISTORICO');
	$usu_codigo = $_GET["usu_codigo"];
	$sqlHistorico = " SELECT p.pro_nome,
							 TO_CHAR(m.mov_data,'DD/MM/YYYY') AS data,
							 TO_CHAR(m.mov_data + 5,'DD/MM/YYYY')  AS duracao
						FROM movimento AS m
						JOIN itens_movimento AS im
						  ON m.mov_codigo = im.mov_codigo
						JOIN produto AS p
						  ON p.pro_codigo = im.pro_codigo
					   WHERE usu_codigo = $usu_codigo
					     AND mov_data > CURRENT_DATE - $dias";
	$queryHistorico = pg_query($sqlHistorico);
	$numLinhas = pg_num_rows($queryHistorico);
	if($numLinhas > 0){
		echo $dias;
	}else{
		echo "-";
	}
	