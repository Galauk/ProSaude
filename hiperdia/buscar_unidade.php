<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	if(strlen($palavra) > 0)
	{		
		if($acao == "buscar_unidade")
		{
			$where = " uni_desc like upper('%".trim($palavra)."%') ";
		}

		$sql = "select * from unidade where $where";

	} else {
		
		exit( "Busca vazia !" );
	}
	

	
	$exec = pg_query($sql);
	if(pg_num_rows($exec) == 0)
	{
		//exit("[[{codigo: '', nome: 'Este param&ecirc;tro n&atilde;o possu&iacute; registro.', mae : '', data_nasc: '', cidade : ''}]]");
	}
	//echo "[";
	//$resp = "[";
	$i = 0;
	echo "
	<table>
		<tr>
			<th colspan='3'>
			FORAM ENCONTRADOS <font color='red'>".pg_num_rows($exec)."</font> REGISTROS COM '<font color='red'>".strtoupper($palavra)."</font>'
			</th>
		</tr>
		<tr>
			<td align='left'><b>Nome</td>
			<td align='left'><b>Local</td>
			

		</tr>";
	while($row = pg_fetch_array($exec))
	{
		$i++;
		$select = " select cid_nome from cidade where cid_codigo_ibge = '$row[muni_cd_cod_ibge_resid]'";
		$query = pg_query($select);
		$linha = pg_fetch_array($query);
		$aux = $linha[cid_nome] == "" ? "" : $linha[cid_nome];
		echo "
			<tr id='tr$i' onmouseover=\"trocar_cor(this.id, 'fone$i');\" onmouseout=\"retirar_cor(this.id, 'fone$i')\"
				onclick=\"passar_unidade('$row[uni_desc]', '$row[uni_localizacao]')\" style='cursor:pointer;'>
				<td align='left'>
					$row[uni_desc]
				</td>
				<td align='left'>
					$row[uni_localizacao]
				</td>
			</tr>
			";
		echo "
			<div id='fone$i' style='left:15%;width:235px;height:85px;background: 	#FFF8DC;position:absolute;display:none;border:1px solid blue;font-weight:bold;'>
			RG: $row[usu_rg]<br />
			CPF: $row[usu_cpf]<br />
			Prontuario: $row[usu_prontuario]<br />
			Telefone: $row[usu_fone]<br/>
			Celular: $row[usu_celular]<br/>
			<!--Telefone de Recado: $row[usu_fone_recado]-->
			</div>";
		
		
	}
	echo "</table>";
	//echo
	//$resp .= "]";
	
	echo $resp;
