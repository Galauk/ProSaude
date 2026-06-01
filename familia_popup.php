<?php
/**
 * exibe uma busca/listagem/form dos médicos (apac)
 * @brief  Inclusao principal para montagem do sistema
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	
	cabecario( $hotkey = false );
	//cabecario( $hotkey = true );
	
	verauth($id_login);

// busca
	echo "
	<form action='#' method='get' onsubmit='return busca_ibge(\"$id_login\");'>
	<fieldset>
	<legend>Opções de Buscar</legend>
	<table>
	<tr>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='hidden' name='acao' value='busca' />
			<input type='hidden' name='id_login' value='$id_login' />
			<input type='text' name='palavra_chave' id='pac_palavra_chave' class='box' onChange=\"this.value=this.value.toUpperCase();\" />
		</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

/** listagem/busca */
if( empty($acao) || $acao == 'busca' )
{	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
// 	$where1 = empty($acao) ? '' : sprintf("AND cid_nome ILIKE '%%%s%%'", $str);
	$where1 = empty($acao) ? '' : "WHERE cid_nome ILIKE '%$str%' ";
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

	if (empty($where1)){
		$stmt = "SELECT  cid_nome, cid_codigo_ibge,cid_codigo FROM cidade ORDER BY 2 ". $sql_f ;
	}else{	
		$stmt = "SELECT  cid_nome, cid_codigo_ibge,cid_codigo FROM cidade ". $where1 ." ORDER BY 2 ". $sql_f ;
	}
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
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
		<th width='70'>Código IBGE</th>
		<th width='150'>Nome da Cidade</th>
		<th>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
       	  	<td class='c'>$row[1]</td>
	       	<td>$row[0]</td>
	        <td width='230' class='c'>
				<a href='javascript:;'
					onclick=\"add_cod_ibge('$row[cid_nome]','$row[cid_codigo]','$row[cid_codigo_ibge]');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
			</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";
}
?>
