<?php
set_time_limit("10000000000000000000");
// Conexão Banco de dados
$db = pg_connect("host='".$host_correto."' port='".$porta_correto."' dbname='".$banco_correto."' user='".$usuario_correto."' password='".$senha_correto."'") or die ("Não foi possivel conectar ao servidor");
pg_query("SET CLIENT_ENCODING=UTF8");
$i = 0;
foreach($_POST as $ind => $val){
	$exp_val = "";
	$exp = "";
	//$i++; teste para forçar um erro no meio da execução para testar o commit e o rollback
	//echo "<pre>".print_r($_POST,1);
	$exp = explode("|",$ind);
	pg_query("BEGIN;");
	if(!is_numeric($val[0])){
		
		if($exp[0] == "teste"){
			$exp_val = explode("|",$val[0]);
			
			if($i == 2){
				$gru_codigo = "";
			}else{
				$gru_codigo = 99482;
			}
			echo $exp_val[0]."<br/>";
			$sql = "insert  into produto (pro_nome,
										  pro_saida,
										  pro_entrada,
										  pro_dispensacao,
										  pro_transferencia,
										  pro_tipo,
										  umed_codigo,
										  pro_situacao,
										  pro_validade,
										  gru_codigo,
										  pro_horus)
									   VALUES (retira_acentos('$exp_val[0]'),
											   'S',
											   'S',
											   'S',
											   'S',
											   'M',
											   '$exp_val[1]',
											   'A',
											   'S',
											   $gru_codigo,
											   '".str_replace("_","",$exp[1])."');";
		}
	}else{
		$exp_ind = explode("|",$ind);
		$sql = "UPDATE produto SET pro_horus = '".str_replace("_","",$exp_ind[1])."' WHERE pro_codigo = $val[0];";
		echo $sql."<br/>";
	}
	if($sql != ""){
		if(pg_query(utf8_encode($sql)) or die(pg_last_error())){
			$error = 1;
		}else{
			$error = 2;
			$sqlError = $sql;
			pg_last_error();
			break;
		}
	}
}
if($error == 2){
	pg_query("ROLLBACK;");
	echo "Ocorreu um erro na execução";
	echo $sqlError;
}else{
	pg_query("COMMIT;");
}