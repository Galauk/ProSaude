<?
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');


	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

	$inv_codigo = $_POST["inv_codigo"];

	$id_login = $_POST["id_login"];
	$pro_codigo = $_POST["pro_codigo"];
	$invp_quantidade = $_POST["invp_quantidade"];
	$invp_lote = $_POST["invp_lote"];
	$invp_validade = $_POST["invp_validade"];
	$invp_dose_lote = $_POST["invp_dose_lote"];
	//echo "<pre>".print_r($_POST,true)."</pre>";
	//exit();
	for($i = 0; $i < count($pro_codigo); $i++)
	{
		// echo $i;
		for($j = 0; $j < count($invp_quantidade[$i]); $j++){ // lote diferente
			$insert = "";
			//tem que mudar o invp_datahora e o invp_ip
			$quantidade = $invp_quantidade[$i][$j];
			if($quantidade != "" && $quantidade >= "0")
			{
				$select = "SELECT a.invp_codigo,
								  i.set_codigo
							 FROM inventario_produto a 
							 JOIN inventario i 
							   ON a.inv_codigo = i.inv_codigo 
							WHERE a.inv_codigo = '$inv_codigo' 
							  AND a.pro_codigo = '$pro_codigo[$i]'";
				$exec_select = pg_query($select);
				$row = pg_fetch_array($exec_select);
				$aux = pg_num_rows($exec_select);
								
				$set_codigo = $row['set_codigo'];
				$invp_codigo = $row[0];
				$auxLote = $invp_lote[$i][$j];
				$auxValidade = $invp_validade[$i][$j];
				$auxDoseLote = (!empty($invp_dose_lote[$i][$j])?$invp_dose_lote[$i][$j]:1);
				
				if ($auxValidade != ''){
					$select = "SELECT a.invplq_codigo,
									  a.invplq_quantidade,
									  a.invplq_lote,
									  a.invplq_validade
								 FROM inventario_produto_lote_quantidade a 
								WHERE a.invp_codigo = '$invp_codigo'
								  AND a.invplq_lote = '$auxLote'";
				}else{
					$select = "SELECT a.invplq_codigo,
									  a.invplq_quantidade,
									  a.invplq_lote,
									  a.invplq_validade
								 FROM inventario_produto_lote_quantidade a 
								WHERE a.invp_codigo = '$invp_codigo'";
				}
								
				$exec = pg_query($select);
				$linha = pg_fetch_array($exec);
				$contaLinha = pg_num_rows($exec);
	
				$sel = pg_query("select now()");
				$l = pg_fetch_array($sel);
				$invp_datahora = $l[0];
				$quantidade = intval(abs($quantidade));

				$update = "UPDATE inventario_produto SET invp_status = 'T' WHERE invp_codigo = $invp_codigo";
				$exec_update = pg_query($update);

				$sql = "SELECT coalesce(sal_qtde, 0) as sal_qtde
						  FROM saldo
						 WHERE pro_codigo = $pro_codigo[$i]
						   AND set_codigo = $set_codigo ".
						   ($auxValidade != '' ? " AND sal_lote = '$auxLote' AND sal_validade='$auxValidade'" : "");
				
				//die($sql);
				$query = pg_query($sql);
				$dado = pg_fetch_array($query);
				$qtdeSaldo = $dado['sal_qtde'];
				
				if ($quantidade != 0){
					$acuracia = $qtdeSaldo/$quantidade*100;
				}else{
					$acuracia = 0;
				}
				//die($qtdeSaldo .'-'. $quantidade);
				if ($auxValidade != ''){
					$seleciona = "SELECT *
									FROM inventario_produto_lote_quantidade
								   WHERE invp_codigo = '$invp_codigo'
								     AND invplq_lote = '$auxLote'
								     AND invplq_validade = '$auxValidade'";
					$executa_seleciona = pg_query($seleciona);
					//die($seleciona);
					$contaLinhas = pg_num_rows($executa_seleciona);
						$selecionado = pg_fetch_array($executa_seleciona);
					if ($qtdeSaldo !=  $quantidade && $contaLinhas == 0){
						
						//echo 'entrou';
						$insert = "INSERT INTO inventario_produto_lote_quantidade 
										 (invp_codigo, 
										  invplq_quantidade,
										  invplq_lote,
										  invplq_validade,
										  invplq_quantidade_saldo,
										  invplq_acuracia,
										  invplq_dose_lote) 
								   VALUES 
										 ('$invp_codigo', 
										  '$quantidade',
										  '$auxLote',
										  '$auxValidade',
										  '".intval($qtdeSaldo)."',
										  $acuracia,
										  $auxDoseLote);";
					  $exec_insert = pg_query($insert) or die(pg_last_error());
					}else if($contaLinhas > 0 && $qtdeSaldo != $quantidade ){
						$quantidadeNew = $selecionado['iplq_quantidade'];
						$insert = "UPDATE inventario_produto_lote_quantidade 
									   SET invplq_quantidade = $quantidade,
									   	   invplq_acuracia = $acuracia,
									   	   invplq_dose_lote = $auxDoseLote									   	   
									 WHERE invp_codigo = '$invp_codigo'
								       AND invplq_lote = '$auxLote'
								       AND invplq_validade = '$auxValidade'";
									## invplq_dose_lote n�o pode ser atualizado
						
						$exec_insert = pg_query($insert) or die(pg_last_error());
					}
				}else{
					$insert = "INSERT INTO inventario_produto_lote_quantidade 
									 (invp_codigo, 
									  invplq_quantidade,
									  invplq_quantidade_saldo,
									  invplq_acuracia) 
							   VALUES 
									 ('$invp_codigo', 
									  '$quantidade',
									  '".intval($qtdeSaldo)."',
									  $acuracia);";
									  #se n�o h� validade, n�o h� dose
						$exec_insert = pg_query($insert) or die(pg_last_error());
				}
				// echo "'$insert'";
				//$exec_insert = pg_query($insert) or die(pg_last_error());
								// echo "brilha mto no corinthans";
	//			msg($id_login,'edit',$exec_insert);
			}
			
			// echo "la";
		}
	}
	//die("AAAA");
echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
		setTimeout(\"location='inventario.php?id_login=$id_login'\", 0);
	</SCRIPT>
";
?>
