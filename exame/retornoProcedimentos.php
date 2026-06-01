<?php
	include "../global.php";
	
	$sqlProcedimentos = "SELECT proc_nome,
								p.proc_codigo
						   FROM procedimento AS p 
						   JOIN tipodeexame AS t 
						     ON p.proc_codigo = t.proc_codigo 
						  ORDER BY p.proc_nome";
	$queryProcedimentos = pg_query($sqlProcedimentos) or die (pg_last_error());
	
	$responce->records = pg_num_rows($queryProcedimentos); 
	$i=0;
	
	while($row = pg_fetch_array($queryProcedimentos)){ 
		$responce->rows[$i]['id']=$row['proc_codigo'];
		
		$sqlUnidade = "select COALESCE(graexuni_qtde,0) total
		   				 from unidade as u 
		   			LEFT join grade_exame_unidade as geu
		   				   on u.uni_codigo = geu.uni_codigo
		   				AND proc_codigo = {$row['proc_codigo']}
		   				order by uni_desc";
		$queryUnidade = pg_query($sqlUnidade);
		$linha = array($row['proc_nome']);
		while($reg = pg_fetch_array($queryUnidade)){
			 $linha []= $reg['total'];
		}
		//echo "<pre>".print_r($linha,1);exit();
		$responce->rows[$i]['cell']= $linha;
		$i++; 
	}
	
	echo json_encode($responce);