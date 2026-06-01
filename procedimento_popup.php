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
		<input type='hidden' name='acao' value='busca'>
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
	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp = '';
	$max 	= 15;
	$where 	= empty($acao) ? '' : sprintf("WHERE proc_nome LIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'ORDER BY proc_codigo DESC LIMIT '.$max : 'ORDER BY proc_nome';

	$stmt 	= sprintf(
			"SELECT proc_codigo, proc_nome, proc_valor, p.gex_tipo, med_nome 
			FROM %s.procedimento AS p 
			LEFT JOIN %s.medico AS m ON m.med_codigo = p.med_codigo
			%s	%s", ESQ_SAUDE, ESQ_SAUDE, $where, $sql_f);
	//print $stmt;	
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
		<th width='20'>Tipo</th>
		<th>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
	       	<td align='center'>$row[proc_codigo]</td>
	       	<td>$row[proc_nome]</td>
	       	<td>$row[med_nome]</td>
			<td align='center'>".formata_valor($row['proc_valor'])."</td>
	       	<td align='center'>$row[gex_tipo]</td>
	        <td width='60'>		
				<a href='javascript:;'
					onclick=\"atualiza_proc('$row[proc_codigo]','$row[proc_nome]','$row[med_nome]');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0'></a>
			</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";
	

?>
