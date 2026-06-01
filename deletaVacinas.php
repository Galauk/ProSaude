<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$usuario = $_GET['pac_codigo'];
$pro_cod = $_GET['pro_codigo'];
$dose = $_GET['dose'];
$id = $_GET['id'];
$linha = $_GET['linha'];

$selectIteCodigo = "SELECT * 
							  FROM itens_movimento im
							  JOIN controlefracionado c
							    ON c.ite_codigo = im.ite_codigo
							 WHERE im.pro_codigo = $pro_cod   
							   AND cont_dose > 0";
//die($selectIteCodigo);
$querySelectIte = pg_query($selectIteCodigo);
$resQueryIte = pg_fetch_array($querySelectIte);
$ite_codigo = $resQueryIte['ite_codigo'];
$cont_dose= $resQueryIte['cont_dose'];
$cont_codigo= $resQueryIte['cont_codigo'];


$pegaAcao = " SELECT * 
				FROM vacina_usuario
			   WHERE vac_dose = $dose 
			     AND pro_codigo = $pro_cod 
			     AND usu_codigo = $usuario
			   ORDER BY vac_data DESC 
			   LIMIT 1"; // order by: para deletar somente o reforço mais novo
$exe = pg_query($pegaAcao);
$res = pg_fetch_array($exe);
$acao = $res['vac_acao'];

$sql = "DELETE 
 		  FROM vacina_usuario 
		 WHERE vac_usu_codigo = ".$res['vac_usu_codigo'];

$cons = pg_query($sql);




if($res['vac_acao'] == 'A'){
	$cont_dose_aplicada = $cont_dose + 1;
}else{
	$cont_dose_aplicada = $cont_dose; // CASO A AÇÃO NÂO FOR APLICADA, NÃO DESCONTA A DOSE.
}
$update = "UPDATE controlefracionado 
			  SET cont_dose = $cont_dose_aplicada 
			WHERE ite_codigo = $ite_codigo";
$executaUpdate = pg_query($update);


// Saida desnecessária:
// Ao deletar a vacina, o JS recarrega a carteirinha toda
/*
if ($cons){
	echo $id."_".$cont_dose_aplicada."_".$linha;	
}else{
	echo "false";
}*/

