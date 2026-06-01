<?php
// exibe uma busca/listagem/form dos médicos (apac)

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
	<form action='#' method='get' onsubmit='return busca_med(\"$id_login\",\"$tipo\");'>
	<fieldset>
	<legend>Opções de Médico</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick=\"form_med('$id_login','$tipo')\"
				style='cursor:pointer;' />
		</td>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='hidden' name='acao' value='busca'>
			<input type='text' name='palavra_chave' id='med_palavra_chave' class=box
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
	$where1 = empty($acao) ? '' : sprintf("AND m.med_nome ILIKE '%%%s%%'", $str);
	$where2 = empty($acao) ? '' : sprintf("WHERE a.med_nome ILIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

	$stmt = sprintf(
	"(SELECT m.med_codigo, m.med_nome, m.med_cpf, m.med_crm, 'N'
		FROM %s.medico AS m	WHERE prestador_servico = 'N' %s)
	UNION 
	(SELECT a.med_codigo, a.med_nome, a.med_cpf, a.med_crm, 'S'
		FROM %s.apac_medico AS a %s)
	ORDER BY 2 %s",
		ESQ_SAUDE, $where1, ESQ_SAUDE, $where2, $sql_f );
	
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max primeiros Médicos";
	
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='40'>Código</th>
		<th>Nome</th>
		<th width='105'>CPF</th>
		<th width='105'>CRM</th>
		<th width='35'>APAC</th>
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
	       	<td>$row[3]</td>
			<td class='c'>$row[4]</td>
	        <td width='230' class='c'>
				<a href='javascript:;'
					onclick=\"add_medico('$row[0]','$row[1]','$row[2]','$tipo','$row[4]');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
				".
				( $row[4] == 'S' ?
					"<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
						onclick=\"form_med('$id_login','$tipo','$row[0]')\" />
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
						onclick=\"apagar_med('$id_login','$tipo','$row[0]')\" />" :
					'' ).
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
		$stmt = sprintf("SELECT * FROM %s.apac_medico WHERE med_codigo = %d", ESQ_SAUDE, $med_codigo);
		$row = db_getRow( $stmt );
	}
	else
		$row = array();

	echo "
<form action='#' method='get' onsubmit='return form_med_submit(\"$id_login\",\"$tipo\",\"$acao2\");'>
<input type='hidden' name='med_codigo' id='med_codigo' value='$med_codigo' />
<fieldset>
<legend>Cadastro de Médico</legend>
<table>
	<tr>
		<td><label for=\"med_crm\">CRM</label></td>
		<td><input type=\"text\" name=\"med_crm\" id=\"med_crm\" size=\"15\"
			 maxlength=\"10\" class=\"box\" value=\"$row[med_crm]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"med_nome\">Nome</label></td>
		<td><input type=\"text\" name=\"med_nome\" id=\"med_nome\" size=\"35\"
			 maxlength=\"60\" class=\"box\" value=\"$row[med_nome]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"med_cpf\">CPF</label></td>
		<td><input type=\"text\" name=\"med_cpf\" id=\"med_cpf\" size=\"15\"
			 maxlength=\"11\" class=\"box\" value=\"$row[med_cpf]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"med_rg\">RG</label></td>
		<td><input type=\"text\" name=\"med_rg\" id=\"med_rg\" size=\"15\"
			 maxlength=\"15\" class=\"box\" value=\"$row[med_rg]\" />
		</td>
	</tr>
</table>
<table>
<tr>
	<td width='150'>&nbsp;</td>
	<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' /></td>
</tr>
</table>
</fieldset></form>";

}
/** sql INSERT */
else if( $acao == 'add' )
{
	$stmt = "INSERT INTO apac_medico (
	med_crm, 
	med_nome, 
	med_cpf, 
	med_rg
	 ) VALUES ( 
	'".trim(strtoupper(substr($med_crm,0,10)))."', 
	'".trim(strtoupper(substr($med_nome,0,60)))."', 
	'".trim(strtoupper(substr($med_cpf,0,11)))."', 
	'".trim(strtoupper(substr($med_rg,0,15)))."' )";
	
	db_query( $stmt );
	
	print "<p class='aviso'>Médico Inserido.</p>";
}
/** sql UPDATE */
else if( $acao == 'edit' )
{
	// SQL UPDATE
 	$stmt = "UPDATE apac_medico SET 
	med_crm = '".trim(strtoupper(substr($med_crm,0,10)))."', 
	med_nome = '".trim(strtoupper(substr($med_nome,0,60)))."', 
	med_cpf = '".trim(strtoupper(substr($med_cpf,0,11)))."', 
	med_rg = '".trim(strtoupper(substr($med_rg,0,15)))."'
	WHERE med_codigo = ".intval($med_codigo) ;
	
	db_query( $stmt );
	
	print "<p class='aviso'>Médico Atualizado.</p>";

}
/** sql DELETE */
else if( $acao == 'del' )
{
	$stmt = "DELETE FROM apac_medico WHERE med_codigo = ".intval($med_codigo);	
	
	//db_query( $stmt );
	
	print "<p class='aviso'>Médico Removido.</p>";

}

echo "
</body>
</html>";

?>