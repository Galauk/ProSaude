<?php
	session_start(); 
?>
<!--  ########################## FINALIZACAO DO CASO ####################################  -->	
<center> 
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="100%" align="center">
		    <p class="txFieldDestaque">
				FINALIZA&Ccedil;&Atilde;O DO CASO:
			</p>
		</td>
	</tr>
</table>
</center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="20%">Confirma&ccedil;&atilde;o Destino:</td>
		<td width="10%">Hora Chegada:</td>
		<td width="10%">Hora Libera&ccedil;&atilde;o:</td>
		<td width="40%" colspan="2">Paciente recebido por:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="confDestino" maxlength="255"  class="inputForm" style="width: 100%;"></td>
		<td><input type="text" name="horaChegadaDestino" maxlength="8" class="inputForm"></td>
		<td><input type="text" name="horaLiberacaoDestino" maxlength="8" class="inputForm" onkeypress="return validaCampo(this, 8, jsValidHora);" ></td>
		<td colspan="2"><input type="text" name="pacienteRecebidoPor" maxlength="100" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 100, jsValidCharsWithNumbersUp);" ></td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td colspan="2">Fun&ccedil;&atilde;o:</td>
		<td colspan="3">Intercorr&ecirc;ncias na recep&ccedil;&atilde;o/transporte:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><input type="text" name="funcao" maxlength="255"  class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
		<td colspan="3"><input type="text" name="intercorrencias" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td colspan="2">Pertences entregues:</td>
		<td colspan="2">Hora Chegada na Base ou QRV:</td>
		<td width="8%">Km Final</td>
	</tr>
	<tr>
		<td >&nbsp;</td>
		<td colspan="2"><input type="text" name="pertencesEntregues" maxlength="255"  class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
		<td colspan="2"><input type="text" name="horaChegadaBase" maxlength="8"  class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" ></td>
		<td width="8%" ><input type="text" name="kmFinal" maxlength="10"  class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 10, jsValidNumbers);" ></td>
	</tr>
	<tr>
		<td colspan=4>&nbsp;</td>
	</tr>
</table>
<br>
<center>
	<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/salvar_on.jpg" />
</center>
