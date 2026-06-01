<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	/*if(strlen($palavra) > 0)
	{*/		
		$sql = "select * 
				  from produto p 
				JOIN unidmedida um
				  ON um.umed_codigo = p.umed_codigo
				 where pro_nome like upper('".trim($palavra)."%')
				 and pro_tipo IN('M','T')
				 order by pro_nome";
	/* else {
		//exit("[[{codigo: '', nome: 'Este param&ecirc;tro n&atilde;o possu&iacute; registro.', mae : '', data_nasc: '', cidade : ''}]]");
		exit( "Busca vazia !" );
	}
	*/
	//echo $sql;
	
	$exec = pg_query($sql);
	if(pg_num_rows($exec) == 0)
	{
		//exit("[[{codigo: '', nome: 'Este param&ecirc;tro n&atilde;o possu&iacute; registro.', mae : '', data_nasc: '', cidade : ''}]]");
	}
	//echo "[";
	//$resp = "[";
	$i = 0;
	echo "
		<tr>
			<th colspan='3'>
			FORAM ENCONTRADOS <font color='red'>".pg_num_rows($exec)."</font> REGISTROS COM '<font color='red'>".strtoupper($palavra)."</font>'
			</th>
		</tr>
		<tr>
			<th>Descricao</th>
		</tr>";
		$i = 0;
		while($row = pg_fetch_array($exec)){
			$i++;
			echo"
				<tr id='tr$i' onmouseover=\"trocar_cor(this.id, null);\" onmouseout=\"retirar_cor(this.id, null)\"
				onclick=\"passar_produto($row[pro_codigo], '$row[pro_nome]')\" style='cursor:pointer;'>
					<td>
						$row[pro_nome]
					</td>
				</tr>";
			
		}
			
		//$resp .= "[{\"codigo\" : $row[pro_codigo], \"nome\" : \"$row[pro_nome]}]";
		if($i != pg_num_rows($exec))
		{
			$resp .= ", ";
		}
		
		/*echo "
			<tr id='tr$i' onmouseover=\"trocar_cor(this.id, 'fone$i');\" onmouseout=\"retirar_cor(this.id, 'fone$i')\"
				onclick=\"passar_usuario($row[usu_codigo], '$row[usu_nome]', '$row[usu_mae]', '$row[usu_datanasc]', '$aux', '$row[usu_prontuario]')\" style='cursor:pointer;'>
				<td>
					$row[usu_prontuario]
				</td>
				<td>
					$row[usu_nome]
				</td>
				<td>
					$row[usu_mae]
				</td>
				<td>
					$row[usu_datanasc]
				</td>
				<td>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' onclick=\"window.open('paciente.php?id_login=$id_login&acao=form_edit&usu_codigo=$row[usu_codigo]&controle=1',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\"
				</td>
			</tr>";*/
		/*echo "
			<div id='fone$i' style='left:15%;width:235px;height:85px;background: 	#FFF8DC;position:absolute;display:none;border:1px solid blue;font-weight:bold;'>
			RG: $row[usu_rg]<br />
			CPF: $row[usu_cpf]<br />
			Prontuario: $row[usu_prontuario]<br />
			Telefone: $row[usu_fone]<br/>
			Celular: $row[usu_celular]<br/>
			<!--Telefone de Recado: $row[usu_fone_recado]-->
			</div>";
		
		
	}*/
	//echo
	//$resp .= "]";
	
	echo $resp;
