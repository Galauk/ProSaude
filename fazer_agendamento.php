<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//var_dump(getConfig("USU_CODIGO_RESPONSAVEL"))."KS VC?";
cabecario( $hotkey = true);

reglog($id_login,"Acessando Fazer Agendamento");
?>

<script
	type="text/javascript"
	src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script
	type="text/javascript"
	src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script
	type="text/javascript"
	src="fazer_agendamento.js.php?id_login=<?=$id_login?>"></script>
<script>

function editarPaciente(id){	
	usu_codigo = document.getElementById('pac_codigo').value;	
	var url ="paciente.php?acao=form&usu_codigo="+usu_codigo+"&id_login="+id+"&porta=S";
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
}
</script>
<!--<pre id='teste'>&nbsp;</pre>-->

<fieldset><legend>Prestador</legend>
<table cellpadding='4' border=0 class='table'>
	<tr>
		<td align='right'>Atividade prof.</td>
		<td><select id='esp_codigo' class='boxa' onchange='at_medico()'>
			<option value='0'>....</option>
			<?php
			$stmt = "SELECT DISTINCT(e.esp_codigo),
									esp_nome 
							   FROM especialidade AS e
							   JOIN medico_especialidade AS m
							     ON m.esp_codigo=e.esp_codigo
							  ORDER BY esp_nome";
			$qry = db_query( $stmt );
			while( $esp = pg_fetch_array($qry) )
			{
				echo "\n\t\t\t<option value='{$esp[0]}'>{$esp[1]}</option>";
			}
			?>

		</select></td>
		<td width='1'>&nbsp;</td>
		<td align='right' width='130' colspan='2'>Item de Agendamento</td>
		<td colspan='2' align='right'><select name='age_item' id='age_item'
			class='boxa' onchange='at_iframe_esq();'>
			<option value='0'>....</option>
			<option value='ES'>ESPECIALIDADE</option>
			<option value='CB'>CLÍNICA BÁSICA</option>
		</select></td>
	</tr>
	<tr>
		<td align='right'>Unidade de Saude/Laboratorio</td>
		<td><select name='uni_codigo' id='uni_codigo' class='boxa'
			onchange='at_iframe_esq();'>
			<option value='0'>....</option>
			<?php
			$stmt = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc";
			$qry = db_query($stmt);
			while( $uni = pg_fetch_array($qry) )
			{
				echo "\n\t\t\t<option value='{$uni[0]}'>{$uni[1]}</option>";
			}
			?>
			<option value='0'>________________ LABORATORIOS ________________</option>
			<?php
			$stmt = "SELECT med_codigo, med_nome FROM medico where prestador_servico='S' ORDER BY med_nome";
			$qry = db_query($stmt);
			while( $med = pg_fetch_array($qry) )
			{
				echo "\n\t\t\t<option value='{$med[0]}'>{$med[1]}</option>";
			}
			?>

		</select></td>
		<td>&nbsp;</td>
		<td align='right' colspan='2'>Profissional</td>
		<td colspan='2' align='right'><select id='med_codigo' class='boxa'
			disabled="disabled" onchange="preferencia_dia()">
			<option value='0'>....</option>
		</select></td>
	</tr>
	<tr>
		<td align='right'>Tipo de Agendamento</td>
		<td><select name='age_tipo' id='age_tipo' class='box'
			onchange='at_iframe_esq();'>
			<option value='0'>...</option>
			<option>PC</option>
			<option>GE</option>
			<option>RT</option>
			<option>AL</option>
			<option>CA</option>
			<option>CT</option>
			<option>DI</option>
			<option>EX</option>
		</select></td>
		<td>&nbsp;</td>
		<td align='right' width='90'>Preferência de dia&nbsp;</td>
		<td width='10'><input type='text' id='pref_dia' class='boxl' size='12'
			maxlength='10' onKeypress="return Ajusta_Data(this, event);"
			onchange="preferencia_dia(true)" /></td>
		<td width='135' align='right'>Preferência de Horário&nbsp;</td>
		<td align='right'><select id='pref_horario' class='boxl'
			disabled='disabled' style='width: 85px;' onchange='at_iframe_esq()'>
			<option value='0'>...</option>
		</select></td>
	</tr>
</table>
			<?php /*
			<table border="0" width="100%">
			<tr>
			<td width='153' align="right">
			Procedimentos
			</td>
			<td width="50" align="left">
			<select name="proc_codigo" id='proc_codigo' class='boxl' onchange='at_iframe_esq();' style="width:450px">
			<option value="">--SELECIONE--</option>
			<?php
			$sqlProc = "select proc_codigo_sus ,TRANSLATE(proc_nome, 'ZZZ-', '')as proc_nome from procedimento where proc_exame = 'N'  order by proc_nome ";
			$queryProc = pg_query($sqlProc);
			while($linha = pg_fetch_array($queryProc)){
			//						$param = "title='$linha[proc_nome]' style='width:450px;'";
			echo "<option value=$linha[proc_codigo_sus] title='".trim($linha[proc_nome])."' style='width:450px;'>$linha[proc_nome]</option>";
			//echo "<option value=123>123</option>";
			}
			?>
			</select>
			</td>
			</tr>
			</table> */ ?></fieldset>

<fieldset><legend>Agente de saúde</legend>
<table cellpadding='4' class='table'>
	<tr>
		<td width='113' align=right>Agente de saúde</td>
		<td><select id='agt_codigo' class='boxa' onchange='at_agente()'>
			<option value='0'>....</option>
			<?php
			$id_login = intval($id_login);

			// sqls especifico para APUCARANA
			$usr_uni_codigo = db_get("SELECT uni_codigo FROM usuarios
											WHERE usr_codigo = $id_login" );
			/*
			 $stmt = 'SELECT a.agt_codigo, a.agt_descricao
			 FROM usuarios AS u
			 INNER JOIN agente AS a ON
			 ( a.uni_codigo = u.uni_codigo OR agt_codigo IN (384931,393519) )
			 WHERE usr_codigo = '.$id_login . '
			 ORDER BY agt_descricao';
			 */

			$stmt = "SELECT agt_codigo, agt_descricao ".
								"FROM agente ".
			( empty($usr_uni_codigo) ? '' :
									"WHERE uni_codigo = $usr_uni_codigo OR ".
										"agt_codigo IN (384931,393519) " ).
								"ORDER BY agt_descricao";

			$qry = db_query($stmt);

			while($agt=pg_fetch_array($qry))
			{
				echo "\n\t\t\t<option value='{$agt[0]}'>{$agt[1]}</option>";
			}
			?>

		</select></td>
		<td><input type='text' id='agt_numero' class='boxl' size='15'
			readonly='readonly' /></td>
		<td><input type='text' id='agt_responsavel' class='boxl' size='50'
			readonly='readonly' /></td>

	</tr>
</table>
</fieldset>

<fieldset><legend>Dados do Paciente<span id='pac_busca_status'
	style='font-style: italic;'>&nbsp;</span></legend>
<table cellpadding='4' class='table' border=0>
	<tr>
		<td width='80' align='right'>Prontu&aacute;rio</td>
		<td width='120'><input type='hidden' id='pac_codigo' size='10' /> <input
			type='text' id='pac_prontuario' class='boxl' size='10'
			onchange="busca_pac_prontuario()" /></td>
		<td align='right'>Paciente</td>
		<td style='white-space: nowrap;'><input type='text' id='pac_nome'
			class='boxl' size='60' readonly='readonly'> <a href='#'
			onclick="link_f7()"> <img
			src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg'
			style='vertical-align: middle' border='0' alt='Localizar' /> </a>(F7)
		</td>
		<td>Nascimento</td>
		<td><input type='text' id='pac_nascimento' class='boxl' size='12'
			readonly='readonly' /></td>
	</tr>
	<tr>
		<td align='right'>Cidade</td>
		<td><input type='text' id='pac_cidade' class='boxl' size='20'
			readonly='readonly'></td>
		<td align='right' rowspan="3">M&atilde;e</td>
		<td rowspan="3"><input type='text' id='pac_mae' class='boxl' size='50'
			readonly='readonly' /></td>
			<?php if(getConfig("USU_CODIGO_RESPONSAVEL") == TRUE ){?>
		<td align='left' rowspan="3">Respons&aacute;vel</td>
		<td rowspan="3"><input type='text' id='pac_res' class='boxl' size='50'
			readonly='readonly' /></td>
			<?}else{?>
		<td rowspan="3"><input type='hidden' id='pac_res' class='boxl'
			size='50' readonly='readonly' /></td>
			<?php }?>
		<td colspan='2' style='white-space: nowrap;' rowspan="3">
		<table>
			<tr>
				<td><?php 
				$common = new commonClass();
				echo $common->commonButton("Editar paciente", null, "editar_on.png","onclick=editarPaciente('$_GET[id_login]')");
				echo $common->commonButton("CADASTRAR PACIENTE", null, "pacienteAdd.png", "onclick=\"link_f8()\"");
				?></td>
				<td><img id='btn_enviar'
					src='$_SESSION[linkroot].$_SESSION[comum];?>imgs/enviar_off.jpg'
					style='vertical-align: middle; cursor: pointer' alt='Enviar'
					onclick='at_iframe_esq(true);at_iframe_dir();' /></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php if(getConfig("USU_CODIGO_RESPONSAVEL") == TRUE ){?>
	<tr>
		<td align='right'>Área</td>
		<td><input type='text' id='area' class='boxl' size='20'
			readonly='readonly' /></td>
	</tr>
	<tr>
		<td align='right'>Micro Área</td>
		<td><input type='text' id='microarea' class='boxl' size='20'
			readonly='readonly' /></td>
	</tr>
	<tr>
		<td>
			Rua:
		</td>
		<td>
			<input type='text' id='rua_nome' class='boxl' size='50' readonly='readonly' />
		</td>
		<td>
			Numero:
		</td>
		<td>
			<input type='text' id='rua_bairro' class='boxl' size='15' readonly='readonly' />
		</td>
		<td>
			Bairro:
		</td>
		<td>
			<input type='text' id='dom_numero' class='boxl' size='50' readonly='readonly' />
		</td>
	</tr>
	<?php } ?>
</table>
</fieldset>

<table class="table">
	<tr>
		<td width='50%'>
		<fieldset><legend>Agendamento</legend> <iframe id='iframe_esq'
			name='iframe_esq' src='about:blank' frameborder='no' marginheight='0'
			marginwidth='0' scrolling='yes' width='100%' height='210'> </iframe>
		</fieldset>
		</td>
		<td>
		<fieldset><legend>Hist&oacute;rico</legend> <iframe id='iframe_dir'
			name='iframe_dir' src='about:blank' frameborder='no' marginheight='0'
			marginwidth='0' scrolling='yes' width='100%' height='210'> </iframe>
		</fieldset>
		</td>
	</tr>
</table>

<div id="teste"></div>
</body>
</html>
