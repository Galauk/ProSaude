<?php
	include_once 'global.php';
	include_once $_SESSION[root].$_SESSION[comum].'/library/php/funcoes.db.php'; 
	
	if(getConfig('ATUALIZACAO')){
		 echo 1;
	}else{
		$update = "UPDATE conigg SET conf_valor_bool = true where conf_chave = 'ATUALIZACAO'";
		pg_query($update);
		echo 0;
	}
	

	
	
?>