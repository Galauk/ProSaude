<?php	
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$produto_cod = $_POST["pro_codigo"];
	$movimento_cod = $_POST["mov_codigo"];
	$sql_usu = "SELECT usu_codigo FROM movimento WHERE mov_codigo = $movimento_cod";
	$res_usu = pg_query($sql_usu);
	$row_usu = pg_fetch_array($res_usu);
	$mov_codigo = $_GET['mov_codigo'];
	$pro_codigo = $_GET['pro_codigo'];
	$ite_status = $_GET['ite_status'];
	$quantidadeSolicitada = $_GET['ite_quantidade'];
	$ite_consolidado = $_GET['ite_consolidado'];
	$quantidade_dia_soli = $_GET['ite_qtde_solicitada'];
	$ite_posologia = $_GET['ite_posologia'];
	$ite_detalhes_tratamento = $_GET['ite_detalhes_tratamento'];
	$ite_observacoes = $_GET['ite_observacoes'];
	$quantidade_dia = $_GET['quantidade_dia'];
	
	
	
	$resposta = explode(",", $_GET[resp]);
	    
	 	for($i=0; $i<$resposta[$i].length; $i++)
	 	{		
	 		//echo $resposta[$i] ."<br>";
	 		 $loteValidadeQuantidade = explode("|", $resposta[$i]);
	 		 $quantidade = $loteValidadeQuantidade[0];
	 		 $lote = $loteValidadeQuantidade[1];
	 		 $validade= $loteValidadeQuantidade[2];
	 		 echo $quantidade."<br>";
	 		 
	 		/* echo $lote."<br>";
	 		 echo $validade."<br>";*/
	 		$solicitada = $quantidadeSolicitada;
	 		if ($quantidade < $quantidadeSolicitada) {
	 		 	
	 		 	$q[$i] = $quantidade;
	 		 	$quantidadeSolicitada -= $quantidade; 		 	
	 		}
	 	 	else if ($quantidade >= $quantidadeSolicitada) {
	 	 	 	$q[$i] = $quantidadeSolicitada;
	 	 	}	 	 	
			$v[$i] = $validade;
			$l[$i] = $lote;
	 	}	 
	
	$select = "select * from itens_movimento where mov_codigo = $mov_codigo and pro_codigo = $pro_codigo";
	
	//$exec_select = pg_query($select) or die ("ERRO ".$select);

		
	if(pg_num_rows($exec_select) == 0)
	{
		
 		$resposta = explode(",", $_GET[resp]);
	    

	for($i=0; $i<count($q); $i++){		
	    $insertUmNovoItem = "insert into itens_movimento 
									(mov_codigo, 
									pro_codigo, 
									ite_status, 
									ite_quantidade, 
									ite_consolidado, 
									ite_qtde_solicitada, 
									ite_posologia,
									ite_detalhes_tratamento, 
									ite_observacoes, 
									ite_qtde_dia, 
									ite_lote, 
									ite_validade)
								values
									('{$mov_codigo}',
									 '{$pro_codigo}', 
									 '{$ite_status}', 
									 '{$q[$i]}', 
									 '{$ite_consolidado}', 
									 '{$quantidade_dia_soli}',
									 ".(empty($ite_posologia) ? 'null' : "upper('{$ite_posologia}')") .", 
									 ".(empty($ite_detalhes_tratamento) ? 'null' : "upper('{$ite_detalhes_tratamento}')") .", 
									 ".(empty($ite_observacoes) ? 'null' : "upper('{$ite_observacoes}')") .", 
									 ".(empty($quantidade_dia) ? 'null' : $quantidade_dia).",
									 '{$l[$i]}',
									 '{$v[$i]}')";
	 	echo $insertUmNovoItem;
		 $exeinsertUmNovoItem = pg_query($insertUmNovoItem) or die ("ERRO ".$insertUmNovoItem);
	 	} 

			
			if($exec_sql == true)
			{
				echo "Inserido";
			} else {
				echo "Erro: ".pg_last_error($db);
			}	
	
	}
	 else {
	 	echo"<script>
	 		alert(Produto já dispensado)
	 	</script>";
		
		/*$linha = pg_fetch_array($exec_select);
		
		$sql = "update itens_movimento set
					 ite_quantidade = $quantidade_dia, 
					 ite_qtde_solicitada = $quantidade_dia_soli, 
					 ite_posologia = ".(empty($ite_posologia) ? 'null' : "upper('$ite_posologia')") .", 
					 ite_detalhes_tratamento = ".(empty($ite_detalhes_tratamento) ? 'null' : "upper('{$ite_detalhes_tratamento}')") .", 
					 ite_observacoes = ".(empty($ite_observacoes) ? 'null' : "upper('{$ite_observacoes}')") .",
					ite_qtde_dia = ".(empty($quantidade_dia) ? 'null' : $quantidade_dia) ." where ite_codigo = $linha[ite_codigo]";
		
		$exec_sql = pg_query($sql)  or die ("ERRO ".$sql);
		
		if($exec_sql == true)
		{
			echo "Alterado";
		} else {
			echo "Erro: ".pg_last_error($db);
		}
		$linha = pg_fetch_array($exec_select);
		
		echo $linha[0];*/
		
	}
	
	
?>