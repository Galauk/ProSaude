<?php
/**
 Cadastro do APAC 
 "Autorizacao de Procedimentos Ambulatoriais de Alta Complexidade/Custo"
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

Cabecario( $hotkey = false );

/** opções */
echo "
	<form action='#' method='get' onsubmit='return busca_apac(\"$id_login\");'>
	<fieldset>
	<legend>APAC - Autorização de Procedimentos Ambulatoriais de Alta Complexidade/Custo</legend>
	<table>
	<tr>
		<td width='120'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' style='cursor:pointer;'
				onclick=\"document.location.href='$_SERVER[PHP_SELF]?id_login=$id_login&acao=form_add'\" />
		</td>
		<td width='30'>Buscar:</td>
		<td width='120'>
			<input type='hidden' name='acao' value='busca'>
			<input type='text' name='palavra_chave' id='palavra_chave' class='box' value='$palavra_chave'
				onChange=\"this.value=this.value.toUpperCase();busca_apac('$id_login')\" />
		</td>
		<td width='85'>
			<select name='busca_tipo' id='busca_tipo' class='box'>
				<option value='1'".( $busca_tipo==1 ? ' selected' : '' ).">Paciente</option>
				<option value='2'".( $busca_tipo==2 ? ' selected' : '' ).">Médico Auditor</option>
				<option value='3'".( $busca_tipo==3 ? ' selected' : '' ).">Unidade Sol.</option>
				<option value='4'".( $busca_tipo==4 ? ' selected' : '' ).">Órgão Autor.</option>
			</select>
		</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

/** Incluindo o javascript */

echo '	<script type="text/javascript" src="ajax_motor.js"></script>';
//echo monta_janela_usu(  'jan_apac2' );

?>

<div class="janela" id="jan_apac2" style="width:100px;height:100px;border-top:2px solid #000;padding:8px;">
	asdasd
</div>

<form action="?id_login=<?=$id_login;?>&acao=add" method="post" 
	onsubmit="return valida_form_apac('<?=$id_login?>')">
<fieldset>
<legend>Cadastro de APAC</legend>
<table border="0">

	<tr>
		<td>Competência</td>
		<td colspan="3">
		<select id="mes_comp" name="mes_comp" class="box" onchange="document.getElementById('ano_comp').select();">
			<?php print meses_select( date('m') ); ?>
		</select>
		/
		<input type="text" name="ano_comp" id="ano_comp" class="box" size="4" maxlength="4" value="<?=date('Y');?>" />
		</td>
	</tr>


	<tr>
		<td width="120">Nome do Paciente</td>
		<td>
			<input type="hidden" name="apac_paci" id="apac_paci" />
			<input type="hidden" name="paci_codigo" id="paci_codigo" />
			<input type="text" name="paci_nome" id="paci_nome_r" class="box" size="50" readonly />
			<a href='javascript:;'
				onclick="mostra_janela('jan_apac2');">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td>CPF do Paciente</td>
		<td>
			<input type="hidden" name="paci_cpf" id="paci_cpf" />
			<input type="text" name="paci_cpf_r" id="paci_cpf_r" class="box" readonly />
		</td>
	</tr>
</table>

</fieldset>

<p><input type='image' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/adicionar_on.jpg' alt='Enviar' /></p>

</form>

</body>
</html>