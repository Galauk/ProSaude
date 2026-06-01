<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$uni_codigo = $_GET['uni_codigo'];
	$proc_codigo = $_GET['proc_codigo'];
	$periodo = $_GET['periodo'];
	$mesano = mesAno($periodo);

	$sql = "select count(graexuni_qtde) as cont,
					  graexuni_qtde
				 from grade_exame_unidade
				where to_char(graexuni_data, 'mm/yyyy') = '$mesano'
				  and uni_codigo = '$uni_codigo'
				and proc_codigo  = $proc_codigo
				group by graexuni_qtde
				order by cont desc";
		$exe_sql = pg_query($sql);
		$res_exe_sql = pg_fetch_array($exe_sql);
		$qtde_uni_banco = $res_exe_sql["graexuni_qtde"];
	if($qtde_uni_banco != ""){
		echo $qtde_uni_banco;
	}else{
		echo "0";
	}
?>