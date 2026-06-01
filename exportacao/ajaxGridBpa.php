<?php
	require_once '../global.php';
	//setError(1);
	
	if(!function_exists("json_encode")){
		include COMUM."/library/php/JSON.php";
		function json_encode($data) {
       		$json = new Services_JSON();
       		return( $json->encode($data) );
    	}
	}
	
	 $page = $_GET['page']; 
	 $limit = $_GET['rows']; 
	 $sidx = $_GET['sidx']; 
	 $sord = $_GET['sord']; 
	 
 	 $data1 = $_GET['data_inicial'];
 	 $data2 = $_GET['data_final'];
 	 $local = $_GET['uni_codigo'];
 	 $local = explode("|", $local);

 	 if($local[1] == 0){
 	 	$med_codigo = $local[0];
 	 	$cond_result = "bpa.med_codigo='$med_codigo'";
 	 }else if($local[1] == 1){
 	 	$uni_codigo = $local[0];
 	 	$cond_result = "bpa.uni_codigo='$uni_codigo'";
 	 }
 	 
	 $sqlResult = "SELECT COUNT(*) AS count 
	                       FROM bpa 
	                      WHERE $cond_result
		                    AND bpa_ativo='t'
		           	        AND bpa.bpa_data BETWEEN '$data1' AND '$data2'";

	 $result = pg_query($sqlResult);
	 $row = pg_fetch_array($result); 
	 $count = $row['count']; 
	 
	 if( $count >0 ) { 
	 	$total_pages = ceil($count/$limit); 
	 	
	 } else {
	 	$total_pages = 0; 
	 } 
	 
	 if ($page > $total_pages) 
	 	 $page=$total_pages; 
	 	 
	 $start = $limit*$page - $limit; 
	 if($start < 0) 
	 	$start = 10000;
	 	 
	 	if($local[1] == 0){
			$cols = "med_nome";
			$join = "JOIN medico AS med 
					    ON med.med_codigo=bpa.med_codigo";
			$cond_sql = "bpa.med_codigo='$med_codigo'";

		}else if($local[1] == 1){
			$cols = "uni_desc";
			$join = "JOIN unidade AS uni 
					    ON uni.uni_codigo=bpa.uni_codigo";
			$cond_sql = "bpa.uni_codigo='$uni_codigo'";
		}
		
	 	 
	 	 $sql = "   SELECT usr.usr_nome,
					       usu.usu_nome,
					       TO_CHAR(bpa.bpa_data,'DD/MM/YYYY') as bpa_data,
					       proc.proc_nome,
					       bpa.bpa_autorizacao,
					       cd10.cd10_codigo_cid,
					       MIN(esp.esp_nome) as esp_nome,
					       proc.proc_codigo_sus,
					       bpa.bpa_status_inconsistencia,
					       $cols,
					       bpa.bpa_codigo,
					       CASE WHEN rlpr.co_registro = 2 THEN 'I'
					       ELSE 'C'
					       END AS bpa_tipo
					  FROM bpa
				      LEFT JOIN procedimento AS proc 
					    ON proc.proc_codigo=bpa.proc_codigo 
					  JOIN rl_procedimento_registro AS rlpr
					    ON rlpr.co_procedimento=proc.proc_codigo_sus    
					   AND rlpr.co_registro IN (1,2) 
					  $join
				 LEFT JOIN usuarios AS usr
					    ON usr.usr_codigo=bpa.usr_codigo
				 LEFT JOIN medico_especialidade AS mes 
					    ON mes.med_codigo=usr.usr_codigo 
				 LEFT JOIN especialidade AS esp 
				        ON esp.esp_codigo=mes.esp_codigo 
				 LEFT JOIN rl_procedimento_ocupacao AS rlpo 
					    ON rlpo.co_procedimento=proc.proc_codigo_sus
					   AND esp.cod_cbo=rlpo.co_ocupacao
					  JOIN usuario AS usu
					    ON usu.usu_codigo=bpa.usu_codigo
					  
				 LEFT JOIN cid10 AS cd10
				        ON cd10.cd10_codigo=bpa.bpa_cd10_codigo
		             WHERE $cond_sql
		               AND bpa_ativo='t'
		           	   AND bpa.bpa_data BETWEEN '$data1' AND '$data2'
		           	 GROUP BY usr.usr_nome,
					       usu.usu_nome,
					       TO_CHAR(bpa.bpa_data,'DD/MM/YYYY'),
					       proc.proc_nome,
					       bpa.bpa_autorizacao,
					       rlpr.co_registro,
					       cd10.cd10_codigo_cid,
					       proc.proc_codigo_sus,
					       bpa.bpa_status_inconsistencia,
					       $cols,
					       bpa.bpa_codigo
		           	 ORDER BY bpa_data,bpa_tipo
		           	 LIMIT $limit OFFSET $start";
					  
	 	// fdebug($sql);
	 	//die($sql);
	 	 $result = pg_query( $sql ) or die("Couldn t execute query.".pg_last_error()); 
	 	 $responce->page = $page; 
	 	 $responce->total = $total_pages; 
	 	 $responce->records = $count; $i=0;
	 	 while($row = pg_fetch_array($result)){ 
			$responce->rows[$i]['id']=$row['bpa_codigo'];
	 	 	$responce->rows[$i]['cell']=array($row['proc_codigo_sus'], utf8_encode( trim($row['proc_nome']) ) ,utf8_encode( $row['usu_nome'] ),$row['cd10_codigo_cid'],$row['bpa_status_inconsistencia'],($row[bpa_tipo] == "C" ? "Consolidado" : "Individualizado")); 
	 	 	$i++; 
	 	 }
		
	 	 	 echo json_encode($responce);