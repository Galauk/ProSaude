<?

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
 	
	$form = new classForm();
	$common = new commonClass();
	echo $common->incJquery('');

	$id_login = $_GET["id_login"];
    $inv_codigo = $_GET["inv_codigo"];
	
	if ($_GET['acao'] == "excluir"){
		$delete = "DELETE
					 FROM inventario
					WHERE inv_codigo = $inv_codigo";
		$executaDelete = pg_query($delete);
		if ($executaDelete){
			$resposta = "Excluido com sucesso";
			
		}else{
			$resposta = "N&atilde;o foi poss&iacute;vel excluir o invent&aacute;rio, entre em contato com o administrador";
		}
		echo "
			<SCRIPT LANGUAGE=\"JavaScript\">
				alert( \"$resposta\" );
				setTimeout(\"location='cadInventario.php?id_login=$id_login&acao=relatorio'\", 0);
			</SCRIPT>";
	}else{	
		?>
		<link href="estilo.css" rel="stylesheet" type="text/css">
		<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
		<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">
			root 		= "<?=$_SESSION[root]?>";
			linkroot 	= "<?=$_SESSION[linkroot]?>";
			comum 		= "<?=$_SESSION[comum]?>";
			modulo 		= "<?=$_SESSION[modulo]?>";
		</script>
		<script src=relatorio/script.js></script>
		<script src=relatorio/funcoes.js></script>
		<script language="JavaScript" type="text/javascript" src="jsaddex.js"></script>
		<script language="JavaScript" type="text/javascript">
			function excluir(codigo, login){
				location.href = "op_buscarInventario.php?acao=excluir&inv_codigo="+codigo+"&id_login="+login;
			}
		</script>
	    <?    
	
		// busca o inventário
		$busca = "SELECT set_codigo, 
						 inv_data 
					FROM inventario 
				   WHERE inv_codigo = $inv_codigo";
		$exec_busca = pg_query($busca);
		$dados = pg_fetch_array($exec_busca);
		$set_codigo = $dados[set_codigo];
		$dt_final = $dados[inv_data];
	
		// informações do grupo do inventário (vacina, medicamento, impressos....)
		$select = "SELECT gru_nome
					 FROM grupo
					WHERE gru_codigo = (SELECT gru_codigo 
				   						  FROM inventario 
										 WHERE inv_codigo = $inv_codigo)";
		$exec_select = pg_query($select);
		$resultado = pg_fetch_array($exec_select);
		
		
	    $sql = "SELECT count(*) 
	    		  FROM inventario_produto 
	    		 WHERE inv_codigo = $inv_codigo";
	    $exec_sql = pg_query($sql);
	    $l = pg_fetch_array($exec_sql);
	
		echo $common->menuTab(array("Inventário de $resultado[gru_nome]"));
		echo $common->bodyTab('1');
		if($l[0] == 0){
	//		echo $common->modalMsg('N&atilde;o existe invent&aacute;rio cadastrado!');
			echo "<span class=titulo>Este invent&aacute;rio est&aacute; vazio, deseja exclu&iacute;-lo?</span><br><br>";
	
			echo "<div style='clear:both;'>";
				echo "<div style='float:left;width:100px;text-align:right;padding-right:5px;'>";
					echo $common->commonButton("Excluir", null, "delete.png", "onClick=\"excluir($inv_codigo, $id_login);\"");
				echo "</div>";
				echo "<div style='float:left;width:200px;padding-left:5px;'>".
					$common->commonButton("Voltar", "inventario.php?id_login=$id_login", "voltar.png")
				
						//<a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg border=0> </a>
					."  </div>";
			echo "</div>";
			
		} else { // ha itens no inventário
			$sql = "SELECT produto.pro_codigo, 
						   produto.pro_nome,
						   produto.pro_validade,
						   grupo.gru_codigo, 
						   gru_nome,
						   produto.pro_fracionado
					  FROM produto, 
					  	   grupo
					 WHERE produto.gru_codigo = grupo.gru_codigo
					   AND pro_situacao='A'
					   AND grupo.gru_codigo IN (SELECT gru_codigo 
					   							  FROM inventario 
												 WHERE inv_codigo = $inv_codigo) 
	                   AND produto.pro_codigo IN (SELECT pro_codigo 
					   								FROM inventario_produto 
												   WHERE inv_codigo = $inv_codigo 
												     AND pro_codigo = produto.pro_codigo)
					 ORDER BY pro_nome";
							
			$exec = pg_query($sql);
			
	
	
			echo $form->openForm('op_salvarInventario.php', 'POST', 'cadInventarioProduto');		
			//echo "<form name=\"cadInventarioProduto\" action=\"op_salvarInventario.php\" method=\"POST\">";
			echo "<input type=\"hidden\" name=\"inv_codigo\" value=\"$inv_codigo\">";
			echo "<input type=\"hidden\" name=\"id_login\" value=\"$id_login\">";
			echo "<table class=lista>";
				echo "<tr bgcolor=#ffffff>";
					echo "<th>";
						echo "Produto";
					echo "</th>";
					echo "<th width='200'>";
						echo "Quantidade";
					echo "</th>";
					echo "<th width='200'>";
						echo "Fração/Lote";
					echo "</th>";
					echo "<th width='200'>";
						echo "Lote";
					echo "</th>";
					echo "<th width='240'>";
						echo "Validade";
					echo "</th>";
				echo "</tr>";
				$aux = 0;
			while($linha = pg_fetch_array($exec))
			{
	//			round(cast(calcula_estoque($linha[pro_codigo], $set_codigo, '$dt_final') as numeric), 0)";
				$sql  = "SELECT sal_qtde, 
								sal_lote, 
								sal_validade,
								sal_dose_lote";
				$sql .= "  FROM saldo ";
				$sql .= " WHERE set_codigo = $set_codigo ";
				$sql .= "   AND pro_codigo = $linha[pro_codigo]";
				$sql .= "   AND sal_qtde <> 0";
				$sql .= "   AND sal_validade >=  NOW()";
				
	/*			$sql  = "SELECT round(cast(calcula_estoque($linha[pro_codigo], $set_codigo, '$dt_final') as numeric), 0)";
				$sql .= "  FROM produto ";
				$sql .= " WHERE gru_codigo = $linha[gru_codigo] ";
	*///			echo $sql;
	
				$exec_consulta = pg_query($sql);
				
	
	/*			$select = "select a.invp_quantidade from inventario_produto a where a.inv_codigo = $inv_codigo and a.invp_status = 'A' and a.pro_codigo = $linha[0]";
				
				$exec_select = pg_query($select);
				
				$row = pg_fetch_array($exec_select);*/
				
				echo "<tr>";
					echo "<td>";
						echo "<input type=\"hidden\" name=\"pro_codigo[$aux]\" value=\"$linha[0]\">";
						echo $linha[1];
					echo "</td>";
					echo "<td colspan='5'>";
	
						echo "<table cellpadding='0' cellspacing='0'>";
							echo "<tr>";
								echo "<td width='100%' style=\"border:none\">";
								$temQtde = 0;
								while ($row = pg_fetch_array($exec_consulta)){
									if($row[0] != "")
									{
										$row[0] = intval($row[0]);
									}
									echo "<table cellpadding='0' cellspacing='0'>";
										echo "<tr>";
											echo "<td width='194'  style=\"border:none\">";
												$quantidade = $row[0] == '' ? 0 : $row[0];
												$temQtde++;
												echo "<input type=\"text\" name=\"invp_quantidade[$aux][]\" value=\"$quantidade\" class=\"inputForm\" onkeypress=\"return Bloqueia_Caracteres(event);\" onblur=\"this.value = this.value.replace( /\D/g,'');\" maxlength='9' />";
											echo "</td>";
									
											echo "<td width='205' style=\"border:none\">";
													 if ($linha[pro_fracionado] == 'S'){
												?>
													<input type="text" name="invp_dose_lote[<?=$aux?>][]" value="<?=$row[3]?>" <? if ($row[3] != '') echo "readonly=\"readonly\""; ?> class="inputForm" maxlength='10' onkeypress="return Bloqueia_Caracteres(event);" />
												<?
													 }
											echo "</td>";
											echo "<td width='205' style=\"border:none\">";
												 if ($linha[pro_validade] == 'S'){
											?>
												<input type="text" name="invp_lote[<?=$aux?>][]" value="<?=$row[1]?>" <? if ($row[1] != '') echo "readonly=\"readonly\""; ?> class="inputForm" maxlength='20' />
											<?
												 }
											echo "</td>";
											echo "<td width='200' style=\"border:none\">";
												 if ($linha[pro_validade] == 'S'){
											?>
												<input type="text" name="invp_validade[<?=$aux?>][]" value="<?=formatarData($row[2])?>" <? if ($row[2] != '') echo "readonly=\"readonly\""; ?> class="inputForm" maxlength='10' onkeypress="return Ajusta_Data(this, event);" />
											<?
												 }
											echo "</td>";
//											echo "<td style=\"border:none\">";
//												echo "&nbsp;&nbsp;&nbsp;&nbsp;";
//											echo "</td>";
										echo "</tr>";
									echo "</table>";
								}
									echo "<div id='camposTexto$aux'></div>";
									echo "</td>";
									$id = "idImg".$aux;
									echo "<td valign=top style='padding-top:7px; border:none;' id='$id'>";
									if ($linha[pro_validade] == 'S'){
										echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/add.png\" border=0 onClick='addInput($aux, $linha[0], ".($linha[pro_fracionado] == 'S'?"true":"false").")' style='cursor: pointer;'>";
									}else{
										if ($temQtde == 0){
											echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/add.png\" border=0 onClick='addInputQtde($aux, $linha[0])' style='cursor: pointer;'>";	
										}else {
											echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_off.png\" border=0>";	
										}
									}
									echo "</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
					echo "</tr>";
					$aux++;
			}
			echo "<table class=table>
					<tr>";
				echo "<td width=50% align=\"right\">";
					echo $common->commonButton("Salvar", null, "salvar.gif", "onClick=\"return verificaCamposObrigatorios();\"");
				echo "</td>
					<td width=50% align=\"left\">";
					//echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gravar.jpg\" alt=\"Gravar\" onclick=\"return verificaCamposObrigatorios();\">";
					echo " <a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif alt='Voltar' border =0> </a>\n";
				echo "</td>";
				echo "</tr>
				</table>";
	//		echo "</form>
			echo $form->closeForm();
		}
		echo $common->closeTab();
	}
?>
