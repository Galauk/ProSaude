<?php
session_start();

require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//echo "<pre>".print_r($_REQUEST,true)."</pre>";

$selectIteCodigo = "SELECT * 
							  FROM itens_movimento im
							  JOIN controlefracionado c
							    ON c.ite_codigo = im.ite_codigo
							 WHERE im.pro_codigo = $pro_codigo   
							   AND cont_dose > 0";
		//die($selectIteCodigo);
		$querySelectIte = pg_query($selectIteCodigo);
		$resQueryIte = pg_fetch_array($querySelectIte);
		$ite_codigo = $resQueryIte['ite_codigo'];
		$cont_dose= $resQueryIte['cont_dose'];
		$cont_codigo = $resQueryIte['cont_codigo'];
if($cont_dose == 0 && $resposta == 'A'){
	//echo "1";
		echo "1";
	
}else{
	echo $id."|".$resposta;
}

?>