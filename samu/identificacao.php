<?php
	session_start(); 
?>
<center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="100%" align="center">
		    <p class="txFieldDestaque">
				IDENTIFICA&Ccedil;&Atilde;O DO CHAMADO E DA V&Iacute;TIMA:
			</p>
		</td>
	</tr>
</table>
</center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="12%">Dt Chamado:</td>
		<td width="12%">Hr Chamado:</td>
		<td width="15%">N&ordm; Solicita&ccedil;&atilde;o:</td>
		<td width="51%">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="data" size="10" value="" class="inputForm" readonly></td>
		<td><input type="text" name="horaChamado" size="10" maxlength="10" class="inputForm" readonly ></td>
		<td><input type="text" name="numSolicitacao" size="15" maxlength="15" class="inputForm" readonly></td>
		<td>&nbsp;</td>
	</tr>
</table>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="68%">Paciente:</td>
		<td width="15%">Sexo:</td>
		<td width="15%">Idade:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="text" name="paciente" size="80" maxlength="70" class="inputForm" >
		</td>
		<td>
			<select name="sexo" class="inputForm">
				<option value="-1">[- Selecione -]</option>
				<option value="M">Masculino</option>
				<option value="F">Feminino</option>
			</select>
		</td>
		<td><input type="text" name="idade" size="5" maxlength="3" class="inputForm" onkeypress=""></td>
	</tr>
</table>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="68%">Solicitante:</td>
		<td width="30%">Rela&ccedil;&atilde;o c/ a v&iacute;tima:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="solicitante" size="80" maxlength="70" class="inputForm" ></td>
		<td><input type="text" name="relacaoVitima" size="30" maxlength="30" class="inputForm" ></td>
	</tr>
</table>

<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="98%">Motivo da Chamada:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="motivoChamada" size="135" maxlength="250" class="inputForm"></td>
	</tr>
</table>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="98%" colspan="2">
		Origem:
		</td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td colspan="2">
		&nbsp;&nbsp;&nbsp;&nbsp;VP&nbsp;<input type="radio" value="vp" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;DOM&nbsp;<input type="radio" value="dom" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;RESGATE&nbsp;<input type="radio" value="resgate" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;SAMU B&nbsp;<input type="radio" value="samub" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;SAMU VIR&nbsp;<input type="radio" value="samuvir" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;CS&nbsp;<input type="radio" value="cs" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;PA&nbsp;<input type="radio" value="pa" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;HOSP&nbsp;<input type="radio" value="hosp" name="origem">
		&nbsp;&nbsp;&nbsp;&nbsp;OUTROS&nbsp;<input type="radio" value="outros" name="origem">
		</td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="48%">Local:</td>
		<td width="50%">Bairro:</td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td><input type="text" name="local" size="50" maxlength="250" class="inputForm" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
		<td><input type="text" name="bairro" size="50" maxlength="100" class="inputForm" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" ></td>
	</tr>
</table>

<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="15%">Fone:</td>
		<td width="53%">Ponto de Refer&ecirc;ncia:</td>
		<td width="28%">Tarm:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="fone" size="15" maxlength="15" class="inputForm" onkeypress="return validaCampo(this, 15, jsValidTelefone);" ></td>
		<td><input type="text" name="pontoRef" size="70" maxlength="255" class="inputForm" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
		<td>
			<input type="text" name="tarm" size="25" maxlength="255" class="inputForm" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" >
			<a id='lnkPesquisaUsuario' title='Pesquisar informações sobre um usuario' href='javascript:findUsuariosTarm();'><img border='0' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg' id='imgPesquisar' align="absmiddle"></a>
		</td>
	</tr>
</table>
<br>
<center>
	<div id="listaGeral" style="display: none; overflow: auto; height: 110px; width:730px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF">
	</div>
</center>
