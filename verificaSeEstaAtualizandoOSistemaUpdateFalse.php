<?php
	include_once 'global.php';
	include_once $_SESSION[root].$_SESSION[comum].'/library/php/funcoes.db.php'; 
	
	if(getConfig('ATUALIZACAO')){
		$update = "UPDATE conigg SET conf_valor_bool = false where conf_chave = 'ATUALIZACAO'";
		pg_query($update);
		 echo 0;
	}else{
		echo 1;
	}

	
	
?>