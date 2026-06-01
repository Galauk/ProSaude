<?php
// exibe uma busca/listagem dos procedimentos

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario( $hotkey = false );

verauth($id_login);

// busca
if( 1 ) {}

	echo "
	<form action='#' method='get' onsubmit='return busca_proc(".intval($id_login).")'>
	<fieldset>
	<legend>Opções do Procedimento</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick=\"form_proc('$id_login')\"
				style='cursor:pointer;' />
		</td>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='text' name='palavra_chave' id='proc_palavra_chave' class='box'
				onChange=\"this.value=this.value.toUpperCase();busca_proc(".intval($id_login).");\">
		</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

if( empty($acao) || $acao == 'busca' )
{
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp = '';
	$max 	= 15;
	$where1 = empty($acao) ? '' : sprintf("WHERE TO_ASCII(p.proc_nome) ILIKE TO_ASCII('%%%s%%') or  TO_ASCII(p.proc_classificacao_sus)ILIKE TO_ASCII('%%%s%%')", $str, $str);
	$where2 = empty($acao) ? '' : sprintf("WHERE TO_ASCII(a.proc_nome) ILIKE TO_ASCII('%%%s%%') or TO_ASCII(a.proc_numero)ILIKE TO_ASCII('%%%s%%')", $str, $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';
	
	$stmt 	= sprintf(
			"(SELECT p.proc_classificacao_sus, p.proc_nome, m.med_nome, p.proc_valor, 'N', p.proc_codigo
			FROM %s.procedimento AS p 
			LEFT JOIN %s.medico AS m ON m.med_codigo = p.med_codigo %s)
			UNION
			(SELECT a.proc_numero, a.proc_nome, m1.med_nome, a.proc_valor, 'S', a.proc_codigo
			FROM %s.apac_procedimento_cad AS a
			LEFT JOIN %s.medico AS m1 ON m1.med_codigo = a.med_codigo %s)
			ORDER BY 2 %s",
				ESQ_SAUDE, ESQ_SAUDE, $where1,
				ESQ_SAUDE, ESQ_SAUDE, $where2,
				$sql_f);

	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max últimos Procedimentos Cadastrados";
	
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='40'>Código</th>
		<th>Procedimento</th>
		<th>Laboratório</th>
		<th width='80'>Valor (R$)</th>
		<th width='20'>APAC</th>
		<th width='220'>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
	       	<td align='center'>$row[0]</td>
	       	<td>$row[1]</td>
	       	<td>$row[2]</td>
			<td align='center'>".number_format($row[3],2)."</td>
	       	<td align='center'>$row[4]</td>
	        <td align='center'>		
				<a href='javascript:;'
					onclick=\"atualiza_proc('$row[0]','$row[1]','$row[2]','$row[4]','$row[5]');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0'></a>".
			( $row[4] == 'S' ?
				"<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
						onclick=\"form_proc('$id_login','$row[0]')\" />
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
						onclick=\"apagar_proc('$id_login','$row[0]')\" />"  :
						"" 
			).
			"</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";
}
// cadastro
else if( $acao == 'form_add' || $acao == 'form_edit' )
{
	$acao2 = ( $acao == 'form_add' ? 'add' : 'edit' );

	if( $acao == 'form_edit'  )
	{
		$stmt = "SELECT * FROM apac_procedimento_cad WHERE proc_codigo = $codigo";
		$row = db_getRow( $stmt );
		
		//$checked_m = ($row['pac_sexo'] == 'M' ? 'checked="checked"' : '' );
		//$checked_f = ($row['pac_sexo'] == 'F' ? 'checked="checked"' : '' );
		
	}
	else
		$row = array();
	
	//var_dump($_SERVER['QUERY_STRING']);
	
	print "
	<form action=\"?\" method=\"get\" onsubmit=\"return form_proc_submit('$id_login','$acao2')\">

	<input type=\"hidden\" name=\"codigo\" id=\"codigo\" value=\"$codigo\" />

	<fieldset>
	<legend>Cadastro de Procedimento da APAC</legend>
	<table>
		<tr>
			<td width=\"85\"><label for=\"proc_numero\">N&uacute;mero</label></td>
			<td><input type=\"text\" name=\"proc_numero\" id=\"proc_numero\" size=\"15\"
				 maxlength=\"99\" class=\"box\" value=\"$row[proc_numero]\" />
			</td>
		</tr>
		<tr>
			<td><label for=\"proc_nome\">Nome</label></td>
			<td><input type=\"text\" name=\"proc_nome\" id=\"proc_nome\" size=\"35\"
				 maxlength=\"99\" class=\"box\" value=\"$row[proc_nome]\" />
			</td>
		</tr>
		<tr>
			<td><label for=\"med_codigo\">Laboratorio</label></td>
			<td>
				<select name=\"med_codigo\" id=\"med_codigo\" class=\"box\">
				";
			$qry =	db_query("SELECT med_codigo, med_nome 
										FROM medico
										WHERE prestador_servico = 'S' ORDER BY med_nome");
			while( $row_med = pg_fetch_row($qry) )
			{
				$sel = ( $row_med[0] == $row['med_codigo'] ? 'selected' : '' );
				print "\n\t\t\t\t<option value='$row_med[0]' $sel>$row_med[1]</option>";
			}
			
			print "	
				</select>
			</td>
		</tr>
		<tr>
			<td><label for=\"proc_valor\">Valor (R$)</label></td>
			<td><input type=\"text\" name=\"proc_valor\" id=\"proc_valor\" size=\"5\"
				 class=\"box\" value=\"$row[proc_valor]\" />
			</td>
		</tr>
		<tr>
			<td><label for=\"proc_tipo\">Tipo</label></td>
			<td>
				<select name=\"proc_tipo\" id=\"proc_tipo\" class=\"box\">
						<option value='Q'".($row['proc_tipo'] == 'Q' ? ' selected' : '').">Quantidade</option>
						<option value='V'".($row['proc_tipo'] == 'V' ? ' selected' : '').">Valor</option>
				</select>
			</td>
		</tr>
	</table>
		<table>
			<tr>
				<td width='150'>&nbsp;</td>
				<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' /></td>
			</tr>
		</table>
		
	</fieldset>
	</form>";
}
// sql insert
else if( $acao == 'add' )
{
	// SQL INSERT
	$stmt = "INSERT INTO apac_procedimento_cad ( 
	proc_nome, 
	proc_numero, 
	med_codigo, 
	proc_valor, 
	proc_tipo
	 ) VALUES ( 
	'".trim(strtoupper(substr($proc_nome,0,99)))."', 
	'".trim(strtoupper(substr($proc_numero,0,99)))."', 
	".intval($med_codigo).", 
	".floatval($proc_valor).", 
	'".trim(strtoupper(substr($proc_tipo,0,2)))."' )";
	
	db_query($stmt);
	
	print "<p class='aviso ok'>Procedimento da APAC cadastrado !</p>";
}
else if( $acao == 'edit' )
{
	
	//var_dump($_SERVER['QUERY_STRING']);
	
	$stmt = "UPDATE apac_procedimento_cad SET 
	proc_nome = '".trim(strtoupper(substr($proc_nome,0,99)))."', 
	proc_numero = '".trim(strtoupper(substr($proc_numero,0,99)))."', 
	med_codigo = ".intval($med_codigo).", 
	proc_valor = ".floatval($proc_valor).", 
	proc_tipo = '".trim(strtoupper(substr($proc_tipo,0,2)))."'
	WHERE proc_codigo = ".intval($codigo) ;
	
	db_query($stmt);
	
	print "<p class='aviso ok'>Procedimento da APAC alterado !</p>";
}
else if( $acao == 'del' )
{
	// SQL DELETE
	$stmt = "DELETE FROM apac_procedimento_cad WHERE proc_codigo = '".intval($codigo)."'" ;	
	
	db_query($stmt);
	
	print "<p class='aviso ok'>Procedimento da APAC apagado !</p>";
}
?>
