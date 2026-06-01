<?php
	session_start(); 
?>
<!--  ########################## DADOS REFERENCIA HOSPITALAR ####################################  -->	
<center> 
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="100%" align="center">
		    <p class="txFieldDestaque">
				DADOS DE REF&Ecirc;RENCIA HOSPITALAR:
			</p>
		</td>
	</tr>
</table>
</center>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td>
			<table align="center" border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td width="1%">&nbsp;</td>
					<td width="7%">&nbsp;</td>
					<td width="20%" style="border-bottom: 1px solid #000; border-left: 1px solid #000; ">&nbsp;Hora</td>
					<td width="20%" style="border-bottom: 1px solid #000; border-left: 1px solid #000;">&nbsp;Servi&ccedil;o</td>
					<td width="20%" style="border-bottom: 1px solid #000; border-left: 1px solid #000;">&nbsp;Prof. Contato</td>
					<td width="30%" style="border-bottom: 1px solid #000; border-left: 1px solid #000;">&nbsp;Motivo da Recusa</td>
					<td width="2%" style = "border-left: 1px solid #000;">&nbsp;</td>
				</tr>
				<tr>
					<td width="1%">&nbsp;</td>				
					<td>1&ordm; REF:</td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="horaRefHosp1" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="servicoRefHosp1" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="contatoRefHosp1" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="motivoRecusaRefHosp1" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td width="2%" style = "border-left: 1px solid #000;">&nbsp;</td>					
				</tr>
				<tr>
					<td width="1%">&nbsp;</td>				
					<td>2&ordm; REF:</td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="horaRefHosp2" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="servicoRefHosp2" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="contatoRefHosp2" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="motivoRecusaRefHosp2" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td width="2%" style = "border-left: 1px solid #000;">&nbsp;</td>					
				</tr>
				<tr>
					<td width="1%">&nbsp;</td>				
					<td>3&ordm; REF:</td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="horaRefHosp3" maxlength="5" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 8, jsValidHora);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="servicoRefHosp3" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="contatoRefHosp3" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td style = "border-left: 1px solid #000;"><input type="text" name="motivoRecusaRefHosp3" maxlength="255" class="inputForm" style="width: 100%;" onkeypress="return validaCampo(this, 255, jsValidCharsWithNumbersUp);" ></td>
					<td width="2%" style = "border-left: 1px solid #000;">&nbsp;</td>					
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<center>
	<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/continuar_cadastro.png" />
</center>