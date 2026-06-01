<?php
// exibe uma busca/listagem/form dos pacientes (apac)

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

verauth($id_login);

/** verifica se tem apac em 30 dias */
if( $acao == 'verifica' && ! empty($codigo) )
{

	$campo = ( $apac == 'S' ? 'pac_apac_codigo' : 'pac_codigo' );
	$stmt = "SELECT COUNT(apac_codigo)
	FROM apac WHERE $campo = $codigo AND CURRENT_DATE - apac_dt_cadastro < 30";
	$val = intval( db_get($stmt) );
	if( $val > 0 )
		print 'NOK';
	else
		print 'OK';
	die;
}

/** atualiza cpf */
if( $acao == 'atualiza_cpf' )
{

	if( empty($apac) || empty($codigo) )
			die("Erro: Escolha um Paciente antes !");

	if( empty($cpf) )
			die("Erro: Digite um CPF !");
	
	$cpf = substr($cpf,0,15);

	if( $apac == 'S' )
		$stmt = "UPDATE apac_paciente SET pac_cpf_cns = '$cpf' WHERE pac_codigo=$codigo";
	else
		$stmt = "UPDATE usuario SET usu_cpf = '$cpf' WHERE usu_codigo=$codigo";
	
	//print $stmt;
	db_query($stmt);

	//return "Paciente atualizado !";
	die;
}

//var_dump($_GET);die;

cabecario( $hotkey = false );

// busca
	echo "
	<form action='#' method='get' onsubmit='return busca_paci(\"$id_login\");'>
	<fieldset>
	<legend>Opções do Paciente</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick=\"form_paci('$id_login')\"
				style='cursor:pointer;' />
		</td>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='hidden' name='acao' value='busca'>
			<input type='text' name='palavra_chave' id='pac_palavra_chave' class='box'
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

	/** Construindo a busca */
	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
	//$where1 = empty($acao) ? '' : sprintf("WHERE u.usu_nomeu ILIKE '%%%s%%'", $str);
	$where1 = empty($acao) ? '' : sprintf("WHERE u.usu_nome ILIKE '%%%s%%'", $str);
	$where2 = empty($acao) ? '' : sprintf("WHERE p.pac_nome ILIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

	$stmt = sprintf(
	"(SELECT u.usu_codigo, u.usu_nome, u.usu_mae, u.usu_cpf, 'N'
		FROM %s.usuario AS u %s
	) 
	UNION ALL
	(SELECT p.pac_codigo, p.pac_nome, p.pac_mae_responsavel, p.pac_cpf_cns, 'S'
		FROM %s.apac_paciente AS p %s
	)
	ORDER BY 2
	%s",
		ESQ_SAUDE, $where1, ESQ_SAUDE, $where2, $sql_f );
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max primeiros Pacientes";
	
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='40'>Código</th>
		<th>Nome</th>
		<th>Mãe</th>
		<th width='95' style='text-align:center;'>CPF</th>
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
	       	<td>$row[2]</td>
	       	<td class='c'>$row[3]</td>
			<td class='c'>$row[4]</td>
	        <td width='230' class='c'>
				<a href='javascript:;'
					onclick=\"add_paci('$id_login','$row[0]', '$row[1]','$row[3]','$row[4]');\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
				".
				( $row[4] == 'S' ?
					"<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
						onclick=\"form_paci('$id_login','$row[0]')\" />
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
						onclick=\"apagar_paci('$id_login','$row[0]')\" />" :
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
		$stmt = sprintf("SELECT *, TO_CHAR( pac_dt_nasc, 'DD/MM/YYYY') AS pac_dt_nasc FROM %s.apac_paciente WHERE pac_codigo = %d", ESQ_SAUDE, $codigo);
		$row = db_getRow( $stmt );
		
		$checked_m = ($row['pac_sexo'] == 'M' ? 'checked="checked"' : '' );
		$checked_f = ($row['pac_sexo'] == 'F' ? 'checked="checked"' : '' );
		
	}
	else
		$row = array();


	echo "
	<form action='#' method='get' onsubmit='return form_paci_submit(\"$id_login\",\"$acao2\");'>
	<input type='hidden' name='codigo' id='codigo' value='$codigo' />
	<fieldset>
	<legend>Cadastro dos Pacientes</legend>
	<table>
		<tr>
			<td width='140'><label for='pac_nome'>Nome</label></td>
			<td><input type='text' id='pac_nome' name='pac_nome' class='box' size='35' 
				value='$row[pac_nome]' />
			</td>
		</tr>
		<tr>
			<td>SEXO</td>
			<td>
				<input type='radio' name='pac_sexo' id='pac_sexo_m' value='M' $checked_m/>
					<label for='pac_sexo_m'>Masculino</label>
					&nbsp; &nbsp;
				<input type='radio' name='pac_sexo' id='pac_sexo_f' value='F' $checked_f/>
					<label for='pac_sexo_f'>Feminino</label>
			</td>
		</tr>
		<tr>
			<td><label for='pac_cpf_cns'>CPF/CNS</label></td>
			<td><input type='text' id='pac_cpf_cns' name='pac_cpf_cns' class='box' size='15' maxlength='25'
				value='$row[pac_cpf_cns]' />
			</td>
		</tr>
		<tr>
			<td><label for='pac_mae_responsavel'>Mãe / Responsável</label></td>
			<td><input type='text' id='pac_mae_responsavel' name='pac_mae_responsavel' class='box' size='35'
				value='$row[pac_mae_responsavel]' />
			</td>
		</tr>
		<tr>
			<td><label for='pac_pai'>Pai</label></td>
			<td><input type='text' id='pac_pai' name='pac_pai' class='box' size='35'
				value='$row[pac_pai]' />
			</td>
		</tr>
		<tr>
			<td><label for='pac_telefone'>Telefone</label></td>
			<td><input type='text' id='pac_telefone' name='pac_telefone' class='box' size='15' maxlength='15'
				value='$row[pac_telefone]' /></td>
		</tr>
		<tr>
			<td><label for='pac_dt_nasc'>Data Nascimento</label></td>
			<td><input type='text' id='pac_dt_nasc' name='pac_dt_nasc' class='box' size='10'
				value='$row[pac_dt_nasc]' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" /></td>
		</tr>
	</table>
	
	<fieldset>
	<legend>Endereço</legend>

	<table>
		<tr>
			<td width='140'><label for='pac_end_rua'>Rua</label></td>
			<td><input type='text' id='pac_end_rua' name='pac_end_rua' class='box' size='25'
				value='$row[pac_end_rua]' /></td>
		</tr>
		<tr>
			<td><label for='pac_end_nr'>Número</label></td>
			<td><input type='text' id='pac_end_nr' name='pac_end_nr' class='box' size='5' maxlength='5'
				value='$row[pac_end_nr]' /></td>
		</tr>
		<tr>
			<td><label for='pac_end_compl'>Complemento</label></td>
			<td><input type='text' id='pac_end_compl' name='pac_end_compl' class='box' size='15'
				value='$row[pac_end_compl]' /></td>
		</tr>
		<tr>
			<td><label for='pac_end_bairro'>Bairro</label></td>
			<td><input type='text' id='pac_end_bairro' name='pac_end_bairro' class='box' size='15'
				value='$row[pac_end_bairro]' /></td>
		</tr>
		<tr>
			<td><label for='pac_end_cep'>CEP</label></td>
			<td><input type='text' id='pac_end_cep' name='pac_end_cep' class='box' size='10' maxlength='9'
				value='$row[pac_end_cep]' /></td>
		</tr>
		<tr>
			<td><label for='pac_end_cidade'>Cidade</label></td>
			<td><input type='text' id='pac_end_cidade' name='pac_end_cidade' class='box' size='25'
				value='$row[pac_end_cidade]' /></td>
		</tr>
	</table>
	</fieldset>
	
	<fieldset>
	<legend>Convênio</legend>
	<table>
		<tr>
			<td width='140'>
				<select name='pac_tem_convenio' id='pac_tem_convenio' class='box'>
					<option value='S'".($row['pac_tem_convenio'] == 'S' ? ' selected' : '').">Sim</option>
					<option value='N'".($row['pac_tem_convenio'] == 'N' ? ' selected' : '').">Não</option>
				</select>
			</td>
			<td>
				<input type='text' name='pac_convenio_nome' id='pac_convenio_nome' class='box' size='50'
					value='$row[pac_convenio_nome]' />
			</td>
		</tr>
	</table>
	</fieldset>
	
	<table>
		<tr>
			<td width='150'>&nbsp;</td>
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
	$stmt = sprintf("INSERT INTO %s.apac_paciente
		(pac_nome,
		pac_sexo,
		pac_cpf_cns,
		pac_mae_responsavel,
		pac_pai,
		pac_end_rua,
		pac_end_nr,
		pac_end_compl,
		pac_end_bairro,
		pac_end_cep,
		pac_end_cidade,
		pac_dt_nasc,
		pac_telefone,
		pac_tem_convenio,
		pac_convenio_nome) 
		VALUES
		('%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s','%s','%s')",
		ESQ_SAUDE,
		$pac_nome,
		$pac_sexo,
		$pac_cpf_cns,
		$pac_mae_responsavel,
		$pac_pai,
		$pac_end_rua,
		$pac_end_nr,
		$pac_end_compl,
		$pac_end_bairro,
		substr($pac_end_cep,0,9),
		$pac_end_cidade,
		$pac_dt_nasc,
		$pac_telefone,
		$pac_tem_convenio,
		( $pac_tem_convenio == 'N' ? '' : substr($pac_convenio_nome,0,200) ) );
	
	db_query( $stmt );
	
	print "<p class='aviso'>Paciente Inserido.</p>";
}
/** sql UPDATE */
else if( $acao == 'edit' )
{
	$stmt = sprintf("UPDATE %s.apac_paciente SET
		pac_nome = '%s', 
		pac_sexo = '%s',
		pac_cpf_cns = '%s',
		pac_mae_responsavel = '%s',
		pac_pai = '%s',
		pac_end_rua = '%s',
		pac_end_nr = '%s',
		pac_end_compl = '%s',
		pac_end_bairro = '%s',
		pac_end_cep = '%s',
		pac_end_cidade = '%s',
		pac_dt_nasc = '%s',
		pac_telefone = '%s',
		pac_tem_convenio = '%s',
		pac_convenio_nome = '%s'
		WHERE pac_codigo = %d",
		ESQ_SAUDE,
		$pac_nome,
		$pac_sexo,
		$pac_cpf_cns,
		$pac_mae_responsavel,
		$pac_pai,
		$pac_end_rua,
		$pac_end_nr,
		$pac_end_compl,
		$pac_end_bairro,
		substr($pac_end_cep,0,9),
		$pac_end_cidade,
		$pac_dt_nasc,
		$pac_telefone,
		$pac_tem_convenio,
		( $pac_tem_convenio == 'N' ? '' : substr($pac_convenio_nome,0,200) ),
		$codigo );
	
	db_query( $stmt );
	
	print "<p class='aviso'>Paciente Atualizado.</p>";

}
/** sql DELETE */
else if( $acao == 'del' )
{
	$stmt = sprintf("DELETE FROM %s.apac_paciente WHERE uni_codigo = %d",
		ESQ_SAUDE, $codigo );
	
	db_query( $stmt );
	
	print "<p class='aviso'>Paciente Removido.</p>";

}

echo "
</body>
</html>";

?>
