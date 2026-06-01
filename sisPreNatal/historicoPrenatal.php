<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
  <tr>
	<td>Data <br>
		<input name="data" onKeyPress="return validaCampo(event, this, 10, '0123456789/');" type="text" size="12" maxlength="10" value='' class="inputForm" readOnly>
	</td>
	<td>Id. Gestacional(Semanas) <br>
	    <input name="idadeGest"  type="text" value="" size="10" maxlength="3" class="inputForm"  onKeyPress="return validaCampo(event, this, 3, '0123456789');">
	</td>
    <td>Peso(kg)<br>
		<input name="pesoGest" onKeyPress="return validaCampo(event, this, 6, '0123456789,.');" type="text" size="12" maxlength="6" value='' class="inputForm" onChange="">
	</td>
    <td >Estatura(m)  <br>
		<input name="alturaGest" onKeyPress="return validaCampo(event, this, 6, '0123456789,.');" type="text" size="12" maxlength="6" value='' class="inputForm" onChange="">
	</td>
	</tr>
	<tr>
    <td>Massa  <br>
		<input name="massa" onKeyPress="return validaCampo(event, this, 6, '0123456789,.');" type="text" size="12" maxlength="6" value='' class="inputForm" readonly>
	</td>

    <td >Altura Uterina(cm)  <br>
		<input name="barriga" onKeyPress="return validaCampo(event, this, 6, '0123456789,.');" type="text" size="12" maxlength="6" value='' class="inputForm">
	</td>
    <td>Batimento  <br>
		<input name="batimento" onKeyPress="" type="text" size="12" maxlength="6" value='' class="inputForm">
	</td>
    <td>Edema <br>
		<select name="edema" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="S">Sim</option>
			<option value="N">N&atilde;o</option>
		</select>
	</td>
	</tr>
	<tr>
    <td>Apresenta&ccedil;&atilde;o <br>
		<select name="apresentacao" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="P">P&eacute;lvica</option>
			<option value="C">Cef&aacute;lica</option>
			<option value="D">Dorsal</option>
		</select>
	</td>
    <td>Tipo <br>
		<select name="tipoHistorico" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="C">Consulta</option>
			<option value="R">Reconsulta</option>
		</select>
	</td>
	<td>
	Press&atilde;o Arterial  <br>
	<input name="preArterialHist" type="text" size="12" maxlength="7" value='' class="inputForm" onChange="javascript:calculaPressao();">
	</td>
		<td>Press&atilde;o  <br>
		<input name="resultPressao"  type="text" size="10" maxlength="10" value='' class="inputForm" readonly>
	</td>
  </tr>

  <tr>
   <td>IGM:<br>
		<select name="igm" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="P">Positivo</option>
			<option value="N">Negativo</option>
		</select>
	</td>

  <td>Hora(h:m)<br>
  <input name="horaAgendaHist" type="text" value='' size="8" maxlength="8" class="inputForm">
	</td>

   <td colspan="2">Atendimento <br>
		<select name="atendHist" class="inputForm">
			<option value="I">--Selecione--</option>
			<option value="E">Em aberto</option>
			<option value="N">N&atilde;o compareceu</option>
			<option value="A">Atendido</option>
		</select>
	</td>
  </tr>
    <tr>
		  <td colspan="3"> M&eacute;dico Respons&aacute;vel <br>
				<input name="cdmedicohist"  type="text" value="" size="5" maxlength="5" class="inputForm" readonly >
				<input name="nmmedicohist"  type="text" value="" size="20" maxlength="40" class="inputForm" readonly >
				<a href="#" title="Pesquisa por Medico"><img border="0" src="../imgs/buscar4_on.jpg" ></a>
		  </td>
	</tr>
	<tr>
	<td colspan="3">
		Observa&ccedil;&otilde;es <br>
		<textarea name="observacao" rows="2" cols="120" class="txArea"></textarea>
	</td>
  </tr>
</table>
<center>
	<input type="button" value="Tabela/Grafico Alt. Uterina" name="btnCrescimento" onClick='' title="Evolucao Altura Uterina." style="border: 1px solid #808080; background-color: #FFFFFF; width:170; height:20">
	<input type="button" value="Tabela/Grafico IMC" name="btnPCefalico" onClick='' title="Evolucao IMC." style="border: 1px solid #808080; background-color: #FFFFFF; width:170; height:20">
</center>
<br>
<hr>
<!-- <div id="" style="display:none; z-index:1;"></div> -->
<div id="tabelaHistorico" style="overflow: auto; height: 110px; width:580px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF"></div>
</center>
