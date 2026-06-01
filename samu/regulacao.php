<?php
	session_start(); 
?>
<center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="100%" align="center">
		    <p class="txFieldDestaque">
				REGULA&Ccedil;&Atilde;O M&Eacute;dica:
			</p>
		</td>
	</tr>
</table>
</center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="98%" colspan="5">Queixas:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>	
		<td width="98%" colspan="5">
			<textarea name="queixas" rows="3" class="txArea" style="width: 100%;"></textarea>
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="98%" colspan="5">Regula&ccedil;&atilde;o:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>	
		<td width="98%" colspan="5">
			<textarea name="regulacao" rows="3" class="txArea" style="width: 100%;"></textarea>
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="98%" colspan="5">HD:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>	
		<td width="98%" colspan="5">
			<input type="text" name="hd" maxlength="100" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" >			
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="80%" colspan="4">Resposta:</td>
		<td width="19%">Qti inicial:</td>		
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="80%" colspan="4">
		&nbsp;&nbsp;&nbsp;&nbsp;VSA&nbsp;<input type="radio" value="vsa" name="resposta">
		&nbsp;&nbsp;&nbsp;&nbsp;VSB&nbsp;<input type="radio" value="vsb" name="resposta">
		&nbsp;&nbsp;&nbsp;&nbsp;VRS&nbsp;<input type="radio" value="vrs" name="resposta">
		&nbsp;&nbsp;&nbsp;&nbsp;VPSQ&nbsp;<input type="radio" value="vpsq" name="resposta">
		&nbsp;&nbsp;&nbsp;&nbsp;VNEO&nbsp;<input type="radio" value="vneo" name="resposta">
		&nbsp;&nbsp;&nbsp;&nbsp;REORIENTA&Ccedil;&Atilde;O&nbsp;<input type="radio" value="reorientacao" name="resposta">				
		</td>
		<td width="19%">
		<input type="text" name="qtiInicial" maxlength="50" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 50, jsValidCharsWithNumbersUp);" >			
		</td>		
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td>Hora Resg:</td>
		<td>CRM:</td>
		<td>M&eacute;dico:</td>
		<td>Operador:</td>
		<td>Hora Despacho:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="10%">
			<input type="text" name="horaResgate" size="8" maxlength="5" class="inputForm" onkeypress="return validaCampo(this, 8, jsValidHora);" >			
		</td>
		<td width="10%">
			<input type="text" name="crm" size="8" maxlength="10" class="inputForm" onkeypress="return validaCampo(this, 8, jsValidCharsWithNumbersUp);" >			
		</td>
		<td width="30%">
			<input type="text" name="medicoRegulacao" size="30" maxlength="100" class="inputForm" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" >
			<a id='lnkPesquisaUsuario' title='Pesquisar informações sobre um usuario' href='javascript:findUsuariosRegulacao();'><img border='0' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg' id='imgPesquisar' align="absmiddle"></a>
		</td>
		<td width="30%">
			<input type="text" name="operador" size="30" maxlength="100" class="inputForm" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);">
			<a id='lnkPesquisaUsuario' title='Pesquisar informações sobre um usuario' href='javascript:findUsuariosOperador();'><img border='0' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg' id='imgPesquisar' align="absmiddle"></a>
		</td>
		<td width="10%">
			<input type="text" name="horaDespacho" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" >			
		</td>		
	</tr>
	<tr>
		<td colspan=6>&nbsp;</td>
	</tr>
</table>
<center>
	<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/continuar_cadastro.png" />
</center>