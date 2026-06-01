<?
if($usu_codigo != 0){
	$common = new commonClass();
	echo $common->incJquery('../');
	$sql = "select * from alerta where usu_codigo = $usu_codigo";
	$qry = pg_query($sql);
	
	if(pg_num_rows($qry) != 0){
		$common = new commonClass();
		echo $common->openModal('Alerta',400,'');
		//$linha = pg_fetch_array($qry);
		//echo $linha['alerta_desc'];	
		
		while($linha = pg_fetch_array($qry))
		{
			echo "<b>".$linha['alerta_desc']."<br/>";	
			
		}
		echo $common->closeModal();
	}
	
}
?>