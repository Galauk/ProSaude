<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$mlz_datadacoleta = $_GET['mlz_datadacoleta'];
$mlz_quantidade = $_GET['mlz_quantidade'];
$med_codigo = $_GET['med_codigo'];
$checkout = $_GET['checkout'];
$labm_codigo = $_GET['labm_codigo'];
$id_login = $_GET['id_login'];
$cad_exame = $_GET['cad_exame'];
$itx_codigo = explode("|",$checkout);
$usu_codigo = $_GET['usu_codigo'];
foreach($itx_codigo as $itx){
	$sqlVerifica = pg_query("select * from materialdeanalise where itx_codigo = $itx");
	$linhasVerifica = pg_num_rows($sqlVerifica);
	if($linhasVerifica > 0 ){
		
	}else{
		if($itx != "" || $itx != null){
		$stmt = "INSERT INTO materialdeanalise( 
													 itx_codigo, 
													 mlz_coletado, 
													 id_login, 
													 labm_codigo, 
													 cad_exame, 
													 mlz_datadacoleta, 
													 mlz_bioquimico
										) VALUES (  
													 $itx, 
													 '$mlz_coletado', 
													 $id_login, 
													 $labm_codigo, 
													 $cad_exame, 
													 '$mlz_datadacoleta',  
													 '$mlz_bioquimico')";
		//die($stmt);
		 $qry = pg_query($stmt);
		}
	}
	
	$usr_codigo = $_SESSION['id_login'];
	$sqlVerificaStatus = "select * from itensdoexame WHERE cad_exame = $cad_exame ";
	
	$queryVerificaStatus = pg_query($sqlVerificaStatus);
	while($unaLinha = pg_fetch_array($queryVerificaStatus)){
		//echo $unaLinha["itx_codigo"]."-".$itx;exit;
		if($unaLinha["itx_codigo"] == $itx){
			$alterStatus = "UPDATE itensdoexame 
			                   SET itx_status = 'C',
			                       usr_codigo = $usr_codigo,
			                       itx_data = NOW() 
			                 WHERE itx_codigo = $itx";
			$qryAltera = pg_query($alterStatus);
			
			
			$filter = "n";
		}else{
			if($filter == "n"){
				
			}else{
				$alterStatus = "UPDATE itensdoexame 
				                   SET itx_status = ' ',
			                       usr_codigo = $usr_codigo,
			                       itx_data = NOW()
			                 WHERE itx_codigo = {$unaLinha['itx_codigo']}";
		 		die($alterStatus);
				$qryAltera = pg_query($alterStatus);		 		

			}
		}
	}
}

$sql = "select * from materialdeanalise where cad_exame = $cad_exame";
$exec = pg_query($sql);
$numLinhas = pg_num_rows($exec);
if ($numLinhas > 0){
	//o = OK deu certo entao vai para o txt fazer a validação
	echo $usu_codigo."|o";
}else{
	echo $usu_codigo."|e";
	//erro
}
?>