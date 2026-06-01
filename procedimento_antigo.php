<?php
/**
 Cadastro dos procedimentos
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario( $hotkey = true );

//------------------------------------------------------------------>

echo "
	<fieldset>
	<legend>Opções</legend>
		".ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?')."
		".ChmodBtn($id_login,'manutencao_agenda_exames','manutencao_exame.php?')."
		".ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?')."
		".ChmodBtn($id_login,'procedimento','procedimento.php?')."
		".ChmodBtn($id_login,'laboratorio','laboratorio.php?')."
	</fieldset>";


?>
<script type="text/javascript" src="ajax_motor.js"></script>

<script type='text/javascript'>

function hotkey(eventname)
{
	if( eventname.keyCode == 27 )
		return esconde_janela( 'janela_lab' );

	if( eventname.keyCode == 114 )
		return redir_form_add();

}
var PAGINA = 'laboratorio_popup.php?id_login=<?=$id_login;?>';

function cad_laboratorio( txt )
{
	document.getElementById('janela_lab_conteudo').innerHTML = txt;
}


function busca_lab()
{
	var pc = document.getElementById('palavra_chave').value;
	var endereco = PAGINA+'&acao=busca&palavra_chave='+pc;
	ajax_tudo(endereco,busca_lab_cont);
	return false;
}

function busca_lab_cont( txt )
{
	document.getElementById('janela_lab_conteudo').innerHTML = txt;
}

function apagar( med_codigo, proc_nome )
{
	var c = confirm("Remover o Procedimento "+ proc_nome +" ?");
	if( c )
	{
		var endereco = PAGINA+'&acao=del&med_codigo='+med_codigo;
		ajax_tudo(endereco, apagar2);
	}
}

function apagar2( txt )
{
	busca_lab_cont( txt );
	setTimeout('init()', 3*1000);
}

function init()
{
	ajax_tudo( PAGINA, cad_laboratorio )
}

function form_laboratorio( med_codigo )
{
	//$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&popup=$popup
	
	var acao 	= document.getElementById('acao').value;
 	var crm 	= document.getElementById('med_crm').value;
	var uf_crm 	= document.getElementById('uf_codigo_crm').value;
 	var nome 	= document.getElementById('med_nome').value;
	var email 	= document.getElementById('med_email').value;
	var end 	= document.getElementById('med_endereco').value;
	var cid 	= document.getElementById('cid_codigo').value;
	var cpf		= document.getElementById('med_cpf').value;
	var rg 		= document.getElementById('med_rg').value;
	
		if( ! valida('med_nome','Nome') )
	{
		return false;
	}
	
	var endereco = PAGINA+'&acao='+acao+'&med_crm='+crm+'&uf_codigo_crm='+uf_crm+'&med_nome='+nome+'&med_email='+email+'&med_endereco='+end+'&cid_codigo='+cid+'&med_cpf='+cpf+'&med_rg='+rg+'&med_codigo='+med_codigo;
	
	ajax_tudo( endereco, form_laboratorio2 );
	
	return false;
}

function form_laboratorio2( txt )
{
	busca_lab_cont( txt );
	setTimeout('init()', 3*1000);
}


function redir_form_add()
{
	var endereco = PAGINA+'&acao=form_add';
	ajax_tudo( endereco, cad_laboratorio );	
	return false;
}

function redir_form_edit( med_codigo )
{
	var endereco = PAGINA+'&acao=form_edit&med_codigo='+med_codigo;
	ajax_tudo( endereco, cad_laboratorio );
	return false;
}

</script>
<?php
	
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
	<form action='$PHP_SELF?id_login=$id_login&acao=busca' method='post'>
	<fieldset>
	<legend>Opções do Procedimento</legend>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=95>
			<a href='$PHP_SELF?id_login=$id_login&acao=form_add'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' border=0></a>
		</td>
		<input type='hidden' name='acao' value='busca'>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type=text name=palavra_chave class=box
				onChange=\"this.value=this.value.toUpperCase();\">
		</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
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
	$where 	= empty($acao) ? '' : sprintf("WHERE proc_nome LIKE '%%%s%%'", $str);
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'ORDER BY proc_codigo DESC LIMIT '.$max : 'ORDER BY proc_nome';

	$stmt 	= sprintf(
			"SELECT proc_codigo, proc_nome, proc_valor, p.gex_tipo, proc_sipac, med_nome 
			FROM %s.procedimento AS p 
			LEFT JOIN %s.medico AS m ON m.med_codigo = p.med_codigo
			%s	%s", ESQ_SAUDE, ESQ_SAUDE, $where, $sql_f);
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max últimos Procedimentos Cadastrados";
		
	/**
	 * @brief Listando / Buscando
	*/
	// ------------------------------------------------------------------>
  	echo "
	<fieldset>
	<legend>$resp</legend>
		<table width=100% align=center cellspacing=2 cellpadding=4 border=0 class='lista'>
		<tr bgcolor='#ffffff'>
		<th width='40'>Código</th>
		<th width='170'>Procedimento</th>
		<th width='120'>Laboratório</th>
		<th width='80'>Valor (R$)</th>
		<th width='20'>Tipo</th>
		<th width='20'>SIPAC</th>
		<th colspan='2'>&nbsp;</th>
	</tr>
	"; // se estiver aberto como um popup, ele vai deixar 'selecionar' um novo !

	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
	       	<td align='center'>$row[proc_codigo]</td>
	       	<td>$row[proc_nome]</td>
	       	<td>$row[med_nome]</td>
			<td align='center'>".formata_valor($row['proc_valor'])."</td>
	       	<td align='center'>$row[gex_tipo]</td>
			<td align='center'>$row[proc_sipac]</td>
	        <td width='60'>
	       		<a href=$PHP_SELF?id_login=$id_login&acao=form_edit&proc_codigo=$row[proc_codigo]>
	       			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border=0></a>
			</td>
			<td width='60'>		
	       		<a href='$PHP_SELF?id_login=$id_login&acao=del&proc_codigo=$row[proc_codigo]'
	       			onclick=\"return confirm('Remover o ítem \'$row[proc_nome]\' ?')\">
	       			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0'></a>
	       	</td>
	     </tr>";
     }
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
	    <legend>Opções de Procedimento</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	";

	// -> Formulario propriamente dito
	// quando a acao NAO FOR form_edit, o row sera vazio
	if( $acao != 'form_edit' )
		$row = array();
	else
	{
		$stmt = sprintf(
			'SELECT p.*, m.med_nome FROM %s.procedimento AS p
			LEFT JOIN %s.medico AS m ON m.med_codigo = p.med_codigo
			WHERE proc_codigo=%d', 
			ESQ_SAUDE, ESQ_SAUDE, $proc_codigo );
		$row = db_getRow($stmt);	
	}

	if( $acao == 'form_add' ) 		$acao_form = 'add';
	elseif( $acao == 'form_edit' ) 	$acao_form = 'edit';

	echo 
		monta_janela('janela_lab','Laboratório').
	"
	<form method=post action='$PHP_SELF?id_login=$id_login&proc_codigo=$proc_codigo'
		onsubmit=\"return valida('proc_nome','Nome') && valida('med_nome_r','Laboratório')\">
	<input type='hidden' name='acao' value='$acao_form'>
	<fieldset>
	<legend>Cadastro de Procedimento</legend>
	<p>Os campos em <span class='destaque'>destaque</span> são obrigatórios !</p>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width='110' class='destaque'>Nome</td>
		<td><input type='text' id='proc_nome' name='proc_nome' class='box' size='50' 
			onchange='this.value=this.value.toUpperCase();' value='$row[proc_nome]'></td>
	</tr>
	<tr>
		<td class='destaque'>Laboratório</td>
		<td>
		<input type='hidden' name='med_codigo' id='med_codigo' value='$row[med_codigo]' />
		<input type='text' name='med_nome_r' id='med_nome_r' class='box' readonly size='60' value='$row[med_nome]' />
		<a href='javascript:;'
		 	onclick=\"mostra_janela('janela_lab');init()\">
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>	
		</td>
	</tr>
	<tr>
		<td class='destaque'>Tipo</td>
		<td><select name='gex_tipo' class='box'>";
			
	//
	// gex_tipo	
	foreach( $GEX_TIPO as $value => $txt )
	{
		$selected = $value == trim($row['gex_tipo']) ? 'selected' : '';
		print "\n\t<option value='$value' $selected>$txt</option>";
	}
		
	echo "
			</select>
		</td>
	</tr>
	<tr>
		<td><label for='proc_valor'>Valor (R$)</label></td>
		<td><input type='text' id='proc_valor' name='proc_valor' class='box' size='8'
			value='$row[proc_valor]'></td>
	</tr>
	<tr>
		<td><label for='proc_valor'>Custo (R$)</label></td>
		<td><input type='text' id='proc_valor_custo' name='proc_valor_custo' class='box' size='8'
			value='$row[proc_valor_custo]'></td>
	</tr>
	<tr>
		<td>Classifica&#231;ão AMB</td>
		<td><input type='text' name='proc_classificacao_amb' class='box' size='12'
			value='$row[proc_classificacao_amb]'></td>
	</tr>
	<tr>
		<td>Classifica&#231;ão SUS</td>
		<td><input type='text' name='proc_classificacao_sus' class='box' size='12'
			 value='$row[proc_classificacao_sus]'></td>
	</tr>
	<tr>
		<td>Exame</td>
		<td><select name='proc_exame' class='box'>
				<option value='S'".( $row['proc_exame']=='S' ? ' selected' : '' ).">SIM</option>
				<option value='N'".( $row['proc_exame']=='N' ? ' selected' : '' ).">NÃO</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Intervalo Mínimo</td>
		<td><input type='text' name='proc_intervalo_min' class='box' size='4' maxlength='3'
			 value='$row[proc_intervalo_min]'></td>
	</tr>
	<tr>
		<td valign='top'>SIPAC</td>
		<td>
		<label><input type='radio' name='proc_sipac' class='box' value='S'
		".( $row['proc_sipac'] == 'S' || empty ($row['proc_sipac']) ? 'checked' : '')." > Sim </label><br />
		<label><input type='radio' name='proc_sipac' class='box' value='N'
		".( $row['proc_sipac'] == 'N' || empty ($row['proc_sipac']) ? 'checked' : '')." > Não </label>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/".
		( $acao == 'form_add' ? 'adicionar_on.jpg' : 'editar_on.jpg').
		"'></td>
	</tr>
	</table>
	</fieldset>
	</form>";
	
}//fechamento do if

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>

//-> ADD <---------------------------------------------------------->
elseif($acao=="add")
{
	$stmt = sprintf(
	"INSERT INTO %s.procedimento 
		(med_codigo, proc_nome, proc_classificacao_amb, proc_classificacao_sus, proc_exame, 
		proc_valor, gex_tipo, proc_intervalo_min, proc_valor_custo, proc_sipac )
	VALUES ( %d, upper('%s'), '%s', '%s', '%s', %f, '%s', %d, %f, '%s' )",
		ESQ_SAUDE,
		$med_codigo, $proc_nome, $proc_classificacao_amb, $proc_classificacao_sus,
		$proc_exame, $proc_valor, $gex_tipo, $proc_intervalo_min, $proc_valor_custo, $proc_sipac );
	
    $sql = db_query($stmt);

	msg($id_login,$acao,$sql);
}
//
//-> EDIT <--------------------------------------------------------->
elseif($acao=="edit")
{
	$stmt = sprintf( 
	"UPDATE %s.procedimento SET
		med_codigo = %d, proc_nome = '%s', proc_classificacao_amb = '%s', 
		proc_classificacao_sus = '%s', proc_exame = '%s', proc_valor = %f, 
		gex_tipo = '%s', proc_intervalo_min = %d, proc_valor_custo = %f, 
		proc_sipac = '%s'
	WHERE proc_codigo = %d", 
		ESQ_SAUDE, $med_codigo, $proc_nome, $proc_classificacao_amb,
		$proc_classificacao_sus, $proc_exame, $proc_valor, $gex_tipo, 
		$proc_intervalo_min, $proc_valor_custo, $proc_sipac, $proc_codigo  );

	$sql = db_query( $stmt );
	msg($id_login,$acao,$sql);
}
//
//-> DEL <---------------------------------------------------------->
else if($acao=="del")
{
	$stmt = sprintf( "DELETE FROM %s.procedimento WHERE proc_codigo=%d",
		ESQ_SAUDE, $proc_codigo );
	
	$sql = db_query($stmt);
	
	msg($id_login,$acao,$sql);

}

?>

</body>
</html>