<?
	require_once '../global.php';
	fdebug("OK");
		
	$proSelecionados = implode(",",$_POST['pro_codigo']);
	
	$sql = "SELECT pro_codigo,
	               pro_nome,
	               pro_fracionado
	          FROM produto
	         WHERE pro_codigo IN ($proSelecionados)
	         ORDER BY pro_nome";
//echo $sql;
	
	$query = pg_query($sql);
	
	$sqlConfiguracao = "SELECT * FROM config WHERE conf_chave = 'VALIDADE_DOS_MEDICAMENTOS'";
	$queryConfiguracao = pg_query($sqlConfiguracao);
	$reg_configuracao = pg_fetch_array($queryConfiguracao);
	
	echo"<table>";
		while($r = pg_fetch_array($query)){
		echo"<tr>
			<td>
				{$r['pro_nome']}
			</td>
			<td style='padding-left:5px;'>
				<input type='text' name='{$r['pro_codigo']}' value='1' class='inputForm pro-qtd' size='10' />
			</td>";
				
		if($reg_configuracao[conf_valor_bool] == "t"){
			echo "<td>
					 Dura&ccedil;&atilde;o <small>(dias)</small>: <input type='text' name='{$r['pro_codigo']}' value='' class='inputForm duracao' size='6' />
				 </td>";
		}	
			if($r['pro_fracionado'] == 'S'){
				echo "<td style='padding-left:5px;'>
				<select name='{$r['pro_codigo']}' class='inputForm fracionado' style=width:100px;>
					<option value=unidade>UNIDADE</option>
					<option value=fracao>FRACAO</option>					
				</select>
				</td>";
				echo "<option value=$reg[cid_codigo_ibge]>$reg[cid_nome]</option>";
				//echo $form->inputSelect('fracao',$arrayUnidadeFracao,'Unidade de Medida');
			}
				
		echo"</tr>";
	    }
	echo "</table>";