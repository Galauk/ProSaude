<?
	include_once 'global.php';
	include_once COMUM."/library/php/funcoes.inc.php";
	include_once COMUM."/library/php/funcoes.db.php";
	//echo"<pre>".print_r($_SESSION,1);
	$set_codigo = getSetorByLogon();
	$linha = $_GET['linha'];
	$saldo = "SELECT * 
				FROM saldo 
			   WHERE sal_lote = '$ite_lote'
			     AND set_codigo = $set_codigo
			     AND sal_qtde > 0";
	//die ($saldo);
	$querySaldo = pg_query($saldo);
	$res2 = pg_fetch_array($querySaldo);	
	$qtde = $res2[sal_qtde];
	//echo "aqui bosta".$qtde;
	$set_codigo = $res2[set_codigo];
	
	$select = "SELECT for_codigo,
					  ite_dose 
				 FROM itens_movimento im
				 JOIN movimento m
				   ON m.mov_codigo = im.mov_codigo
				WHERE ite_lote = '$ite_lote'";
	$query = pg_query($select);
	$res = pg_fetch_array($query);	
	$for_codigo = $res[for_codigo];
	$ite_dose = $res[ite_dose];
	
		
	if($descartar == 'D')
	{
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
		
		$update = "UPDATE controlefracionado set
						  cont_dose = 0
			   		WHERE ite_codigo = $ite_codigo";
		$query = pg_query($update);
		
		
		$ite_dose = $resQueryIte[cont_dose];
		$qtde = $qtde - 1;
		echo "0"."_".$ite_lote."_".$qtde."_".$linha."_".$ite_codigo;
		exit;
		
	}
	
	
	$mov_codigo = insereMovimento('S','D', $id_login, $for_codigo,"Aplicação de vacina");	
	if(is_numeric($mov_codigo)){
		
	}else{
		echo"<script>alert('Não foi possivel gerar a movimentação.')<script>";
	}
	$ite_codigo = insereItensMovimento($pro_codigo, 1, $ite_lote, $ite_validade, $mov_codigo,  $id_login,$ite_dose);
	 if(is_numeric($ite_codigo)){
	}else{
		echo"<script>alert('Não foi possivel gerar a movimentação.')<script>";
	}
	
	if(is_numeric($ite_codigo)){
//		if($descartar == 'D')
//		{ 
//			$update = "UPDATE controlefracionado set
//							  cont_dose = 0, 
//				   		   	  ite_dose = $ite_dose
//				   		WHERE ite_codigo = $ite_codigo";
//			//die($update);
//			$query = pg_query($update);
//		}
		
		$sql = "SELECT * 
				  FROM controlefracionado
				 WHERE ite_codigo = $ite_codigo
				   AND set_codigo = $set_codigo";
		$query2 = pg_query($sql);
		
		//$cont_codigo = nextVal('controlevacina_cont_codigo_seq');
		
		if(pg_num_rows($query2) > 0 ){
			$update = "UPDATE controlefracionado set
							  cont_dose = $ite_dose, 
				   		   	  ite_dose = $ite_dose
				   		WHERE ite_codigo = $ite_codigo";
			$query = pg_query($update);
			
		}else{
			$insert= "INSERT INTO controlefracionado 
						(
						 cont_dose,
						 ite_codigo,						 
						 set_codigo)
					  values(
					  	 
						 $ite_dose,
						 $ite_codigo,
						 $set_codigo)";
			//echo $insert;
						 
			 $query = pg_query($insert);
		}
	
	
		
		$qtde = $qtde - 1;
		echo $ite_dose."_".$ite_lote."_".$qtde."_".$linha."_".$ite_codigo;
	}else{
		$insert;
	}
	
?>