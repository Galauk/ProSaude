<?php
session_start();
    include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	if(strlen($palavra) > 0)
	{		
		if($acao == "buscar_nome")
		{
			$where = " usu_nome like upper('".trim($palavra)."%') ";
		} else if($acao == "buscar_data") {
			$where = " usu_datanasc = '".trim($palavra)."%' ";
		}
		$sql = "select usu_codigo, usu_prontuario, usu_nome, usu_mae, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc,
				muni_cd_cod_ibge_resid, usu_fone, usu_celular, usu_cpf, usu_rg,
				case when usu_cartao_sus is not null then usu_cartao_sus else usu_cartao_p_sus end as usu_cartao_sus
				from usuario
				where $where order by usu_nome $limit";
	} else {
		//exit("[[{codigo: '', nome: 'Este param&ecirc;tro n&atilde;o possu&iacute; registro.', mae : '', data_nasc: '', cidade : ''}]]");
		exit( "Busca vazia !" );
	}
	
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
			<th>Cart.SUS</th>
			<th>NOME</th>
			<th>M&Atilde;E</th>
			<th>NASCIMENTO</th>
		</tr>";
	while($row = pg_fetch_array($exec))
	{
		$i++;
		$select = " select cid_nome from cidade where cid_codigo_ibge = '$row[muni_cd_cod_ibge_resid]'";
		
		$query = pg_query($select);
		
		$linha = pg_fetch_array($query);
		
		$aux = $linha[cid_nome] == "" ? "" : $linha[cid_nome];
		
		//echo
		
		/*$resp .= "[{\"codigo\" : $row[usu_codigo], \"nome\" : \"$row[usu_nome]\", \"mae\" : \"$row[usu_mae]\", \"data_nasc\" : \"$row[usu_datanasc]\", \"cidade\" : \"$aux\"}]";
		if($i != pg_num_rows($exec))
		{
			$resp .= ", ";
		}*/
		
		echo "
			<tr id='tr$i' onmouseover=\"trocar_cor(this.id, 'fone$i');\" onmouseout=\"retirar_cor(this.id, 'fone$i')\"
				onclick=\"passar_usuario($row[usu_codigo], '$row[usu_nome]', '$row[usu_mae]', '$row[usu_datanasc]', '$aux', '$row[usu_prontuario]')\" style='cursor:pointer;'>
				<td>
					$row[usu_cartao_sus]
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
			</tr>";
		echo "
			<div id='fone$i' style='left:15%;width:235px;height:85px;background: 	#FFF8DC;position:absolute;display:none;border:1px solid blue;font-weight:bold;'>
			RG: $row[usu_rg]<br />
			CPF: $row[usu_cpf]<br />
			Cart&atilde;o do SUS: $row[usu_cartao_sus]<br />
			Telefone: $row[usu_fone]<br/>
			Celular: $row[usu_celular]<br/>
			<!--Telefone de Recado: $row[usu_fone_recado]-->
			</div>";
		
		
	}
	//echo
	//$resp .= "]";
	
	echo $resp;
