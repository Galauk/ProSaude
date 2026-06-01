<table border="0" cellpadding=0 cellspacing=0 width="100%">
  <tr>
	<td class="txFieldDestaqueMenor_Hiperdia">INFORMA&Ccedil;&Otilde;ES</td>
	<td colspan="3" align="right"></td>
  </tr>
  <tr>
   <td> Dt. Cadastro <br>
	<input name="dataItem" type="text" size="10" maxlength="10"  value='' class="inputForm">
	<a href="javascript:void(0);" ><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calendario'></a>
  </td>
    <td> Dt. &Uacute;ltima Menstrua&ccedil;&atilde;o <br>
	<input name="dataUltMenstru" type="text" size="10" maxlength="10" class="inputForm">&nbsp;
	<a href="javascript:void(0);" ><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calendario'></a>
    </td>

    <td colspan="2"> Dt. Prov&aacute;vel do Parto <br>
	<input name="dataProvParto"  type="text" size="10" maxlength="10" class="inputForm" readOnly>
    </td>

	<td> Dias de Gesta&ccedil;&atilde;o <br>
	<input name="diasMenstruacao" type="text" size="10" maxlength="10" class="inputForm" readOnly>
    </td>
	<input name="diasMenstruacao" type="hidden">
  </tr>

  <tr>
    <td> Dt. da 1&ordf; Consulta de Pr&eacute;-Natal <br>
	<input name="dataPreNatal"  type="text" size="10" maxlength="10"  class="inputForm">
	<a href="javascript:void(0);" ><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calend�rio'></a>
    </td>

	<!-- Nome do campo = codRespConsulta -->

    <td> Respons&aacute;vel pela 1&ordf; Consulta de Pr&eacute;-Natal <br>
		
    </td>

    <td colspan = "2"> Atividade Profissional do Respons&aacute;vel <br>
		<select name="codAtividade" class="inputForm">
			<option value="00" selected>--&gt; Selecione Atividade Profissional &lt;--</option>
			<option value="01">Enfermeiro</option>
			<option value="02">Ginecologia</option>
			<option value="29">Obstetr&iacute;cia</option>
			<option value="59">M&eacute;dico do PSF</option>
			<option value="60">Enfermeiro do PSF</option>
			<option value="73">Ginecologia/Obstetr&iacute;cia</option>
			<option value="74">Medicina Geral Comunit&aacute;ria</option>
			<option value="76">Enfermeiro do PACS</option>
			<option value="74">Enfermeiro Obstetra</option>
			<option value="76">M&eacute;dico (qualquer especialista)</option>
		</select>
    </td>
  </tr>
  <tr>
   <td > N&ordm; (SISPRENATAL)<br>
        <input name="numSisPreNatal"  type="text" value="" size="10" maxlength="15" class="inputForm" readOnly >
	  </td>
	  <td colspan="2"> Maternidade de Refer&ecirc;ncia <br>
		<input name="maternidade"  type="text"  size="40" maxlength="40" class="inputForm">
	  </td>
	  </tr>
	    <tr>
 		   <td class="txFieldDestaqueMenor_Hiperdia" colspan="3">
				RESPONS&Aacute;VEIS PELA GESTANTE
		   </td>
	   </tr>
       <tr>
	      <td width="33%"> M&eacute;dico Respons&aacute;vel <br>
				<input name="cdMedico"  type="text" value="" size="5" maxlength="8" class="inputForm" readonly >
				<input name="nmMedico"  type="text" value="" size="20" maxlength="40" class="inputForm" readonly >
				<a href="" title="Pesquisa de Medico"><img border="0" src="../imgs/buscar4_on.jpg" ></a>
		  </td>

	      <td width="33%"> Enfermeiro Respons&aacute;vel <br>
				<input name="cdEnfermeiro"  type="text" value="" size="5" maxlength="8" class="inputForm" readonly >
				<input name="nmEnfermeiro"  type="text" value="" size="20" maxlength="40" class="inputForm" readonly >
				<a href="" title="Pesquisa de Enfermeiro"><img border="0" src="../imgs/buscar4_on.jpg" ></a>
		  </td>

	      <td width="34%"> Outros Profissionais <br>

				<input name="cdMedicoParticular"  type="text" value="" size="5" maxlength="8" class="inputForm" readonly >
				<input name="nmMedicoParticular"  type="text" value="" size="20" maxlength="40" class="inputForm" onKeyPress="">
				<a href="#" title="Pesquisa de Medico"><img border="0" src="../imgs/buscar4_on.jpg" ></a>
			</td>
		</tr>
</table>



<hr>
<table border="0" cellpadding=0 cellspacing=0 width="100%">
	<tr>
		<td width="50%" valign="top">
			<table border="0" cellpadding=0 cellspacing=0 width="100%">
				<tr>
					<td width="100%" class="txFieldDestaqueMenor_Hiperdia" colspan="2">
						PARTO
					</td>
				</tr>
				<tr>
					<td width="50%"> Data <br>
						<input name="dataParto" onKeyPress="" type="text" size="10" maxlength="10" value='' class="inputForm" onBlur="">
						<div id="divDtParto" style="display: inline;">
							<a href="#" ><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calendario'></a>
						</div>
					</td>
					<td width="50%"> Hora <br>
						<input name="horaParto" onKeyPress="" type="text" size="10" maxlength="10" value='' class="inputForm">
					</td>
				</tr>
				<tr>
					<td width="50%"> N&iacute;vel de Risco <br>
						<select name="risco" class="inputForm">
							<option value="X" selected>--&gt; Selecione Nivel Risco &lt;--</option>
							<option value="B">Baixo</option>
							<option value="M">Medio</option>
							<option value="A">Alto</option>
						</select>
					</td>
					<td colspan="2" width="50%"> Tipo <br>
							<select name="tipoParto" class="inputForm">
								<option value="X" selected>--&gt; Selecione Tipo Parto &lt;--</option>
								<option value="N">Normal</option>
								<option value="C">Cesareana</option>
							</select>
					</td>
				</tr>
			</table>
		</td>
		<td width="50%" valign="top">
			<table border="0" cellpadding=0 cellspacing=0 width="100%">
				<tr>
					<td width="100%" class="txFieldDestaqueMenor_Hiperdia" colspan="2">
						INTERRUP&Ccedil;&Atilde;O
					</td>
				</tr>
				<tr>
					<td width="100%"> Data <br>
						<input name="dataInterrupcao"  type="text" size="10" maxlength="10"  class="inputForm" onBlur="">
						<div id="divDtInterrupcao" style="display: inline;">
							<a href="#"><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calendario'></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="100%"> Indica&ccedil;&atilde;o <br>
						<input name="indicacao" type="text" size="50" maxlength="60" class="inputForm"  onKeyPress="">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<center>
					<div id="tabelaItem" style="overflow: auto; height: 150px; width:700px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF">
				</center>
			</td>
		</tr>
</table>

