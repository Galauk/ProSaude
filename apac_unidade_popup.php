<?php
/**
 * @version Dudu 2007-09-19
 * @author Dudu 
 * 
 * 
 * obs: não sei de nada !!!
 * 
*/ 
// exibe uma busca/listagem/form das unidades (apac)

#cadaSTR

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
	echo "
	<form action='#' method='get' onsubmit='return busca_uni(\"$id_login\",\"$tipo\");'>
	<fieldset>
	<legend>Opções da Unidade</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick=\"form_uni('$id_login','$tipo')\"
				style='cursor:pointer;' />
		</td>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='hidden' name='acao' value='busca'>
			<input type='text' name='palavra_chave' id='uni_palavra_chave' class=box
				onChange=\"this.value=this.value.toUpperCase();\" />
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
	$where1 = empty($acao) ? '' : sprintf("WHERE u.uni_desc ILIKE '%%%s%%'", $str);
	$where2 = empty($acao) ? '' : sprintf("WHERE u2.uni_desc ILIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

// 	$stmt = sprintf(
// 	"(SELECT u.uni_codigo, u.uni_desc, u.uni_localizacao, u.uni_responsavel, u.uni_cnpj, 'N'
// 	FROM %s.unidade AS u %s ORDER BY u.uni_desc)
// 	UNION 
// 	(SELECT u2.uni_codigo, u2.uni_desc, u2.uni_localizacao, u2.uni_responsavel, u2.uni_cnpj, 'S' 
// 	FROM %s.apac_unidade AS u2 %s ORDER BY u2.uni_desc) %s",
// 		ESQ_SAUDE, $where1, ESQ_SAUDE, $where2, $sql_f );


	/*
	$stmt = sprintf(
	"(SELECT u.uni_codigo, u.uni_desc, u.uni_localizacao, u.uni_responsavel, u.uni_cnpj, 'N'
	FROM %s.unidade AS u %s order by 2)",
		ESQ_SAUDE, $where1, ESQ_SAUDE, $where2, $sql_f );
	*/
	
	$stmt = sprintf(
	"(SELECT u.uni_codigo, u.uni_desc, u.uni_localizacao, u.uni_responsavel, u.uni_cnpj, 'N'
	FROM %s.unidade AS u %s)
	UNION 
	(SELECT u2.uni_codigo, u2.uni_desc, u2.uni_localizacao, u2.uni_responsavel, u2.uni_cnpj, 'S' 
	FROM %s.apac_unidade AS u2 %s) ORDER BY 2 %s",
		ESQ_SAUDE, $where1, ESQ_SAUDE, $where2, $sql_f );
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando as $max primeiras Unidades";
	
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='40'>Código</th>
		<th>Descrição</th>
		<th style='text-align:center' width='100'>CNPJ</th>
		<th>Localização</th>
	<!--	<th width='35'>APAC</th> -->
		<th>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
       	  	<td class='c'>$row[0]</td>
	       	<td>$row[1]</td>
	       	<td class='c'>$row[4]</td>
	       	<td>$row[2]</td>
	<!--	<td class='c'>$row[5]</td> -->
	        <td width='230' class='c'>
				<a href='javascript:;'
					onclick=\"add_unidade('$row[0]','$row[1]','$tipo','$row[5]',".( $tipo == 3 ? "'$row[4]'" : "''").");\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
				".
				//( $row[5] == 'S' ?
					"<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
						onclick=\"form_uni('$id_login','$tipo','$row[0]')\" />
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
						onclick=\"apagar_uni('$id_login','$tipo','$row[0]')\" />".
				//	'' ).
			"</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";

}
/** Form add */
else if( $acao == 'form_add' || $acao == 'form_edit' )
{

	$acao2 = ( $acao == 'form_add' ? 'add' : 'edit' );

	if( $acao == 'form_edit'  )
	{
		//$stmt = sprintf("SELECT * FROM %s.apac_unidade WHERE uni_codigo = %d", ESQ_SAUDE, $codigo);
		$stmt = sprintf("SELECT * FROM %s.unidade WHERE uni_codigo = %d", ESQ_SAUDE, $codigo);		
		$row = db_getRow( $stmt );
	}
	else
		$row = array();

	echo "
	<form action='#' method='get' onsubmit='return form_uni_submit(\"$id_login\",\"$tipo\",\"$acao2\");'>
	<input type='hidden' name='codigo' id='codigo' value='$codigo' />
	<fieldset>
	<legend>Cadastro das Unidades</legend>
	<table>
		<tr>
			<td width='120'><label for='uni_desc'>Descrição</label></td>
			<td><input type='text' id='uni_desc' name='uni_desc' class='box' size='35' value='$row[uni_desc]' /></td>
		</tr>
		<tr>
			<td><label for='uni_localizacao'>Localização</label></td>
			<td><input type='text' id='uni_localizacao' name='uni_localizacao' class='box' size='35' value='$row[uni_localizacao]' /></td>
		</tr>
		<tr>
			<td><label for='uni_responsavel'>Responsável</label></td>
			<td><input type='text' id='uni_responsavel' name='uni_responsavel' class='box' size='35' value='$row[uni_responsavel]' /></td>
		</tr>
		<tr>
			<td><label for='uni_cnpj'>CNPJ</label></td>
			<td><input type='text' id='uni_cnpj' name='uni_cnpj' class='box' size='15' value='$row[uni_cnpj]' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' /></td>
		</tr>
	</table>
	</fieldset>
	</form>
	";
}
/** sql INSERT */
else if( $acao == 'add' )
{
	$stmt = sprintf("INSERT INTO %s.unidade
		(uni_desc, uni_localizacao, uni_responsavel, uni_cnpj,uni_tipo) VALUES
		(upper('%s'),upper('%s'),upper('%s'), upper('%s'),'A')",
		ESQ_SAUDE, $uni_desc, $uni_localizacao, $uni_responsavel, $uni_cnpj);
	
	db_query( $stmt );
	
	print "<p class='aviso'>Unidade Inserida.</p>";
}
/** sql UPDATE */
else if( $acao == 'edit' )
{
	$stmt = sprintf("UPDATE %s.unidade SET
		uni_desc = upper('%s'), 
		uni_localizacao = upper('%s'), 
		uni_responsavel = upper('%s'),
		uni_cnpj = upper('%s')
		WHERE uni_codigo = %d",
		ESQ_SAUDE, $uni_desc, $uni_localizacao, $uni_responsavel, $uni_cnpj, $codigo );
	
	db_query( $stmt );
	
	print "<p class='aviso'>Unidade Atualizada.</p>";

}
/** sql DELETE */
else if( $acao == 'del' )
{
	/*
	print $stmt = sprintf("DELETE FROM %s.unidade WHERE uni_codigo = %d",
		ESQ_SAUDE, $codigo );
	
	db_query( $stmt ); */
	
	print "<p class='aviso'>Opera&ccedil;&atilde;o N&atilde;o Permitida.</p>";

}

echo "
</body>
</html>";

?>
