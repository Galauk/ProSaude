<table border="0" cellpadding=0 cellspacing=0 width="100%">
  <tr>
	<td class="txFieldDestaqueMenor_Hiperdia"  colspan="5">CONSULTA PUERPERAL</td>
  </tr>
  <tr>
    <td width="20%"> Data da Consulta  <br>
		<input name="dataPuerperal" type="text" size="10" maxlength="10" value='' class="inputForm" readOnly="readonly">
    </td>
	<td width="27%"> Risco Reprodutivo <br>
		<select name="tipoRisco" class="inputForm">
			<option value="X" selected="selected">Selecione o Tipo do Risco</option>
			<option value="I">Identificado</option>
			<option value="N">N&atilde;o Identificado</option>
			<option value="A">N&atilde;o Avaliado</option>
		</select>
	</td>
	<td width="30%"> Vincula&ccedil;&atilde;o Planejamento familiar <br>
		<select name="tipoVinculacao" class="inputForm">
			<option value="X" selected="selected">Selecione o Tipo do Programa</option>
			<option value="R">Como Rotina</option>
			<option value="P">Como Prioridade</option>		
		</select>
	</td>
	<td width="12%">
		Peso(kg) <br>
		<input name="pesoMae" type="text" size="7" maxlength="6" value='' class="inputForm">
	</td>
	<td width="12%">
		Temperatura  <br>
		<input name="temperatura" type="text" size="7" maxlength="6" value='' class="inputForm">
    </td>
  </tr>
  <tr>
	<td width="20%">
		Press&atilde;o Arterial  <br>
		<input name="preArterial" type="text" size="10" maxlength="7" value='' class="inputForm">
    </td>
    <td width="57%" colspan="2">
		Caracter&iacute;sticas dos l&oacute;quios  <br>
		<input name="loquios" type="text" size="70" maxlength="60" value='' class="inputForm">
    </td>
	<td width="24%" colspan="2">
		<table border="0" width="100%">
			<tr>
				<td width="52%">
					<label class="chkBox"><input name="hemoCheck" type="checkbox"  value="N"> Hemorragia</label>
				</td>
				<td>
					<label class="chkBox"><input name="infecCheck" type="checkbox"  value="N"> Infec&ccedil;&atilde;o</label>
				</td>
			</tr>
		</table>
    </td>
  </tr>
  <tr>
    <td width="20%" colspan="1">Amamenta&ccedil;&atilde;o  <br>
	<select name="amamentacao" style="width:75px;" class="inputForm">
			<option value="S">Sim</option>
			<option value="N">Nao</option>
		</select>
    </td>
    <td width="40%" colspan="4">Motivo  <br>
		<input name="motivo" type="text" size="50" maxlength="60" value='' class="inputForm">
    </td>
    </tr>
    <tr>
    <td colspan ="2">Diagn&oacute;stico
		<br> <textarea name="anomalia" rows="2" cols="70" class="textArea"></textarea>
		<input name="btnPesquisar" type="button" style="border: 1px solid #808080; background-color: #FFFFFF; width:110; height:40" title="Pesquisar" value='Pesquisar CID'>
	</td>
  </tr>
</table>

<center>
<div id = "tabelaPuerperal" style="display:none; overflow: auto; height: 70px; width:770px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF" >

</div>
</center>


