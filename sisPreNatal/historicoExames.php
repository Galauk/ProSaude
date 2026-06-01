<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td colspan = "2" class="txFieldDestaqueMenor_Hiperdia">ULTRA-SONOGRAFIA</td>
		<td class="txFieldDestaqueMenor_Hiperdia" colspan="5"></td>
	</tr>
  <tr>
	<td width="13%">Data<br>
		<input name="dataUltra" onKeyPress="" type="text" size="12" maxlength="10" value='' class="inputForm" readOnly>
	</td>
	<td width="12%">
		IG (DUM)<br>
	    <input name="dum"  type="text" value="" size="8" maxlength="7" class="inputForm" >
	</td>
	<td width="12%">
		IG (USG)<br>
		<input name="usg"  type="text" value='' size="8" maxlength="7" class="inputForm"  >
	</td>
	<td width="12%">
		Apresenta&ccedil;&atilde;o<br>
		<select name="apresentacaoUltra" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="P">Pelvica</option>
			<option value="C">Cefalica</option>
			<option value="D">Dorsal</option>
		</select>
	</td>
	<td width="12%">
		Peso Fetal(gr)<br>
		<input name="pesoFetal" type="text" size="9" maxlength="10" value='' class="inputForm">
	</td>
	<td width="20%">
		Placenta<br>
		<input name="placenta" type="text" size="20" maxlength="20"  value='' class="inputForm">
	</td>
	<td width="20%">
		L&iacute;quido Amni&oacute;tico<br>
	<input name="liquido" type="text" size="20" maxlength="20"  value='' class="inputForm">
	</td>
  </tr>
  <tr>
    <td colspan="7">
	Outros Dados<br>
		<textarea name="outrosDados" rows="2" cols="133" class="txArea" >
        
        </textarea>
	</td>
  </tr>
</table>
<hr>
<center>

<DIV id="tabelaUltra" style="display:none; z-index:1;"></DIV>

</center>
<hr>
<table border="1" cellpadding=0 cellspacing=0 width="100%" class="b5">
	<tr>
		<td width="49%">
			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
				<tr>
					<td width="100%" colspan="2" class="txFieldDestaqueMenor_Hiperdia"> <br>
						CARDIOTOCOGRAFIA <br><br>
					</td>
				</tr>
				<tr>
					<td width="30%">
						Data<br>
						<input name="dataCardio" type="text" size="12" maxlength="10" value='' class="inputForm" readOnly>
					</td>
					<td width="70%" align="left">
						Resultado<br>
						<input name="resultadoCardio" type="text" size="35" maxlength="30" value=''>
					</td>
				</tr>
			</table>
			<div id="tabelaCardio" style="overflow: auto; height: 70px; width: 375px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF" >

			</div>
		</td>
		<td width="2%">&nbsp;
			
		</td>
		<td width="49%">
			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
				<tr>
					<td width="100%" colspan="2" class="txFieldDestaqueMenor_Hiperdia"><br>
						DOPPLERFLUXOMETRIA <br><br>
					</td>
				</tr>
				<tr>
					<td width="30%">
						Data<br>
						<input name="dataDoppler" type="text" size="12" maxlength="10" value='' class="inputForm" readOnly>
					</td>
					<td width="70%" align="left">
						Resultado<br>
						<input name="resultadoDoppler" type="text" size="35" maxlength="30" value=''>
					</td>
				</tr>
			</table><br>
			<div id="tabelaDoppler" style="overflow: auto; height: 70px; width: 375px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF" >

			</div>
		</td>
	</tr>
</table>