<?php
/**
Cadastro dos laboratórios 
	medico.prestador_servico = 'S'
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario();
//------------------------------------------------------------------>

/**
 * @brief Secao Vazia, mostrando registros e botoes
*/
// ------------------------------------------------------------------>

if( empty($acao) || $acao == 'busca' )
{

	/**
	 * @brief Formulário de Busca
	*/
	// ------------------------------------------------------------------>
	echo "
	<form action='#' method='get' onsubmit=\"return busca_lab()\">
	<fieldset>
	<legend>Opções do Laboratório (popup)</legend>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=95>
			<a href='javascript:;' onclick='redir_form_add()' >
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' border=0></a>
		</td>
		<input type=hidden name=acao value=busca>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type='text' name='palavra_chave' id='palavra_chave' class='box'
				onChange=\"this.value=this.value.toUpperCase();\">
		</td>
		<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
	</tr>
	</form>
	</table>
	</fieldset>
	</form>";

	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp = '';
	$max 	= 15;
	$where 	= empty($acao) ? '' : sprintf("AND med_nome ILIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'ORDER BY med_codigo DESC LIMIT '.$max : 'ORDER BY med_nome';

	$stmt 	= sprintf(
			"SELECT 
				m.med_codigo, m.med_crm, m.med_nome, m.uf_codigo_crm, m.med_email,
				m.med_endereco, m.cid_codigo, m.med_cpf, m.med_rg,
				m.prestador_servico, m.gex_tipo, c.cid_nome, e.uf_sigla
			FROM %s.medico AS m
			LEFT JOIN %s.estado AS e ON e.uf_codigo = m.uf_codigo_crm
			LEFT JOIN %s.cidade AS c ON c.cid_codigo = m.cid_codigo
			WHERE m.prestador_servico = 'S' %s %s", 
			ESQ_SAUDE, ESQ_COMMON, ESQ_COMMON, $where, $sql_f);
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max últimos Laboratórios Cadastrados";
		
	/**
	 * @brief Listando / Buscando
	*/
	// ------------------------------------------------------------------>
  	echo "
	<fieldset>
	<legend>$resp</legend>
		<table width=100% align=center cellspacing=2 cellpadding=4 border=0 class='lista'>
		<tr bgcolor='#ffffff'>
		<th width='40'>C&oacute;digo</th>
		<th>Laborat&oacute;rio</th>
		<th width='170'>Cidade (UF)</th>
		<th colspan='3'>&nbsp;</th>
	</tr>
";

	while($row=pg_fetch_array($qry))
	{
       echo "
		<tr>
			<td align='center'>$row[med_codigo]</td>
			<td>$row[med_nome]</td>
			<td>$row[cid_nome] ($row[uf_sigla])</td>
			<td width='60'>
			<a href='javascript:;' onclick='return redir_form_edit(\"$row[med_codigo]\");'>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0'></a>
			</td>
			<td width='60'>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' style='cursor:pointer;'
						onclick=\"apagar('$row[med_codigo]','$row[med_nome]')\">
			</td>
			<td width='60'>		
				<a href='javascript:;'
					onclick=\"etapa2('$row[med_codigo]','$row[med_nome]')\">
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0'></a>
			</td>
		</tr>";
	}
// 'med_nome_r','med_codigo','$row[med_codigo]','$row[med_nome]'

	echo "
	</table>
	</fieldset>";
}
//------------------------------------------------------------------>
/**
 @brief Formulario de Adicao/Edicao de Conteudo
*/
//------------------------------------------------------------------>
elseif($acao=="form_add" || $acao=="form_edit")
{

	//-> Abaixo sao os botoes de voltar 
	echo "
	<fieldset>
	<legend>Opções de Laboratório</legend>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<tr>
		<td width=79><a href='javascript:;' onclick='init()'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border=0></a></td>
		<td>&nbsp;</td>
		</tr>
		</table>
	</fieldset>";

	// -> Formulario propriamente dito
	// quando a acao NAO FOR form_edit, o row sera vazio
	if( $acao != 'form_edit' )
		$row = array();
	else
	{
		$stmt = sprintf('SELECT * FROM %s.medico WHERE med_codigo=%d',
			ESQ_SAUDE, $med_codigo);
		$row = db_getRow($stmt);
	}

	if( $acao == 'form_add' ) 		$acao_form = 'add';
	elseif( $acao == 'form_edit' ) 	$acao_form = 'edit';

	echo "
	<form method='get' action='#' onsubmit=\"return form_laboratorio($med_codigo)\">
	<input type='hidden' name='acao' id='acao' value='$acao_form'>
	<fieldset>
	<legend>Cadastro de Laboratório</legend>
	<p>Os campos em <span class='destaque'>destaque</span> são obrigatório !</p>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width='70'>CRM:</td>
		<td>
			<input type='text' id='med_crm' name='med_crm' class='box' size='10' value='$row[med_crm]'>
		</td>
	</tr>
	<tr>
		<td>Estado CRM:</td>
		<td>
		<select name='uf_codigo_crm' id='uf_codigo_crm' class='box'>";
	//
	//-> SQL do Estado
	$stmt = sprintf("SELECT * FROM %s.estado ORDER BY uf_sigla", ESQ_COMMON);
	$query = db_query($stmt);
	while($uf=pg_fetch_array($query))
	{
		echo ($uf['uf_codigo']==$row['uf_codigo_crm']) ? 
			"<option value='$uf[uf_codigo]' selected>$uf[uf_sigla]</option>":
			"<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	}
	echo "
		</select>
		</td>
	</tr>
	<tr>
		<td class='destaque'>Nome:</td>
		<td>
			<input type='text' id='med_nome' name='med_nome' class='box' size='70' value='$row[med_nome]'>
		</td>
	</tr>
	<tr>
		<td>E-mail: </td>
		<td>
			<input type='text' name='med_email' id='med_email' class='box' size='50' value='$row[med_email]'>
		</td>
	</tr>
	<tr>
		<td>Rua:</td>
		<td><input type='text' name='med_endereco' id='med_endereco' class='box' size='60' value='$row[med_endereco]'></td>
	</tr>
	<tr>
		<td>Cidade:</td>
		<td>
		<select name='cid_codigo' id='cid_codigo' class=box>";
		//
		//-> SQL da Cidade
		$stmt = sprintf("SELECT cid_codigo, cid_nome FROM %s.cidade WHERE uf_codigo = 18 
			ORDER BY cid_nome", ESQ_COMMON);
		$query = db_query($stmt);
		while($cidade=pg_fetch_array($query))
		{
			echo ($cidade['cid_codigo']==$row['cid_codigo'])?
			"<option value='$cidade[cid_codigo]' selected>$cidade[cid_nome]</option>":
			"<option value='$cidade[cid_codigo]'>$cidade[cid_nome]</option>";
		}
	echo "
		</select>
		</td>
	</tr>
	<tr>
		<td>CPF:</td>
		<td><input type='text' name='med_cpf' id='med_cpf' class='box' size='20' value='$row[med_cpf]'></td>
	</tr>
	<tr>
		<td>RG:</td>
		<td><input type='text' name='med_rg' id='med_rg' class='box' size='20' value='$row[med_rg]'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<a href='javascript:;' onclick='init()'>
				<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;
				<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/".
				( $acao == 'form_add' ? 'adicionar_on.jpg' : 'editar_on.jpg').
				"'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

}//fechamento do if

/** Atualizando os dados da "relacao" Laboratorio X Procedimento */

elseif($acao=="etapa2")
{

	//-> Abaixo sao os botoes de voltar 
	echo "
	<fieldset>
	<legend>Opções de Laboratório</legend>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<tr>
		<td width=79><a href='javascript:;' onclick='init()'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border=0></a></td>
		<td>&nbsp;</td>
		</tr>
		</table>
	</fieldset>";

	echo "
	<form method='get' action='#' onsubmit=\"return form_etapa2('$med_codigo','$med_nome')\">
	<input type='hidden' name='acao' id='acao' value='$acao_form'>
	<input type='hidden' name='proc_tipo' id='proc_tipo' value='Q'>
	<fieldset>
	<legend>Sele&ccedil;&atilde;o do Laborat&oacute;rio</legend>
	
	<tr>
		<td>&nbsp;</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' /></td>
		<td><h3>Clique no botao adicionar para confirmar a inclusao ou no botao voltar para aborta-la</h3></td>
	</tr>
	</table>" ;
}
//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>

//-> ADD <---------------------------------------------------------->
elseif($acao=="add")
{
	$stmt = sprintf(
	"INSERT INTO medico ( " .
            "med_nome, " .
            "med_crm, " .
            "uf_codigo_crm, " .
            "med_email, " .
            "med_endereco, " .
            "cid_codigo, " .
            "med_cpf, " .
            "med_rg, " .
            "prestador_servico " .
            ") values ( " .
            "upper('$med_nome'), " .
            ($med_crm ? "'$med_crm'" : "NAOTEM") . ", " .
            ($uf_codigo_crm ? "'$uf_codigo_crm'" : "18") . ", " . //se nao for digitado grava o parana (18)
            ($med_email ? "'$med_email'" : "null") . ", " .
            ($med_endereco ? "'$med_endereco'" : "null") . ", " .
            ($cid_codigo ? "'$cid_codigo'" : "null") . ", " .
            ($med_cpf ? "'$med_cpf'" : "null") . ", " .
            ($med_rg ? "'$med_rg'" : "null") . ",  " .
            "'S'".
            ")");
            
    $sql = db_query($stmt);

	echo "<p class='aviso'>Laboratório inserido.</p>";
}
//
//-> EDIT <--------------------------------------------------------->
elseif($acao=="edit")
{
	$stmt = "UPDATE medico SET " .
            "med_nome=upper('$med_nome'), " .
            ($med_crm ? "med_crm='$med_crm'" : "med_crm='NAOTEM'") . ", " .
            "uf_codigo_crm='$uf_codigo_crm', " .
            ($med_email ? "med_email='$med_email'" : "med_email=null") . ", " .
            ($med_endereco ? "med_endereco='$med_endereco'" : "med_endereco=null") . ", " .
            ($cid_codigo ? "cid_codigo='$cid_codigo'" : "cid_codigo=null") . ", " .
            ($med_cpf ? "med_cpf='$med_cpf'" : "med_cpf=null") . ", " .
            ($med_rg ? "med_rg='$med_rg'" : "med_rg=null") . " " .
            "where med_codigo='$med_codigo'";
	
 	$sql = db_query( $stmt );
 	echo "<p class='aviso'>Laboratório atualizado.</p>";
}
//
//-> DEL <---------------------------------------------------------->
else if($acao=="del")
{
	$stmt = sprintf( "DELETE FROM %s.medico WHERE med_codigo=%d",
		ESQ_SAUDE, $med_codigo );
	
	//$sql = db_query($stmt);
	//echo "<p class='aviso'>Procedimento Removido !</p>";
	echo "<p class='aviso erro'>Esta operação está temporariamente indisponível !</p>";
}

?>
