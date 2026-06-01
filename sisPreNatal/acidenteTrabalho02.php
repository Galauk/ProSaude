<%=com.banksystem.util.TabCreator.tabHead("Dados do Acidente (1)",3,0)%>
	
	<tr> 
 		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Data do Acidente
			&nbsp;&nbsp;
			<input type="text" name="dtAcidente" maxlength="10" size="12" value="" class="txOthers" onChange="javascript:dtAcidenteChange();" onBlur="javascript:dtAcidenteChange();" onKeyPress="return validaCampo(event, this, 10, '0123456789/');" >
			<a href="javascript:void(0);" onBlur="javascript:dtAcidenteChange();" onclick="javascript:show_calendar('formAcidenteTrabalho.dtAcidente');"><img src="../images/calendar.gif" width="34" height="21" border="no" title='Calend&aacute;rio' align="absmiddle"></a>	    	        								
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <input type="text" name="diaSemana" size="20" value="" class="txReadOnly" readonly>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Hora do Acidente
			&nbsp;&nbsp;
			<input type="text" name="hrAcidente" size="9" maxlength="8" value="" class="txOthers"  onKeyPress="return validaCampo(event, this, 8, '0123456789:');">
    	</td>
	</tr>		



	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Tempo que trabalha com o agente espec&iacute;fico do acidente: 
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboTempoTrabalha()%>
		</td>
	</tr>	
	

	
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Acidente:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboTipoAcidente()%>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Les&atilde;o: 
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboLesao()%>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&Oacute;bito:    
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboObito()%>
		</td>
	</tr>	


		
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Causado por agentes qu&iacute;micos, f&iacute;sicos ou biol&oacute;gicos:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboCausaAgentes()%>
			&nbsp;&nbsp;&nbsp;&nbsp;
			Quais:
		</td>
	</tr>	

	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			<%=beanVigilanciaAcidenteTrabalho.getCheckBoxAgente()%>
		</td>
	</tr>	


	
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Acidente devido a causas externas:   
			&nbsp;&nbsp;  	
			<%=beanVigilanciaAcidenteTrabalho.getComboCausaExterna()%>
			&nbsp;&nbsp;&nbsp;&nbsp;  	
			Quais:
		</td>
	</tr>	
	
	<tr> 
		<td width="2%">&nbsp;</td>
    	<td colspan="2">
			<%=beanVigilanciaAcidenteTrabalho.getCheckBoxCausaExterna()%>
		</td>
	</tr>		


	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Local do acidente:
			&nbsp;&nbsp;  	
			<%=beanVigilanciaAcidenteTrabalho.getComboLocal()%>
		</td>
	</tr>			

		
	<tr> 
 		<td width="2%">&nbsp;</td>
		<td width="12%">
			Descri&ccedil;&atilde;o do acidente: 
		</td>
		<td width="*">	
			<textarea name="dsAcidente" rows="6" cols="80" class="txArea" onKeyPress="return validaCampo(event, this, 500, jsValidCharsObsText);"></textarea>
		</td>
	</tr>	
	


<%=com.banksystem.util.TabCreator.tabTail("trash",1)%>