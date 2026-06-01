<?php
	session_start(); 
?>
<center> 
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="100%" align="center">
		    <p class="txFieldDestaque">
				DADOS DO MOTORISTA:
			</p>
		</td>
	</tr>
</table>
</center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">Motorista:</td>
		<td>VTR:</td>
		<td>Local de sa&iacute;da:</td>
		<td>Km Sa&iacute;da:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">
			<input type="text" name="motorista" size="30" maxlength="100" class="inputForm" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);">
			<a id='lnkPesquisaUsuario' title='Pesquisar informações sobre um usuario' href='javascript:findUsuariosMotorista();'><img border='0' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg' id='imgPesquisar' align="absmiddle"></a>
		</td>
		<td>
			<!--<input type="text" name="vtr" maxlength="100" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);">-->
		</td>
		<td>
		&nbsp;&nbsp;&nbsp;&nbsp;Base&nbsp;<input type="radio" value="outros" name="localSaida">
		&nbsp;&nbsp;&nbsp;&nbsp;Outros&nbsp;<input type="radio" value="base" name="localSaida">
		</td>
		<td>
			<input type="text" name="kmSaida" maxlength="100" class="inputForm" style="width: 100%;">
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td width="16%">Hora Sa&iacute;da:</td>
		<td width="16%">Hora chegada no local:</td>
		<td>Hora sa&iacute;da do local:</td>
		<td align="center" colspan="2">Situa&ccedil;&atilde;o da v&iacute;tima:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td >
			<input type="text" name="horaSaida" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" >			
		</td>
		<td >
			<input type="text" name="horaChegadaLocal" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" >			
		</td>
		<td >
			<input type="text" name="horaSaidaLocal" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" >					
		</td>
		<td colspan="2">
			&nbsp;&nbsp;Conci&ecirc;ncia:&nbsp;<input type="text" name="conciencia" maxlength="100" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" ><br>		
			&nbsp;&nbsp;Respira&ccedil;&atilde;o:&nbsp;<input type="text" name="respiracao" maxlength="100" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" >
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="5">Outros Dados:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="5">
			<input type="text" name="outrosDados" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" >			
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">Passagem do caso para a base:</td>
		<td colspan="2">Orienta&ccedil;&atilde;o:</td>
		<td>M&eacute;dico:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">
		&nbsp;&nbsp;Solicita apoio:
		&nbsp;&nbsp;Sim&nbsp;<input type="radio" value="sim" name="solicitaApoio">
		&nbsp;&nbsp;N&atilde;o&nbsp;<input type="radio" value="nao" name="solicitaApoio">
		</td>
		<td colspan="2">
			<input type="text" name="orientacao" size="50" maxlength="255" class="inputForm" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" >
		</td>
		<td>
			<input type="text" name="medico" size="25" maxlength="70" class="inputForm" onkeypress="return validaCampo(this, 70, jsValidCharsWithNumbersUp);" >
			<a id='lnkPesquisaUsuario' title='Pesquisar informações sobre um usuario' href='javascript:findUsuariosMedico();'><img border='0' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg' id='imgPesquisar' align="absmiddle"></a>
		</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">Evolu&ccedil;&atilde;o:</td>
		<td colspan="3">Motivo:</td>
	</tr>
	<tr>
		<td width="1%">&nbsp;</td>
		<td colspan="2">
		&nbsp;&nbsp;Transporte&nbsp;<input type="radio" value="transporte" name="evolucao">
		&nbsp;Aguardar Apoio&nbsp;<input type="radio" value="aguardar" name="evolucao">
		&nbsp;QTA&nbsp;<input type="radio" value="qta" name="evolucao">
		</td>
		<td colspan="3">
			<input type="text" name="motivo" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" >		
		</td>
	</tr>
	<tr>
		<td colspan=6>&nbsp;</td>
	</tr>
</table>
<center>
	<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/continuar_cadastro.png" />
</center>