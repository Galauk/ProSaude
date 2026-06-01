<?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once 'global.php';
	//__autoload("commonClass");
	//$common = new commonClass();
	//$common->incJquery();
	//echo "<pre>".print_r($_REQUEST,1);
/*	$id_login = $_REQUEST['id_login'];
	$rec_codigo = $_REQUEST['rec_codigo'];*/
	
	/*foreach ($dados as $item) {
		echo $item."<br />";
	}*/
	
	/*
	 * SELECIONANDO O SETOR EM QUE SERÁ REALIZADA A DISPENSAÇÃO
	 */
	$selectSetor = "select *from logon  WHERE id_login = $id_login";
	$execSelectSetor = pg_query($selectSetor);
	$row = pg_fetch_array($execSelectSetor); 

	$set_codigo = $row['cod_setor'];
	
	/*
	 * SELECIONANDO OS PRODUTOS A SEREM DISPENSADOS
	 */
	$seleciona = "SELECT p.pro_codigo,
						 p.pro_nome,
						 ir.irec_quantidade,
						 ir.irec_qtde_pendente
					FROM receita r 
					JOIN itemreceita ir 
					  ON r.rec_codigo = ir.rec_codigo 
					JOIN produto p
					  ON p.pro_codigo = ir.pro_codigo
				   WHERE r.rec_codigo = $rec_codigo
				     AND ir.irec_qtde_pendente > 0";
	$exec = pg_query($seleciona);

	$erro = false;
	$contaMov = 0;
	$numMovimento = "";
	while ($linha = pg_fetch_array($exec)){
		/*
		 * VERIFICANDO A DISPONIBILIDADE E DISPENSANDO (OU BLOQUEANDO)
		 */
		
		/*O subselect desconta os envios de requisicao do saldo de determinado lote*/
		$select = "SELECT sal_validade,
						  sal_lote,
						  COALESCE(sal.sal_qtde,0) 
					      - 
					      (SELECT COALESCE(sum(remil_quantidade),0) 
					         FROM requisicao_materiais_itens remi
					         JOIN requisicao_materiais_itens_lote remil
					           ON remil.remi_codigo = remi.remi_codigo
					         JOIN requisicao_materiais rem
							   ON rem.rem_codigo = remi.rem_codigo
					        WHERE remi.remi_status = 'E'
					          AND remi.pro_codigo = $linha[pro_codigo]
					          AND set_codigo_sol = $set_codigo
					          AND remil.remil_lote = sal.sal_lote) AS sal_qtde,
					      sal_qtde as saldo_original
					 FROM saldo sal
					WHERE pro_codigo = $linha[pro_codigo]
					  AND set_codigo = $set_codigo
					  AND sal_qtde > 0
					  AND sal_validade >= CURRENT_DATE
					ORDER BY sal_validade ASC";
		$execSelect = pg_query($select);
		$quantidadeABaixar = $linha['irec_qtde_pendente'];
		if (pg_num_rows($execSelect) > 0){
			if($numMovimento == ""){
				$movimento = criaMovimento("S", null, $id_login, "DISPENSACAO DE MEDICAMENTO",$rec_codigo);
				$numMovimento = $movimento;
			}else{
				$movimento = $numMovimento;
			}
		}else{
			//$erro = true;
		}
		
		while ($row = pg_fetch_array($execSelect)){
			if ($quantidadeABaixar > 0){
				if ($quantidadeABaixar >= $row['sal_qtde']){
					
					if($row[sal_qtde] < $row[saldo_original]){
						//echo $common->modalMsg("ALERTA","O lote $$row[sal_lote] possui uma pendência de envio de materiais");
						$enviado = "aler";
					}
					if (itens_movimento($linha['pro_codigo'], $row['sal_qtde'], $row['sal_lote'], $row['sal_validade'], $movimento, null, $id_login)){
						$quantidadeABaixar = $quantidadeABaixar - $row['sal_qtde'];
						$update = "UPDATE itemreceita
									  SET irec_qtde_pendente = $quantidadeABaixar
									WHERE rec_codigo = $rec_codigo
									  AND pro_codigo = $linha[pro_codigo]";
						$execUpdate = pg_query($update) or die($update);
						$contaMov++;
						echo $enviado.$rec_codigo."|".$id_login;
					}
				}else{
					if (itens_movimento($linha['pro_codigo'], $quantidadeABaixar, $row['sal_lote'], $row['sal_validade'], $movimento, null, $id_login)){
						$quantidadeABaixar = $quantidadeABaixar - $row['sal_qtde'];
						$update = "UPDATE itemreceita
									  SET irec_qtde_pendente = 0
									WHERE rec_codigo = $rec_codigo
									  AND pro_codigo = $linha[pro_codigo]";
						$execUpdate = pg_query($update) or die($update);
						
						$contaMov++;
						echo $rec_codigo."|".$id_login;
						break;
					}
				}
			}
		}
		//die("aqui");
		if ($quantidadeABaixar > 0){
			$erro = true;
		}
	}
	if ($erro){
		echo "erro$rec_codigo|$id_login";
	} 
?>