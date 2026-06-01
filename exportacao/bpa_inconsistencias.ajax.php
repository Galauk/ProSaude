<?php
	require_once '../global.php';
	setError(1);
	
	if(!function_exists("json_encode")){
		include COMUM."/library/php/JSON.php";
		function json_encode($data) {
       		$json = new Services_JSON();
       		return( $json->encode($data) );
    	}
	}
	
	 $page = $_GET['page']||1; 
	 $limit = isset($_GET['rows'])?$_GET['rows']:10;  
	 $sidx = $_GET['sidx']||1;  
	 $sord = isset($_GET['sord'])?$_GET['sord']:"ASC"; 
	 
	 $bpa_codigo = $_GET['id'];
	 
	 $result = pg_query("SELECT COUNT(*) AS count FROM rl_bpa_inconsistencia"); 
	 $row = pg_fetch_array($result); 
	 $count = $row['count']; 
	 
	 if( $count > 0 ) { 
	 	$total_pages = ceil($count/$limit); 
	 	
	 } else { $total_pages = 0; } 
	 
	 if ($page > $total_pages) 
	 	 $page = $total_pages; 
	 	 
 	 $start = $limit*$page - $limit; 
 	 $sql = "   SELECT i.bpai_codigo,
 	 				   bpai_descricao
				  FROM bpa_inconsistencias AS i
				  JOIN rl_bpa_inconsistencia AS rl
				    ON rl.bpai_codigo=i.bpai_codigo
				 WHERE rl.bpa_codigo=$bpa_codigo";
 	 
 	 $result = pg_query( $sql ) or die("Couldn t execute query.".pg_last_error()); 
 	 $responce->page = $page; 
 	 $responce->total = $total_pages; 
 	 $responce->records = $count; 
 	 $i=0;
 	 while($row = pg_fetch_array($result)){ 
		$responce->rows[$i]['id']=$row['bpai_codigo'];
 	 	$responce->rows[$i]['cell']=array($row['bpai_descricao']); 
 	 	$i++; 
 	 }
 	 
 	 if(!$i){
		$responce->rows[$i]['id']=0;
 	 	$responce->rows[$i]['cell']=array(utf8_encode("<em>Nenhuma inconsistência encontrada.</em>"));  	 	
 	 }

 	 echo json_encode($responce);