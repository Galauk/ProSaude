<?php

/**
@Modulo: Layout Critica
@Arquivos Relacionados: layout_critica.inc.php layout_critica_op.php layout_critica_popup.php
@Tabelas: familia, usuario, cidade, layout_critica
@Acao: Form para geracao do arquivo de exportacao para o SUS
*/ 

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

Cabecario( $hotkey = false );

print '
<form id="form_busca" action="?" onsubmit="return valida_form_busca()">
<fieldset>
<legend>Busca de Cidadess</legend>
<table>
<tr>
	<td width="300">
		<label>Buscar
		<input type="text" id="palavra_chave" class="box" size="15" maxlength="50" /></label>
		<select id="tipo_busca" class="box">
			<option value="cid_nome">Nome da Cidade</option>
			<option value="cid_codigo_ibge">C&oacute;digo IBGE</option>
		</select>
	</td>
	<td><input type="image" src="".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg" alt="Procurar" /></td>
</tr>
</table>
</fieldset>
</form>
';

	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
	$where = empty($palavra_chave) ? '' : sprintf("WHERE %s ILIKE '%%%s%%'", $tipo, $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($palavra_chave) ? ' LIMIT '.$max : '';

	$stmt = "SELECT cid_codigo_ibge, cid_nome, uf_sigla FROM cidade ".$where.$sql_f;
		
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	//if( $acao == 'busca' && ! empty($palavra_chave) )	
	if(  ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando as $max primeiras Cidades";
		
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='100'>C&oacute;digo IBGE</th>
		<th>Nome</th>
		<th width='35'>UF</th>
		<th>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
       	  	<td class='c'>$row[0]</td>
	       	<td>$row[1]</td>
	       	<td class='c'>$row[2]</td>
	        <td width='230' class='c'>
				<a href='javascript:;'
					onclick=\"add_ibge('$row[0]','$row[1]'+' ('+'$row[2]'+')');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
			</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";	