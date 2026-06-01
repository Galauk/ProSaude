<%=com.banksystem.util.TabCreator.tabHead("Dados Gerais",3,0)%>

	<%@include file="comboMunicipioUnidadeSecaoEspaco.jsp"%>

	<tr>
   	<td width="2%">&nbsp;</td>
    <td width=12%>
      C&oacute;digo da Notifica&ccedil;&atilde;o
    </td>
    <td width="*" colspan="4">
      <input type="text" name="idNotificacao" size="9" value="" class="txReadOnly" readonly>
   </td>
  </tr>


	<tr>
 		<td width="2%">&nbsp;</td>
    	<td width="12%">
			Data da Notifica&ccedil;&atilde;o
		</td>
		<td width="*" colspan="4">
			<input type="text" name="dtNotificacao" size="12" maxlength="10" value="" class="txOthers" onKeyPress="return validaCampo(event, this, 10, '0123456789/');">
			<a href="javascript:void(0);" onclick="javascript:show_calendar('formAcidenteTrabalho.dtNotificacao');"><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calend&aacute;rio' align="absmiddle"></a>
    	</td>
	</tr>

  <tr>
  	<td width="2%">&nbsp;</td>
	<td width="12%">
    	Nome
	</td>
	<td width="*" colspan="4">
		<input name="cdCidadao" type="hidden" value='<%= beanVigilanciaAcidenteTrabalho.getCdCidadao() %>'>
	     <input type="text" name="nmCidadao" size="60" value="" class="txReadOnly" readonly>&nbsp;
    	<a href="javaScript:findCidadaos()" title="Pesquisar Cidadao"><img border="0" src="../images/pesquisa.gif" ></a>
    </td>
  </tr>



  <tr>
   	<td width="2%">&nbsp;</td>
    <td width=12%>
      Sexo
    </td>
    <td width="20%">
      <input type="text" name="sexo" size="18" value="" class="txReadOnly" readonly>
    </td>

  	<td width="2%">&nbsp;</td>
    <td width="12%">
      Dt. Nascimento
    </td>
    <td width="*%">
     <input type="text" name="dtNascimento" size="18" value="" class="txReadOnly" readonly>
    </td>
  </tr>



   <tr>
   	<td width="2%">&nbsp;</td>
    <td width=12%>
      CNS
    </td>
    <td width="20%">
      <input type="text" name="nrCns" size="18" value="" class="txReadOnly" readonly>
    </td>

  	<td width="2%">&nbsp;</td>
    <td width="12%">
      CPF
    </td>
    <td width="*%">
     <input type="text" name="nrCpf" size="18" value="" class="txReadOnly" readonly>
    </td>
  </tr>



  <tr>
   	<td width="2%">&nbsp;</td>
    <td width=12%>
      Nome da M&atilde;e
    </td>
    <td width="*" colspan="4">
      <input type="text" name="nmMae" size="60" value="" class="txReadOnly" readonly>
    </td>

  </tr>



  <tr>
	<td>&nbsp;</td>
	<td> Estabelecimento: </td>
	<td width="*" colspan="4">
		<input	name="cdVigilanciaInfo" type="hidden" value="-1" size="6" maxlength="6" class="txReadOnly" readonly>
		<input 	name="razaoSocial" type="text" value=""	size="60" maxlength="1000" class="txReadOnly" readonly>&nbsp;
		<a  	name="pesqEstab" href="javascript:void(0);"
		Onclick="javascript:findEstabelecimento1();"
		title="Pesquisa Estabeleciamento/Profissional"><img border="0" src="../images/pesquisa.gif">
		</a>
	</td>
  </tr>



  <tr>
	<td width="2%">&nbsp;</td>
	<td width="12%"> Cargo / Fun&ccedil;&atilde;o: </td>
    <td width="*" colspan="4">
	<input name="dsCargo" type="text" value="" size="40" maxlength="50" class="txNormal" onKeyPress="return validaCampo(event, this, 500, jsValidCharsObsText);">
	</td>
  </tr>



 <tr>
	<td width="2%">&nbsp;</td>
	<td width="12%"> Local (Depto / Andar): </td>
    <td width="*" colspan="4">
	<input name="dsDepto" type="text" value="" size="40" maxlength="50" class="txNormal" onKeyPress="return validaCampo(event, this, 500, jsValidCharsObsText);">
	</td>
  </tr>



  <tr>
	<td width="2%">&nbsp;</td>
	<td width="12%"> Hor&aacute;rio de Trabalho: </td>
    <td width="*" colspan="4">
		<%=beanVigilanciaAcidenteTrabalho.getComboHorarioTrabalho()%>
	</td>
  </tr>



	<tr>
 		<td width="2%">&nbsp;</td>
    	<td width="12%">
			Data da Admiss&atilde;o
		</td>
		<td width="*">
			<input type="text" name="dtAdmissao" size="12" value="" class="txOthers" onKeyPress="return validaCampo(event, this, 10, '0123456789/');">
			<a href="javascript:void(0);" onclick="javascript:show_calendar('formAcidenteTrabalho.dtAdmissao');"><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calend&aacute;rio' align="absmiddle"></a>
    	</td>

		<td width="2%">&nbsp;</td>
		<td width="12%">
		 	Tempo na fun&ccedil;&atilde;o (meses):
		 </td>
    	<td width="*" colspan="4">
			<input name="nrTempoFuncao" type="text" value="" size="5" maxlength="3" class="txNormal" onKeyPress="return validaCampo(event, this, 500, '0123456789');">
		</td>

	</tr>



  <tr>
	<td width="2%">&nbsp;</td>
	<td width="12%"> Vínculo: </td>
    <td width="*" colspan="4">
		<%=beanVigilanciaAcidenteTrabalho.getComboVinculo()%>
	</td>
  </tr>



  <tr>
	<td>&nbsp;</td>
	<td> Empresa (Se for terceirizado): </td>
	<td width="*" colspan="4">
		<input	name="cdTerceirizada" type="hidden" value="-1" size="6" maxlength="6" class="txReadOnly" readonly>
		<input 	name="razaoSocialTerceirizada" type="text" value=""	size="60" maxlength="1000" class="txReadOnly" readonly>&nbsp;
		<a  	name="pesqEstabTerceirizada" href="javascript:void(0);"
		Onclick="javascript:findEstabelecimento2();"
		title="Pesquisa Estabeleciamento/Profissional"><img border="0" src="../images/pesquisa.gif">
		</a>
	</td>
  </tr>

<%=com.banksystem.util.TabCreator.tabTail("trash",1)%>


