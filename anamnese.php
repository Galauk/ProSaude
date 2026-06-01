<?php
/**
 Cadastro da ANAMNESE
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."anamnese.inc.php";

Cabecario( $hotkey = false );

verauth($id_login);

$Anamnese = & new Anamnese( $id_login );

/** opções */
echo "
	<form action='?id_login=$id_login&amp;acao=busca' method='post'>
	<fieldset>
	<legend>ANAMNESE</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' style='cursor:pointer;'
				onclick=\"document.location.href='$_SERVER[PHP_SELF]?id_login=$id_login&amp;acao=form_add'\" />
		</td>
		<td width='30'>Buscar:</td>
		<td width='120'>
			<input type='text' name='palavra_chave' id='palavra_chave' class='box' value='$palavra_chave'
				onChange=\"this.value=this.value.toUpperCase();\" />
		</td>
		<td width='85'>
			<select name='busca_tipo' id='busca_tipo' class='box'>
				<option value='0'".( $busca_tipo==0 ? ' selected' : '' ).">Todas</option>
				";
					$qry_tp = db_query( "SELECT * FROM anamnese_tipo ORDER BY 2" );
					while( $row_tp = pg_fetch_row($qry_tp) )
					{
						$s 	= ( $busca_tipo == $row_tp[0] ? 'selected' : '' );
						$busca_tipo_desc = ( $busca_tipo == $row_tp[0] && empty($busca_tipo_desc) ? 
							$row_tp[1] : $busca_tipo_desc );
						print "\n\t\t\t<option value='$row_tp[0]' $s>$row_tp[1]</option>";
					}
					
				echo "
			</select>
		</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
	</tr>
	</table>
	</fieldset>
	</form>";
	
/** Incluindo o javascript */

echo '
	<script type="text/javascript" src="funcoes.js"></script>
	<script type="text/javascript" src="ajax_motor.js"></script>
	<script type="text/javascript" src="anamnese.js"></script>';
	
/** Formulário */
if( $acao == 'form_add' || empty($acao)	|| $acao == 'form_edit' )
{
	
	if( $acao == 'form_edit' && $id )
	{	
		$id 	= intval($id);
		$row 	= db_getRow( 'SELECT * FROM anamnese WHERE ana_codigo='.$id );
		$acao_f = 'edit&amp;id='.$id;
		
		$stmt_r 	= "SELECT ana_tp_codigo FROM anamnese_tipo_rel WHERE ana_codigo=$id";
		$qry_r 		= db_query($stmt_r);
		$relacoes	= array();
		
		while( $row_r = pg_fetch_row($qry_r) )
			$relacoes[] = $row_r[0];
			
	}
	else
	{
		$row = array();
		$acao_f = 'add';
	}

?>

<form action="?id_login=<?=$id_login;?>&amp;acao=<?=$acao_f;?>" method="post" id="form_ana"
	onsubmit="return valida_form_ana('<?=$id_login?>')">
<fieldset>
<legend>Cadastro de ANAMNESE</legend>
	<table border="0" cellpadding="2">
		<tr>
			<td width="140"><label for="ana_questao">Questão</label></td>
			<td>
				<input type="text" name="ana_questao" id="ana_questao" class="box" size="75"
					value="<?=$row['ana_questao'];?>" />
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="ana_tp_codigo">Relação</label>
				<br>
				<a href='javascript:;' onclick="add_relacao()">[ add ]</a>
				<br>
				<a href='javascript:;' onclick="sel_todas_relacao(false)">[ sel. todas ]</a>
				<br>
				<a href='javascript:;' onclick="sel_todas_relacao(true)">[ invert. sel. ]</a>
				<br>
				<a href='javascript:;' onclick="del_relacao()">[ deletar relação ]</a>
				<br>
				<a href='javascript:;' onclick="edit_relacao()">[ editar relação ]</a>
				</td>
			<td>
				<select name="ana_tp_codigo[]" id="ana_tp_codigo" class="box" size="6" multiple="multiple"
					style="width:550px;">
				<?php
					$qry_tp = db_query( "SELECT * FROM anamnese_tipo ORDER BY 2" );
					while( $row_tp = pg_fetch_row($qry_tp) )
					{
						$s = ( in_array($row_tp[0], $relacoes) ? 'selected' : '' );
						print "\n\t\t\t<option value='$row_tp[0]' $s>$row_tp[1]</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ana_tp_resposta">Tipo da Reposta</label></td>
			<td>
				<select name="ana_tp_resposta" id="ana_tp_resposta" class="box" onchange="tp_resposta(this)">
					<option value="1" <?=($row['ana_tipo_resposta'] == '1' ? 'selected' :'');?>>Texto Pequeno (1 linha)</option>
					<option value="2" <?=($row['ana_tipo_resposta'] == '2' ? 'selected' :'');?>>Texto Grande (2 linhas ou +)</option>
					<option value="3" <?=($row['ana_tipo_resposta'] == '3' ? 'selected' :'');?>>Opções (uma resposta)</option>
					<option value="4" <?=($row['ana_tipo_resposta'] == '4' ? 'selected' :'');?>>Opções (várias respostas)</option>
				</select>
			</td>
		</tr>
		<!-- ana_tp_resposta == 2 -->
		<tr id="tr_opt_2" style="display:<?=( $row['ana_tipo_resposta'] == '2' ? 'table-row' : 'none' );?>;;">
			<td><em>Extras (textarea)</em></td>
			<td>
				Quantidade de linhas
				<input type="text" name="ana_resposta_opt_2" id="ana_tp_resposta_opt_2" class="inputForm" size="3"
					maxlength="2" value="<?=$Anamnese->prepara_opt_edit($row['ana_tipo_resposta_opt']);?>" />
			</td>
		</tr>
		<!-- /2 -->
		<!-- ana_tp_resposta == 3 -->
		<tr id="tr_opt_3" style="display:<?=( $row['ana_tipo_resposta'] == '3' ? 'table-row' : 'none' );?>;">
			<td valign="top"><em>Extras (radio)</em>
			<br>
			Atalhos:<br>
			<a href='javascript:;' onclick="add_sim_nao()">[ sim/não ]</a><br>
			<a href='javascript:;' onclick="add_normal_anormal()">[ normal/anormal ]</a>
			
			</td>
			<td>
				Opções 
				(<em>Obs: separe cada uma das opções por linha</em>)
				<br />
				<textarea class="box" id="ana_tp_resposta_opt_3" name="ana_resposta_opt_3" rows="5"
					cols="90"><?=$Anamnese->prepara_opt_edit($row['ana_tipo_resposta_opt']);?></textarea>
			</td>
		</tr>
		<!-- /3 -->
		<!-- ana_tp_resposta == 4 -->
		<tr id="tr_opt_4" style="display:<?=( $row['ana_tipo_resposta'] == '4' ? 'table-row' : 'none' );?>;;">
			<td valign="top"><em>Extras (checkbox)</em></td>
			<td>
				Opções 
				(<em>Obs: separe cada uma das opções por linha</em>)
				<br />
				<textarea class="box" id="ana_tp_resposta_opt_4" name="ana_resposta_opt_4" rows="5"
					cols="90"><?=$Anamnese->prepara_opt_edit($row['ana_tipo_resposta_opt']);?></textarea>
			</td>
		</tr>
		<!-- /3 -->
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/adicionar_on.jpg" alt="Enviar" />
				<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/limpar_on.jpg" alt="Limpar ?"
					onclick="document.getElementById('form_ana').reset();return false;" />
			</td>
		</tr>
	</table>
</fieldset>
</form>


<?php

} // form_add

/** buscando **/
else if( $acao == 'busca' )
{

	/** Construindo a busca */
	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
	
	if( ! empty($str) || ! empty($busca_tipo) )
	{
		$where  = 'WHERE ';
		$where .= ( ! empty($str) ? "TO_ASCII(ana_questao) ILIKE TO_ASCII('%$palavra_chave%')" : "" );
		$where .= ( ! empty($str) && ! empty($busca_tipo) ? ' AND ' : '' );
		$where .= ( ! empty($busca_tipo) ? 
					"ana_codigo IN (SELECT ana_codigo FROM anamnese_tipo_rel 
										WHERE ana_tp_codigo=$busca_tipo)" :
					"" );
	}
	// se NAO for busca, limitar 
	$sql_f 	= empty($palavra_chave) ? 'LIMIT '.$max : '';

	$stmt = "SELECT ana_codigo, ana_questao, ana_tipo_resposta, ana_tipo_resposta_opt 
		FROM anamnese
		$where	ORDER BY 1 DESC $sql_f";
	
	//print '<pre>'.$stmt.'</pre>';

	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && (! empty($str) || ! empty($busca_tipo) ) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
		
		if( ! empty($busca_tipo) )
			$resp .= " para busca em '$busca_tipo_desc'";
		
	} else
		$resp = "Listando os $max primeiros Registros";
	
	echo "
	<fieldset>
	<legend>$resp</legend>
	
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='30'>Código</th>
		<th>Questão</th>
		<th>Reposta</th>
		<th>Relação</th>
		<th width='75'>&nbsp;</th>
	</tr>
	";

	while( $row = pg_fetch_array($qry) )
	{
		echo "
		<tr>
			<td>$row[ana_codigo]</td>
			<td>$row[ana_questao]</td>
			<td>".$Anamnese->resp_tb( $row['ana_tipo_resposta'], $row['ana_tipo_resposta_opt'] )."</td>
			<td>";
			
			$stmt_in = "SELECT ana_tp_descricao FROM anamnese_tipo AS t
				WHERE ana_tp_codigo IN 
				( SELECT ana_tp_codigo FROM anamnese_tipo_rel AS r 
					WHERE r.ana_codigo = $row[ana_codigo] )
			ORDER BY 1";
			
			$arr_in = array();
			$qry_in = db_query($stmt_in);
			while( $row_in = pg_fetch_row($qry_in) )
				$arr_in[] = $row_in[0];
			
			echo join($arr_in,', ')."
			</td>
			<td class='c'>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' alt='Editar' style='cursor:pointer;' 
					onclick='editar(\"$id_login\",\"$row[0]\")' />
			</td>
		</tr>";
	}

	print "
	</table>
	</fieldset>";

}
// SQL INSERT
else if( $acao == 'add' || $acao == 'edit' )
{
	if( $ana_tp_resposta == 1 )
	{	
		$opt = 'null';
	}
	else if( $ana_tp_resposta == 2 )
	{
		$opt = intval($ana_resposta_opt_2);
	}	
	else
	{
		$p = $_POST[ 'ana_resposta_opt_'.$ana_tp_resposta ];
		$opt = $Anamnese->prepara_opt($p);
	}

	db_query('begin');

	if( $acao == 'add' )
	{
		$stmt1 = "INSERT INTO anamnese ( 
		ana_questao, 
		ana_tipo_resposta, 
		ana_tipo_resposta_opt
		) VALUES ( 
		'".trim(strtoupper($ana_questao))."', 
		'$ana_tp_resposta', 
		'".$opt."' )";
		
		$msg = "cadastrada";
	}
	else
	{
		$stmt1 = "UPDATE anamnese SET 
		ana_questao = '".trim(strtoupper($ana_questao))."', 
		ana_tipo_resposta = '$ana_tp_resposta', 
		ana_tipo_resposta_opt = '$opt'
		WHERE ana_codigo = $id";
		
		$msg = "alterada";
	}
	
	db_query($stmt1);
	
	// ana_codigo
	if( $acao == 'add' )
		$ac = db_get('SELECT MAX(ana_codigo) FROM anamnese');
	else
	{
		// limpando as relacoes
		db_query("DELETE FROM anamnese_tipo_rel WHERE ana_codigo = $id" );
		$ac = $id;
	}
	
	// relacao
	foreach($ana_tp_codigo as $k => $v )
	{
		$stmt2 = "INSERT INTO anamnese_tipo_rel ( 
		ana_codigo, 
		ana_tp_codigo
		) VALUES ( 
		".intval($ac).", 
		".intval($v)." )";
		
		db_query($stmt2);
	}
	
	db_query('commit');
	
	echo "
	<p class='aviso'>Questão $msg !</p>
	<script type='text/javascript'>
		setTimeout('document.location.href=\"$_SERVER[PHP_SELF]?id_login=$id_login&acao=\";', 3000);
	</script>
	";
}


/** fim */
echo "
</body>
</html>";